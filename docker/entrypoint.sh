#!/bin/sh
set -e

# Copia public/ al volumen compartido con nginx (assets compilados)
cp -r /var/www/html/public/. /var/www/public/

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

exec php-fpm
