#!/bin/bash
set -e

WP=/var/www/html

# 1. Gera .htaccess com regras do WordPress + redirect de uploads
# Endpoint usado para saber se o WordPress responde (?rest_route= funciona com
# qualquer estrutura de permalink; /wp-json dá 404 com permalinks "Simples")
HEALTH_URL="http://localhost/?rest_route=/"

UPLOADS_URL=${PRODUCTION_UPLOADS_URL%/}
{
if [ -n "$UPLOADS_URL" ]; then
    # 302 (não 301): o navegador não guarda em cache, então um arquivo enviado
    # localmente depois passa a ser servido daqui mesmo
    cat << HTACCESS
# Redirect uploads ausentes para producao (deve vir ANTES das regras do WordPress)
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^wp-content/uploads/(.*)$ ${UPLOADS_URL}/\$1 [L,R=302]
</IfModule>

HTACCESS
fi
cat << 'HTACCESS'
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
} > "$WP/.htaccess"
echo ".htaccess configurado (uploads: ${UPLOADS_URL:-sem redirect, PRODUCTION_UPLOADS_URL vazio})"

# 2. Inicia Apache em background para poder rodar WP-CLI depois
docker-entrypoint.sh apache2-foreground &
APACHE_PID=$!

# Repassa o sinal de parada ao Apache (bash como PID 1 não repassa sozinho,
# e o docker stop mataria o container à força). A imagem php:apache usa
# STOPSIGNAL SIGWINCH, por isso ele entra na lista junto com TERM/INT.
trap 'kill -TERM "$APACHE_PID" 2>/dev/null; wait "$APACHE_PID"; exit 0' TERM INT WINCH

# 3. Aguarda WordPress responder (REST API, como o healthcheck; o wp-login.php
#    pode ser renomeado por plugin de segurança e retornar 404 para sempre)
echo "Aguardando WordPress iniciar..."
TENTATIVAS=0
until curl -sf "$HEALTH_URL" > /dev/null 2>&1; do
    TENTATIVAS=$((TENTATIVAS + 1))
    if [ $((TENTATIVAS % 15)) -eq 0 ]; then
        echo "AVISO: WordPress ainda nao respondeu apos $((TENTATIVAS * 2))s. Verifique o acesso ao banco (DB_HOST/DB_USER/DB_PASS no .env)."
        curl -s -o /dev/null -w "Status HTTP da REST API: %{http_code}\n" "$HEALTH_URL" || true
    fi
    sleep 2
done
echo "WordPress pronto."

# 4. Limpeza de temas e plugins padrao
rm -rf "$WP/wp-content/themes/twenty"*
rm -rf "$WP/wp-content/plugins/akismet"

# 5. Detecta plugins ativos no banco (lê wp_options diretamente, sem checar arquivos no disco)
echo "Sincronizando plugins com o banco de dados..."
ACTIVE_PLUGINS=$(wp eval '
    foreach (get_option("active_plugins", []) as $plugin) {
        echo dirname($plugin) . "\n";
    }
' --allow-root --path="$WP" 2>/dev/null || true)
# (|| true: com set -e, uma falha do wp eval, ex. banco sem WordPress instalado,
#  derrubaria o script e o restart: unless-stopped deixaria o container em loop)

if [ -n "$ACTIVE_PLUGINS" ]; then
    echo "Plugins ativos no banco: $ACTIVE_PLUGINS"
    echo "$ACTIVE_PLUGINS" | while read -r plugin; do
        # "." = plugin de arquivo único (ex.: hello.php), sem slug para instalar
        [ -z "$plugin" ] || [ "$plugin" = "." ] && continue
        # Já está no volume wp_data: não consulta o wordpress.org de novo
        wp plugin is-installed "$plugin" --allow-root --path="$WP" 2>/dev/null && continue
        echo "Instalando: $plugin"
        wp plugin install "$plugin" --allow-root --path="$WP" 2>&1 || echo "AVISO: $plugin nao disponivel no wordpress.org (plugin premium?)"
    done
    # WP-CLI roda como root; devolve os plugins ao www-data (usuário do Apache)
    # para o wp-admin conseguir atualizar/remover sem pedir credenciais de FTP
    chown -R www-data:www-data "$WP/wp-content/plugins"
    echo "Sincronizacao concluida."
else
    echo "Nenhum plugin ativo encontrado no banco (se o banco for novo, instale o WordPress em /wp-admin/install.php)."
fi

echo "Ambiente pronto e plugins instalados!"

# 6. Mantém container vivo aguardando Apache
wait $APACHE_PID
