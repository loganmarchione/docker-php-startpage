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

FROM php:8-apache-bullseye

ARG BUILD_DATE

LABEL \
  maintainer="Logan Marchione <logan@loganmarchione.com>" \
  org.opencontainers.image.authors="Logan Marchione <logan@loganmarchione.com>" \
  org.opencontainers.image.title="docker-php-startpage" \
  org.opencontainers.image.description="Runs a PHP-based startpage in Docker" \
  org.opencontainers.image.created=$BUILD_DATE

RUN apt-get update && apt-get install -y --no-install-recommends \
    netcat && \
    rm -rf /var/lib/apt/lists/*

COPY --from=builder /app/vendor /var/www/html/vendor

COPY VERSION /

COPY sample.json /var/www/html/sample.json

COPY index.php /var/www/html/index.php

HEALTHCHECK CMD nc -z localhost 80 || exit 1
