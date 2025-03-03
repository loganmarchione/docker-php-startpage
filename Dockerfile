FROM composer:2 AS builder
WORKDIR /app/
COPY composer.* ./
RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-dev \
    --prefer-dist \
    --no-ansi \
    --no-progress

FROM php:8.4-apache-bookworm

ARG BUILD_DATE

LABEL \
  maintainer="Logan Marchione <logan@loganmarchione.com>" \
  org.opencontainers.image.authors="Logan Marchione <logan@loganmarchione.com>" \
  org.opencontainers.image.title="docker-php-startpage" \
  org.opencontainers.image.description="Runs a PHP-based startpage in Docker" \
  org.opencontainers.image.created=$BUILD_DATE

RUN apt-get update && apt-get -y install --no-install-recommends \
    netcat-traditional && \
    rm -rf /var/lib/apt/lists/*

COPY --from=builder /app/vendor /var/www/html/vendor

COPY VERSION /

COPY templates /var/www/html/templates

COPY sample.json /var/www/html/sample.json

COPY index.php /var/www/html/index.php

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

HEALTHCHECK CMD nc -z localhost 80 || exit 1
