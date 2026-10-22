# Site Grupo Fedcorp

Código do site WordPress `grupofedcorp.com.br` (desenvolvido localmente com Local by Flywheel).

## O que está versionado
- `wp-content/mu-plugins/` – menu lateral de atalhos e carrossel de seguradoras (`[fedcorp_parceiros]`)
- `wp-content/themes/` – temas (hello-biz é o tema ativo)
- `wp-content/plugins/` – apenas plugins gratuitos
- `wp-content/uploads/` – mídia
- `lpage/`, `assistencia/` – páginas estáticas
- `deploy/` – template Elementor da Home e instruções de publicação

## O que NÃO está versionado (ver `.gitignore`)
- `wp-config.php` e qualquer senha/chave
- Núcleo do WordPress (`wp-admin`, `wp-includes`, `wp-*.php`) – reinstalar na versão indicada em `wp-includes/version.php`
- Plugins pagos (Elementor Pro, JetEngine/Crocoblock, Unlimited Elements, Duplicator Pro, WPCode) – instalar pela licença
- Banco de dados – o conteúdo das páginas (Elementor) vive no banco; exportar separadamente
