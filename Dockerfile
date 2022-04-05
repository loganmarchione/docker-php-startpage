FROM composer:2 as builder
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

COPY --from=builder /app/vendor /var/www/html/vendor

COPY config.json /var/www/html/config.json

COPY index.php /var/www/html/index.php