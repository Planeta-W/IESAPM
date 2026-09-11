#!/bin/bash
set -e

WP=/var/www/html

# 1. Composer install (gera vendor/ dentro do container)
if [ -f "$WP/composer.json" ]; then
    echo "Rodando composer install..."
    composer install --no-interaction --no-dev --optimize-autoloader --working-dir="$WP"
fi

# 2. Gera .htaccess com regras do WordPress + redirect de uploads
UPLOADS_URL=$PRODUCTION_UPLOADS_URL
cat > "$WP/.htaccess" << HTACCESS
# Redirect uploads ausentes para producao (deve vir ANTES das regras do WordPress)
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^wp-content/uploads/(.*)$ ${UPLOADS_URL}/\$1 [L,R=301]
</IfModule>

# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
HTACCESS
echo ".htaccess configurado: $UPLOADS_URL"

# 3. Inicia Apache em background para poder rodar WP-CLI depois
docker-entrypoint.sh apache2-foreground &
APACHE_PID=$!

# 4. Aguarda Apache responder (wp-login.php nao carrega tema, retorna 200 sempre)
echo "Aguardando WordPress iniciar..."
until curl -sf http://localhost/wp-login.php > /dev/null 2>&1; do sleep 2; done
echo "WordPress pronto."

# 5. Limpeza de temas e plugins padrao
rm -rf "$WP/wp-content/themes/twenty"*
rm -rf "$WP/wp-content/plugins/akismet"

# 6. Detecta plugins ativos no banco (lê wp_options diretamente, sem checar arquivos no disco)
echo "Sincronizando plugins com o banco de dados..."
ACTIVE_PLUGINS=$(wp eval '
    foreach (get_option("active_plugins", []) as $plugin) {
        echo dirname($plugin) . "\n";
    }
' --allow-root --path="$WP" 2>/dev/null)

if [ -n "$ACTIVE_PLUGINS" ]; then
    echo "Plugins ativos no banco: $ACTIVE_PLUGINS"
    echo "$ACTIVE_PLUGINS" | while read -r plugin; do
        [ -z "$plugin" ] && continue
        echo "Instalando: $plugin"
        wp plugin install "$plugin" --allow-root --path="$WP" 2>&1 || echo "AVISO: $plugin nao disponivel no wordpress.org (plugin premium?)"
    done
    echo "Sincronizacao concluida."
else
    echo "Nenhum plugin ativo encontrado no banco."
fi

echo "Ambiente pronto e plugins instalados!"

# 7. Mantém container vivo aguardando Apache
wait $APACHE_PID
