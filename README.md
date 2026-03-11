# Auto Font Loader

Upload de famílias de fontes via ZIP com suporte ao Elementor.

## 📖 Descrição

O **Auto Font Loader** é um plugin para WordPress que permite o upload de famílias de fontes completas via arquivo ZIP. As fontes são registradas automaticamente e ficam disponíveis para uso no tema e, especialmente, no Elementor (aparecem no grupo **Custom Fonts**). O plugin detecta automaticamente o peso (`font-weight`) e o estilo (`italic`/`normal`) com base no nome de cada arquivo.

## ✨ Funcionalidades

- Upload de famílias de fontes via arquivo ZIP (formatos suportados: `.woff2`, `.woff`, `.ttf`, `.otf`).
- Extração automática e organização dos arquivos na pasta `/wp-content/uploads/auto-fonts/`.
- Registro de cada variação da fonte com as propriedades CSS adequadas (`@font-face`).
- Integração com Elementor – as fontes personalizadas aparecem no seletor de fontes.
- Detecção inteligente de peso e estilo baseada no nome do arquivo.
- Geração de CSS inline com `font-display: swap` para melhor performance.

## 🔧 Requisitos

- WordPress 5.0 ou superior.
- PHP 7.2 ou superior.
- (Opcional) Elementor instalado e ativo para integração total.

## 🚀 Instalação

1. Faça o download do plugin e envie via painel WordPress (**Plugins → Adicionar novo → Enviar plugin**) ou via FTP para a pasta `/wp-content/plugins/auto-font-loader/`.
2. Ative o plugin no menu **Plugins**.

## 📤 Como usar

### 1. Upload de uma família de fontes

- No menu administrativo, clique em **Auto Fonts**.
- Preencha o campo **Font Name** com o nome da família (ex: "MinhaFonte").
- Selecione o arquivo ZIP que contém os arquivos de fonte.
- Clique em **Upload**.

O plugin irá:
- Criar uma subpasta dentro de `/wp-content/uploads/auto-fonts/` com o nome informado.
- Extrair todos os arquivos de fonte para essa pasta.
- Registrar cada variação no banco de dados (opção `auto_fonts`).

### 2. Como o plugin detecta peso e estilo

O plugin analisa o **nome do arquivo** (convertido para minúsculas) para determinar as propriedades CSS.

#### 🔹 Peso (`font-weight`)

| Palavra-chave no nome | Peso atribuído |
|-----------------------|----------------|
| `thin`                | 100            |
| `extralight`          | 200            |
| `light`               | 300            |
| `regular`             | 400            |
| `medium`              | 500            |
| `semibold`            | 600            |
| `bold`                | 700            |
| `extrabold`           | 800            |
| `black`               | 900            |

**Exemplo:**  
Arquivo `Inter-BoldItalic.woff2` → contém "bold" → peso **700**.

Caso nenhuma palavra seja encontrada, o peso padrão é **400** (regular).

#### 🔹 Estilo (`font-style`)

- Se o nome do arquivo contiver a palavra `italic`, o estilo será `italic`.
- Caso contrário, será `normal`.

**Exemplo:**  
`Inter-LightItalic.woff2` → contém "italic" → estilo **italic**.  
`Inter-Regular.woff2` → sem "italic" → estilo **normal**.

### 3. Usando as fontes no Elementor

Após o upload, as fontes estarão disponíveis no Elementor:

- Ao editar uma página com Elementor, selecione um elemento de texto.
- No painel de estilos, em **Tipografia → Família**, procure pelo nome da sua fonte.
- Elas aparecerão agrupadas na categoria **Custom Fonts**.

## ❌ Removendo fontes instaladas

Se desejar excluir completamente uma fonte, siga estes passos:

1. **Apagar os arquivos**  
   - Acesse a pasta de uploads: `/wp-content/uploads/auto-fonts/`.
   - Exclua a subpasta com o nome da fonte (ex: `/wp-content/uploads/auto-fonts/VinilaCompressed/`).

2. **Limpar o registro no banco de dados**  
   - A opção `auto_fonts` na tabela `wp_options` armazena todas as fontes registradas.
   - Utilize o **phpMyAdmin** ou um plugin de gerenciamento de banco de dados para localizar a linha com `option_name = 'auto_fonts'` e excluí-la.

3. **Regenerar o CSS do Elementor** (se aplicável)  
   - No painel WordPress, vá em **Elementor → Tools**.
   - Clique no botão **Regenerate CSS & Data**.

## 📄 Changelog

### 1.0
- Versão inicial do plugin.
- Upload de ZIP com suporte a múltiplos formatos.
- Detecção automática de peso e estilo.
- Integração básica com Elementor.

## 📝 Licença

Este plugin é licenciado sob a **GPL v2 ou posterior**.  
Você pode usar, modificar e redistribuir livremente, desde que mantenha os créditos e a mesma licença.