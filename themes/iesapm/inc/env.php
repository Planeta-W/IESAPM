<?php
/*--------------------------------------------------------------
BOOTSTRAP DAS VARIÁVEIS DE AMBIENTE (.env)

O .env fica FORA da public_html, na pasta /home da conta:
Exemplo:  /home/storage/8/27/40/site/home/.env

O tema roda em:
  /home/storage/8/27/40/site/public_html/site/wp-content/themes/tema

Este arquivo está em inc/, então são 6 níveis acima dele até a raiz da conta
(inc > tema > themes > wp-content > site > public_html > conta)
e de lá entramos em /home.

Este arquivo pode ser incluído de qualquer lugar (handlers acessados
direto, inc/, functions.php) e só carrega o .env uma vez.
--------------------------------------------------------------*/

if (!defined('ENV_LOADED')) {

    if (!class_exists('Dotenv\Dotenv')) {
        require_once __DIR__ . '/../lib/autoload.php';
    }

    // Diretórios onde o .env pode estar, em ordem de prioridade.
    $env_paths = [];

    // 1) Override manual, se precisar (ex.: SetEnv ENV_PATH /caminho/da/pasta)
    $env_custom = getenv('ENV_PATH') ?: ($_SERVER['ENV_PATH'] ?? '');
    if (!empty($env_custom)) {
        $env_paths[] = rtrim($env_custom, DIRECTORY_SEPARATOR . '/');
    }

    // 2) Produção: /home da conta, fora da public_html
    $env_paths[] = dirname(__DIR__, 6) . '/home';

    // 3) Fallback (dev/local): .env dentro do próprio tema
    $env_paths[] = dirname(__DIR__);

    foreach ($env_paths as $env_dir) {
        if (is_file($env_dir . '/.env')) {
            try {
                Dotenv\Dotenv::createImmutable($env_dir)->safeLoad();
            } catch (\Dotenv\Exception\InvalidFileException $e) {
                // safeLoad() só engole "arquivo não encontrado"; erro de
                // sintaxe DENTRO do .env (ex.: "\" dentro de aspas duplas,
                // que o parser tenta ler como escape) sobe como fatal e
                // derrubava o wp-admin inteiro com um stack trace ilegível.
                error_log('[' . basename(dirname(__DIR__)) . '] .env encontrado em ' . $env_dir . ' mas invalido: ' . $e->getMessage());
                throw new RuntimeException(
                    'O arquivo .env em "' . $env_dir . '" tem erro de sintaxe: ' . $e->getMessage()
                    . ' Se o valor tiver barra invertida (\\), %, $ ou outro caractere especial,'
                    . ' troque as aspas duplas por aspas simples (ex.: CRYPT_IV=\'valor\') — dentro'
                    . ' de aspas simples nada é tratado como escape.'
                );
            }
            define('ENV_DIR', $env_dir);
            break;
        }
    }

    define('ENV_LOADED', true);

    unset($env_paths, $env_custom, $env_dir);
}