#!/bin/bash
set -e

echo "Starting build script..."

# Print out PHP and Composer version for debugging
php -v
composer -v
composer update


# Install dependencies
composer install --optimize-autoloader --no-dev

# Build assets
if [ -f "package.json" ]; then
    npm install && npm run build
fi

# Cache clearing and other commands
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan vendor:publish --force --tag=livewire:assets
php artisan filament:assets
php artisan filament:cache-components

# Clean-up cache and dump autoload
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Dump autoload
composer dump-autoload
