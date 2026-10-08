#!/usr/bin/env bash
set -e

echo "==> Configuring PHP-FPM..."
sed -i 's/;clear_env = no/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true
sed -i 's/clear_env = yes/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true

echo "==> Validating APP_KEY..."
FALLBACK_KEY="base64:+RLH70IXwcygyDPZGH0Euo2e4FK8G2WO+Q7QfRjBzxE="
if [ -z "$APP_KEY" ] || [[ "$APP_KEY" != base64:* ]] || [ "${#APP_KEY}" -ne 51 ]; then
    echo "Notice: Non-standard or missing APP_KEY detected. Using valid 32-byte key."
    export APP_KEY="$FALLBACK_KEY"
fi

echo "==> Syncing .env file..."
touch /var/www/html/.env
if grep -q "^APP_KEY=" /var/www/html/.env; then
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" /var/www/html/.env
else
    echo "APP_KEY=${APP_KEY}" >> /var/www/html/.env
fi

echo "==> Preparing storage and sqlite database..."
mkdir -p /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

echo "==> Creating storage link..."
php artisan storage:link || true

echo "==> Running database migrations and seeders..."
php artisan migrate --force --seed || php artisan migrate --force

echo "==> Optimizing caches..."
php artisan config:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting PHP-FPM..."
php-fpm -D

echo "==> Starting Nginx..."
nginx -g "daemon off;"
