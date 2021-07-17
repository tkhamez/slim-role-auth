FROM php:8.0-alpine
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
