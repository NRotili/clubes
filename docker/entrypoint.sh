#!/bin/sh
set -e

if [ "${SKIP_SETUP:-false}" != "true" ]; then

    php artisan storage:link --quiet 2>/dev/null || true

    php artisan migrate --force

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

fi

exec "$@"
