#!/bin/bash
set -e

raw_key="${APP_KEY#base64:}"

if [ -z "${APP_KEY}" ] || [[ "${APP_KEY}" != base64:* ]] || [ "${#raw_key}" -ne 44 ] || [ "$(printf '%s' "${raw_key}" | base64 -d | wc -c)" -ne 32 ]; then
    echo "ERROR: APP_KEY is missing or in a bad format (expected: base64: + a 32-byte key). Generate a key and put it in .env, e.g. printf 'base64:%s\n' \"\$(openssl rand -base64 32)\"" >&2
    exit 1
fi

# SKIP_MIGRATE=1 (set only on the queue service) skips the migrate+seed run: php-fpm
# already did it on boot and the queue starts only after php-fpm is healthy.
if [ "${SKIP_MIGRATE:-0}" != "1" ]; then
    php artisan migrate --seed --force
fi
php artisan optimize

exec "$@"