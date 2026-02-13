FROM composer/composer:2.5.4 AS composer_builder

FROM php:8.3-fpm-alpine

ARG HOST_UID=82
ARG HOST_GID=82

ENV HOST_UID=${HOST_UID:-82} \
    HOST_GID=${HOST_GID:-82}

RUN apk add --no-cache  \
    linux-headers \
    shadow \
    git \
    autoconf \
    g++ \
    make \
    postgresql-dev \
    curl-dev \
    freetype-dev \
    libpng-dev \
    jpeg-dev \
    libjpeg-turbo-dev

RUN docker-php-ext-configure \
    gd --with-jpeg --with-freetype

RUN docker-php-ext-install  \
    sockets \
    pcntl \
    pdo \
    pdo_pgsql \
    curl \
    gd \
    exif
RUN pecl install redis xdebug xhprof apcu
RUN docker-php-ext-enable  \
    opcache  \
    sodium \
    sockets \
    pcntl \
    redis \
    pdo \
    pdo_pgsql \
    xdebug \
    xhprof \
    curl \
    gd \
    exif \
    apcu

RUN set -eux; \
    if [ $HOST_UID -ne 82 ]; then \
        usermod -u ${HOST_UID} www-data; \
    fi; \
    if [ $HOST_GID -ne 82 ] && ! grep -q :${HOST_GID}: /etc/group; then \
        groupmod -g ${HOST_GID} www-data; \
    fi;

COPY --from=composer_builder --chown=www-data:www-data /usr/bin/composer /usr/local/bin/composer

COPY docker/app/php/php.ini "$PHP_INI_DIR/php.ini"
COPY docker/app/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/app/php/zz-option.conf /usr/local/etc/php-fpm.d/zz-option.conf
COPY docker/app/php/99-zz.ini /usr/local/etc/php/conf.d/99-zz.ini

USER "www-data"

WORKDIR /app