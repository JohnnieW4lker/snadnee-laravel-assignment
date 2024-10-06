FROM php:8.2-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer

RUN apk --no-cache add \
    git \
    unzip \
    curl \
    bash
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN echo composer --version

RUN docker-php-ext-install mysqli pdo pdo_mysql