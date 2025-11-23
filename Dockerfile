FROM php:8.5-alpine
RUN apk update && apk add --no-cache linux-headers
RUN mkdir -p /usr/src/php/ext/xdebug && \
    curl -fsSL https://xdebug.org/files/xdebug-3.5.0alpha3.tgz | tar xvz -C "/usr/src/php/ext/xdebug" --strip 1
RUN docker-php-ext-install xdebug
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
