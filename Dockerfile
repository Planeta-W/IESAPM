FROM composer:2 AS composer

FROM wordpress:php8.2-apache

# Copia o binário do Composer da imagem oficial (evita download via curl)
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Instala as dependências necessárias
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libzip-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mysqli zip \
    && rm -rf /var/lib/apt/lists/*

# Habilita o mod_rewrite do Apache para o .htaccess funcionar
RUN a2enmod rewrite

# Instala WP-CLI (uma vez no build, não a cada startup)
RUN curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

