FROM php:8.3-alpine
RUN apk update && apk add --no-cache linux-headers
RUN mkdir -p /usr/src/php/ext/xdebug && \
    curl -fsSL https://pecl.php.net/get/xdebug-3.3.0.tgz | tar xvz -C "/usr/src/php/ext/xdebug" --strip 1
RUN mkdir -p /usr/src/php/ext/ast && \
    curl -fsSL https://pecl.php.net/get/ast-1.1.1.tgz | tar xvz -C "/usr/src/php/ext/ast" --strip 1
RUN docker-php-ext-install xdebug ast
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
