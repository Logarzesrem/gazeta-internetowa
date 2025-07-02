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

# Set Apache DocumentRoot to public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
