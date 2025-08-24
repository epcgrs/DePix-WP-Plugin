# Depix WP Plugin

Plugin open‑source para integrar o fluxo de pagamentos DePix/Eulen ao WordPress.

## Visão geral
Este plugin adiciona ao WordPress:
- Painel de configurações para salvar o token da API (com criptografia AES‑256‑GCM)
- Criação de tabela de transações no banco (em `ativação`)
- Cliente/API para criar depósitos (Pix) e consultar status
- Webhook REST para receber atualizações assíncronas
- Shortcodes para uso imediato em páginas

## Requisitos
- WordPress 5.8 ou superior
- PHP 7.4 ou superior
- Extensão OpenSSL do PHP habilitada

## Como instalar (passo a passo)
1) Obtenha o plugin
   - Opção A: compacte a pasta do plugin e envie via WP Admin → Plugins → Adicionar novo → Enviar plugin.
   - Opção B: copie a pasta do plugin para `wp-content/plugins/` no seu servidor.
2) Ative o plugin em Plugins → Instalados.
3) No painel do WordPress, acesse o menu "DePix" (item de nível superior) → "Configurações".
4) Cole seu token de API no campo "API Token" e salve. O token é armazenado de forma cifrada usando as WP SALTs.
5) Opcional: informe e salve o "Webhook Secret" (também ficará cifrado).
6) Clique em "Fazer Ping na API" para validar conectividade/autenticação.

Observação: para obter seu token de API, entre em contato com o suporte da Eulen/DePix.

## Como usar com shortcode (tutorial)

### Shortcode principal: checkout completo
- Shortcode: `[depix_checkout]`
- O que faz: renderiza um fluxo de compra em múltiplos passos, incluindo valor, rede (Liquid), carteira e pagamento via Pix (com QR Code e Copia‑e‑cola), além de telas auxiliares (perfil/contato/FAQ).

Passos para usar:
1) Vá em Páginas → Adicionar nova.
2) Dê um título (ex.: "Comprar com Pix").
3) Insira o shortcode `[depix_checkout]` no conteúdo da página.
4) Publique a página e acesse a URL publicada.

Dicas:
- Ao detectar `[depix_checkout]`, o plugin aplica o template em `templates/depix-blank-template.php` para exibir somente o conteúdo do checkout (sem header/footer do tema), focando na conversão.
- Os assets do checkout são servidos diretamente do plugin em `assets/checkout/` (CSS/JS). É possível ajustar flags de ambiente e defaults em `assets/checkout/config.json` (opcional).

Funcionamento técnico (exato):
- O shortcode carrega `assets/checkout/main.css`, `assets/checkout/script.js` e `assets/checkout/search.js` e define `window.CFTheme` com:
  - `baseUrl`: `home_url('/')`
  - `configUrl`: URL de `assets/checkout/config.json`
- O JS inicia o Pix chamando via POST o endpoint do plugin: `BASE/api/pix/start` (JSON: `{ amountBRL, network, wallet }`).
- Status é acompanhado por polling em `BASE/api/pix/status?txId=<id>` a cada ~2s. Caso haja suporte a SSE no servidor (não implementado neste plugin), o JS tenta `/api/pix/stream/<id>` e cai no polling se falhar.
- Os endpoints `/api/pix/start` e `/api/pix/status` são registrados via rewrite pela camada de serviços do plugin (`src/services/index.php`). Se encontrar 404, vá em Configurações → Links permanentes e clique em "Salvar alterações" para atualizar as regras.
- O backend persiste transações em banco; o webhook (quando configurado) atualiza o status.

### Shortcode de teste: criar Pix simples
- Shortcode: `[depix_test]`
- O que faz: exibe um formulário simples para gerar um depósito (Pix) e mostra o QR Code/"Copia e cola". O status é acompanhado por AJAX (`admin-ajax.php?action=depix_tx_status&tx_id=...`).

Passos para usar:
1) Crie uma página (ex.: "Teste DePix").
2) Insira o shortcode `[depix_test]` e publique.
3) Abra a página, informe um valor e gere o Pix para validar o fluxo fim‑a‑fim.

Ambos os shortcodes já vêm habilitados por padrão.

## Webhook (opcional, recomendado)
- Rota: `POST /wp-json/depix/v1/webhook`
- Handler: `EulenWebhook::handleRequest`
- Autorização: header `Authorization: Basic <secret>`
- Onde configurar o secret: no menu "DePix" → "Configurações" → "Webhook Secret" (salvo cifrado)

Como registrar (Telegram da Eulen):
```
/registerwebhook deposit https://seu-dominio/wp-json/depix/v1/webhook <secret>
```

Comportamento:
- Normaliza `id/qrId`, atualiza ou cria o registro da transação e retorna `{ ok, final }`.
- Estados finais (banco): `paid, completed, confirmed, success, depix_sent, expired, canceled, error`.

Caso o webhook não esteja configurado, é possível consultar status por polling (AJAX) usando o shortcode de teste.

## Banco de dados
- Tabela: `{prefix}_depixwp_transactions`
- Colunas: `id, tx_id (unique), amount_cents, status, async, qr_copy_paste, qr_image_url, meta, created_at, updated_at`
- Criada na ativação via `DepixTablesWP::executeInitialTable()`

## Referência rápida da estrutura
- `depixplugin.php`: bootstrap, constantes, hooks, textdomain, enqueues
- `class.depixplugin.php`: inicializa serviços (API, DB, painel, webhook), hooks de ativação/desativação e template do checkout
- `src/panel/class.eulenpanel.php`: painel admin, criptografia/armazenamento do token e do webhook secret, teste de Ping
- `src/services/class.eulen.php`: cliente da API (ping, deposit, deposit-status) + persistência no DB
- `src/helpers/class.requests.php`: wrapper HTTP (`wp_remote_*`) e cabeçalho Authorization
- `src/services/class.database.php`: criação da tabela e helpers de CRUD
- `src/services/class.eulenWebhook.php`: REST `depix/v1/webhook`
- `src/services/index.php`: endpoints do checkout (`/api/pix/start` e `/api/pix/status`) e REST `/depix/v1/deposit`
- `src/shortcodes/class.shortcode.php`: shortcodes `[depix_checkout]` e `[depix_test]` e AJAX `depix_tx_status`
- `assets/checkout/`: CSS/JS do fluxo de checkout
- `templates/depix-blank-template.php`: template "em branco" para a página com `[depix_checkout]`
- `languages/`: traduções (text domain `depixplugin`)

## Boas práticas e segurança
- O token é cifrado com AES‑256‑GCM, com chave derivada das WP SALTs.
- Use o Ping no painel para validar conectividade e credenciais.
- Configure o Webhook Secret e valide o cabeçalho `Authorization` no provedor.
- Mantenha a resposta do webhook em até ~15s e status HTTP 200.

## i18n
- Domínio de tradução: `depixplugin`
- Carregado em `plugins_loaded` a partir de `languages/`

## Licença
GPLv2 ou posterior.
