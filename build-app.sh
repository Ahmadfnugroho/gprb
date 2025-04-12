# 1. Persiapkan direktori cache
mkdir -p bootstrap/cache
chmod -R 775 bootstrap/cache

# 2. Install Livewire dan dependensi Composer
composer require livewire/livewire
composer install --optimize-autoloader --no-dev

# 3. Install dan build asset frontend jika ada package.json
if exist package.json (
  npm install
  npm run build
)

# 4. Generate cache Laravel (dengan fallback jika error)
php artisan config:cache || echo "config:cache failed"
php artisan route:cache || echo "route:cache failed"
php artisan view:cache || echo "view:cache failed"

# 5. Publish assets Livewire dan Filament
php artisan vendor:publish --force --tag=livewire:assets
php artisan filament:assets
php artisan filament:cache-components

# 6. Clear cache Laravel (untuk memastikan clean state)
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# 7. Dump autoload
composer dump-autoload
