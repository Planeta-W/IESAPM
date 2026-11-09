<?php

define( 'WP_ENVIRONMENT_TYPE', 'local' );

// Dev: desliga a otimização do LiteSpeed Cache (JS delayed/deferred quebra o jQuery).
// Não grava nada no banco, então não afeta a produção.
define( 'LITESPEED_DISABLE_ALL', true );

// Variáveis do .env chegam como variáveis de ambiente (env_file do docker-compose.yml)
function env( $name, $default = '' ) {
    $value = getenv( $name );
    return $value === false ? $default : $value;
}

$http_host = $_SERVER['HTTP_HOST'] ?? 'cli';
$http_port = env('HTTP_PORT', '80');

if ($http_host === 'cli') {
    // WP-CLI (sem HTTP_HOST): usa a porta publicada no host
    $local_url = 'http://localhost' . ($http_port === '80' ? '' : ':' . $http_port);
    define('WP_HOME', $local_url);
    define('WP_SITEURL', $local_url);
} elseif (preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/', $http_host)) {
    // Acesso local pelo navegador ou healthcheck (com ou sem porta)
    define('WP_HOME', 'http://' . $http_host);
    define('WP_SITEURL', 'http://' . $http_host);
} else {
    // Outro host (ex.: túnel/ngrok): usa as URLs do .env
    define('WP_HOME', env('WP_HOME'));
    define('WP_SITEURL', env('WP_SITEURL'));
}

// ** Banco de dados ** //
define( 'DB_NAME', env('DB_NAME') );
define( 'DB_USER', env('DB_USER') );
define( 'DB_PASSWORD', env('DB_PASS') );
define( 'DB_HOST', env('DB_HOST') );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

// ** Chaves e salts ** //
define( 'AUTH_KEY',         env('AUTH_KEY') );
define( 'SECURE_AUTH_KEY',  env('SECURE_AUTH_KEY') );
define( 'LOGGED_IN_KEY',    env('LOGGED_IN_KEY') );
define( 'NONCE_KEY',        env('NONCE_KEY') );
define( 'AUTH_SALT',        env('AUTH_SALT') );
define( 'SECURE_AUTH_SALT', env('SECURE_AUTH_SALT') );
define( 'LOGGED_IN_SALT',   env('LOGGED_IN_SALT') );
define( 'NONCE_SALT',       env('NONCE_SALT') );

$table_prefix = env('DB_PREFIX') ?: 'wp_';

// Sem atualizações automáticas (core, plugins, temas): a versão do WordPress é a
// fixada no Dockerfile, e uma atualização local poderia alterar o banco de produção
define( 'AUTOMATIC_UPDATER_DISABLED', true );

// ** Debug ** //
define( 'WP_DEBUG', true );
// Grava em wp-content/debug.log em vez de exibir na tela (avisos de plugins
// quebrariam respostas JSON, inclusive o healthcheck em /wp-json)
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', '0' );

/* Adicione valores personalizados entre esta linha e a linha "Pare de editar". */

/* Pare de editar! */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
