# Use a base image with PHP 8.4
FROM php:8.4-cli

# Install necessary extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Set working directory
WORKDIR /app

# Copy the project files into the container
COPY . /app

# Install dependencies
RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install --no-dev --optimize-autoloader

# Install npm dependencies and build assets
RUN npm install && npm run build

# Expose necessary port
EXPOSE 80

# Set the entrypoint
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
