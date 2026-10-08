#!/usr/bin/env bash
set -e

echo "==> Preparing storage and sqlite database..."
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

echo "==> Creating storage link..."
php artisan storage:link || true

echo "==> Running database migrations and seeders..."
php artisan migrate --force --seed || php artisan migrate --force

echo "==> Optimizing caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting PHP-FPM..."
php-fpm -D

echo "==> Starting Nginx..."
nginx -g "daemon off;"
