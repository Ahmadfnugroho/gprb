#!/bin/bash
set -e

# Jalankan Laravel command saat startup
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# Jalankan Apache di foreground
exec apache2-foreground
