#!/bin/bash
set -e

# Jalankan Composer Install
composer install --no-dev --optimize-autoloader --no-interaction

# Jalankan Laravel command saat startup
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# Jalankan Apache di foreground
exec apache2-foreground
