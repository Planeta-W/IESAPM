# Versões fixas: a imagem só muda quando estes valores mudarem.
# Para trocar de versão é preciso apagar o volume (docker compose down -v),
# porque o core fica guardado nele e não é atualizado sozinho.
ARG WP_VERSION=7.1.3
ARG PHP_VERSION=8.5
ARG WP_CLI_VERSION=2.12.0

FROM wordpress:cli-${WP_CLI_VERSION} AS wpcli

FROM wordpress:${WP_VERSION}-php${PHP_VERSION}-apache

# WP-CLI copiado da imagem oficial (versão fixa, sem download no build)
COPY --from=wpcli /usr/local/bin/wp /usr/local/bin/wp

# pdo_mysql é usado pelos handlers do tema
# (mysqli e zip já vêm na imagem; curl e mod_rewrite também, então não precisam ser instalados)
RUN docker-php-ext-install pdo_mysql
