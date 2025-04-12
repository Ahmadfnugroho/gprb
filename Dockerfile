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

# Copy project Laravel ke dalam container
COPY . /var/www/html

# Set permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Ganti DocumentRoot Apache ke folder public Laravel
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Tambahkan index.php ke DirectoryIndex jika belum
RUN echo '<Directory /var/www/html/public>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>' >> /etc/apache2/apache2.conf
