FROM php:8.4-fpm-alpine
WORKDIR /app
RUN apk add --no-cache \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        freetype-dev
RUN apk add autoconf g++ libmcrypt make libmcrypt-dev mosquitto-dev tzdata bash
RUN apk add --update linux-headers
RUN pecl install xdebug-3.4.6 \
    && docker-php-ext-enable xdebug
RUN apk add libpq git libzip-dev postgresql-dev  && \
    docker-php-ext-install pdo zip pdo_pgsql pgsql sockets
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd \
    && docker-php-ext-enable gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

RUN pecl install opentelemetry && docker-php-ext-enable opentelemetry

RUN pecl install mongodb && docker-php-ext-enable mongodb
