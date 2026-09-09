#!/bin/bash
set -e

if [ -z "${APP_KEY}" ]; then
    echo "ERROR: APP_KEY is not set." >&2
    exit 1
fi

php artisan migrate --force
php artisan optimize

exec php-fpm