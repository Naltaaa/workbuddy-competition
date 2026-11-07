FROM php:8.5-cli-alpine

RUN apk add --no-cache \
    git curl zip unzip \
    libzip-dev \
    icu-dev icu-libs \
    oniguruma-dev

RUN docker-php-ext-configure intl \
 && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        mbstring \
        zip \
        intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]