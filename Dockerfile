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

# Set working directory ke root Laravel
WORKDIR /var/www/html

# Salin source code Laravel
COPY . .

# Atur permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Ganti DocumentRoot ke public Laravel
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Konfigurasi directory agar .htaccess Laravel bisa dijalankan
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>' >> /etc/apache2/apache2.conf

# Opsional: atur ServerName untuk hilangkan warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Salin entrypoint.sh ke container
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Beri permission untuk dieksekusi
RUN chmod +x /usr/local/bin/entrypoint.sh

# Gunakan entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]