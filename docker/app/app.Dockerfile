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
    curl-dev

RUN docker-php-ext-install  \
    sockets \
    pcntl \
    pdo \
    pdo_pgsql \
    curl
RUN pecl install redis xdebug xhprof
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
    curl

RUN set -eux; \
    if [ $HOST_UID -ne 82 ]; then \
        usermod -u ${HOST_UID} www-data; \
    fi; \
    if [ $HOST_GID -ne 82 ] && ! grep -q :${HOST_GID}: /etc/group; then \
        groupmod -g ${HOST_GID} www-data; \
    fi;

COPY --from=composer_builder --chown=www-data:www-data /usr/bin/composer /usr/local/bin/composer

USER "www-data"

WORKDIR /app