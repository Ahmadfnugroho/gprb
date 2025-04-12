# Gunakan image PHP 8.3 dengan Apache
FROM php:8.3-apache

# Install dependensi sistem dan ekstensi PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Salin source code Laravel ke direktori kerja container
COPY . /var/www/html

# Atur permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Salin konfigurasi Apache kustom (jika ada)
# COPY ./apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Jalankan Composer install (opsional jika tidak pakai volume)
# RUN composer install --no-dev --optimize-autoloader
