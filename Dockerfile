FROM php:8.1-apache

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libonig-dev libzip-dev libpq-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Włączenie mod_rewrite Apache
RUN a2enmod rewrite

# Instalacja Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
