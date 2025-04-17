#!/bin/bash
set -e

# Start required services
service php8.3-fpm start
service nginx start

# Set proper permissions
chown -R www-data:www-data /var/www
find /var/www -type d -exec chmod 755 {} \;
find /var/www -type f -exec chmod 644 {} \;
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Migrate database
php artisan migrate --force

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
php artisan storage:link || true

# Publish assets
php artisan livewire:publish --config
php artisan livewire:publish --assets
php artisan filament:assets
php artisan filament:cache-components

# Dump autoload
composer dump-autoload

# Keep the container running
tail -f /dev/null