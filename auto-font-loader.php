<?php
/**
 * Plugin Name: Auto Font Loader
 * Description: Upload de famílias de fontes via ZIP com suporte ao Elementor.
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;


/*
---------------------------------------
CRIA GRUPO DE FONTES NO ELEMENTOR
---------------------------------------
*/

add_filter('elementor/fonts/groups', function($groups){

    $groups['custom_fonts'] = 'Custom Fonts';

    return $groups;

});


class Auto_Font_Loader {

    private $font_dir;

    public function __construct() {

        $upload = wp_upload_dir();
        $this->font_dir = $upload['basedir'].'/auto-fonts';

        add_action('admin_menu', [$this,'menu']);
        add_action('admin_post_upload_font_zip', [$this,'upload_zip']);
        add_action('wp_enqueue_scripts', [$this,'register_fonts']);

        add_action('elementor/init', function(){
            add_filter('elementor/fonts/additional_fonts', [$this,'elementor_fonts']);
        });

        $this->create_folder();

    }


    private function create_folder(){

        if(!file_exists($this->font_dir)){
            wp_mkdir_p($this->font_dir);
        }

    }


    /*
    ---------------------------------------
    MENU ADMIN
    ---------------------------------------
    */

    public function menu(){

        add_menu_page(
            'Auto Fonts',
            'Auto Fonts',
            'manage_options',
            'auto-fonts',
            [$this,'page']
        );

    }


    public function page(){
        ?>

        <div class="wrap">
            <h1>Upload Font Family (ZIP)</h1>

            <form method="post" enctype="multipart/form-data"
            action="<?php echo admin_url('admin-post.php'); ?>">

            <input type="hidden" name="action" value="upload_font_zip">

            <table class="form-table">

                <tr>
                    <th>Font Name</th>
                    <td>
                        <input type="text" name="font_name" required>
                    </td>
                </tr>

                <tr>
                    <th>ZIP File</th>
                    <td>
                        <input type="file" name="zip_file" accept=".zip" required>
                    </td>
                </tr>

            </table>

            <?php submit_button('Upload'); ?>

            </form>

        </div>

        <?php
    }



    /*
    ---------------------------------------
    UPLOAD ZIP
    ---------------------------------------
    */

    public function upload_zip(){

        if(!current_user_can('manage_options')){
            wp_die('No permission');
        }

        $font_name = sanitize_text_field($_POST['font_name']);
        $zip = $_FILES['zip_file'];

        $tmp = $zip['tmp_name'];

        $extract = $this->font_dir.'/'.$font_name;

        wp_mkdir_p($extract);

        $zipArchive = new ZipArchive;

        if($zipArchive->open($tmp) === TRUE){

            $zipArchive->extractTo($extract);
            $zipArchive->close();

        }

        $files = scandir($extract);

        $fonts = get_option('auto_fonts',[]);

        foreach($files as $file){

            if(!preg_match('/\.(woff2|woff|ttf|otf)$/i', $file)) continue;

            $weight = $this->detect_weight($file);

            $style = strpos(strtolower($file),'italic') !== false ? 'italic' : 'normal';

            $fonts[] = [
                'family' => $font_name,
                'file' => $font_name.'/'.$file,
                'weight' => $weight,
                'style' => $style
            ];

        }

        update_option('auto_fonts',$fonts);

        wp_redirect(admin_url('admin.php?page=auto-fonts'));
        exit;

    }



    /*
    ---------------------------------------
    DETECTA PESO DA FONTE
    ---------------------------------------
    */

    private function detect_weight($filename){

        $name = strtolower($filename);

        $weights = [
            'thin'=>100,
            'extralight'=>200,
            'light'=>300,
            'regular'=>400,
            'medium'=>500,
            'semibold'=>600,
            'bold'=>700,
            'extrabold'=>800,
            'black'=>900
        ];

        foreach($weights as $key=>$value){
            if(strpos($name,$key)!==false){
                return $value;
            }
        }

        return 400;

    }



    /*
    ---------------------------------------
    REGISTRA @FONT-FACE
    ---------------------------------------
    */

    public function register_fonts(){

        $fonts = get_option('auto_fonts',[]);
        if(!$fonts) return;

        $upload = wp_upload_dir();

        $css = "";

        foreach($fonts as $font){

            $url = $upload['baseurl'].'/auto-fonts/'.$font['file'];

            $css .= "

            @font-face{
                font-family:'{$font['family']}';
                src:url('{$url}');
                font-weight:{$font['weight']};
                font-style:{$font['style']};
                font-display:swap;
            }

            ";

        }

        wp_register_style('auto-fonts-style',false);
        wp_enqueue_style('auto-fonts-style');

        wp_add_inline_style('auto-fonts-style',$css);

    }



    /*
    ---------------------------------------
    ADICIONA FONTES AO ELEMENTOR
    ---------------------------------------
    */

    public function elementor_fonts($fonts){

        $saved = get_option('auto_fonts',[]);

        if(!$saved) return $fonts;

        $families = [];

        foreach($saved as $font){
            $families[$font['family']] = true;
        }

        foreach($families as $family => $v){
            $fonts[$family] = 'custom_fonts';
        }

        return $fonts;

    }

}

new Auto_Font_Loader();