#!/bin/sh
set -e

if [ "${SKIP_SETUP:-false}" != "true" ]; then

    php artisan migrate --force

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

fi

exec "$@"
