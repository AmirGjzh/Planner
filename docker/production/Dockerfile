# Single build graph for both prod runtime images.
#   target production → planner-app:latest (php-fpm + queue share it)
#   target nginx      → static assets + fastcgi proxy
# One composer install, one npm ci, one vite build per clean build.

FROM php:8.4-fpm AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    libonig-dev \
    libicu-dev \
    libzip-dev \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# pdo_mysql intl mbstring zip — no gd/bcmath: zero call sites (same as dev).
# pcntl: queue:work's SIGTERM handlers — without it `docker stop` kills the worker
# mid-job (retried via --tries, but never drained cleanly). Dev's image has it too.
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    intl \
    mbstring \
    zip \
    pcntl

# Pinned: unpinned pecl silently floats to a newer ext on rebuild. Bump deliberately.
RUN pecl install redis-6.3.0 && docker-php-ext-enable redis

# Composer: pinned phar + sha256 (replaces the composer:2.10 donor image, ~180MB, which
# floated patches within the minor). Keep identical to docker/development/php-cli/Dockerfile;
# bump both values together. Checksum from
# https://getcomposer.org/download/<version>/composer.phar.sha256sum
# curl/ca-certificates ship in the php base image — no apt needed here.
RUN curl -fsSL https://getcomposer.org/download/2.10.3/composer.phar -o /usr/local/bin/composer \
    && echo "7a2d379d5b8ffdaa028580ef26494c36d2feef4b178d3dd1473a4dbc5e17c8d6  /usr/local/bin/composer" | sha256sum -c - \
    && chmod +x /usr/local/bin/composer

WORKDIR /var/www

COPY laravel/composer.json laravel/composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress --prefer-dist

COPY laravel/ .
RUN php artisan package:discover --ansi

FROM node:24-slim AS frontend

WORKDIR /var/www

COPY laravel/package.json laravel/package-lock.json ./
RUN npm ci --no-audit --no-fund

# vendor IS needed by the vite build: resources/js/app.js imports
# ../../vendor/livewire/livewire/dist/livewire.esm (composer vendor, not views/vendor).
COPY --from=builder /var/www/vendor /var/www/vendor
COPY laravel/ .

RUN npm run build

FROM php:8.4-fpm AS production

# Runtime libs only (the -dev packages carry ~55MB of headers/static libs for nothing).
# Names are pinned to the base's Debian release (currently trixie): when php:8.4-fpm moves
# to the next release these fail LOUDLY at apt-get — reinstall the old names in any running
# image (dpkg -l) to get the new ones. See production.md decisions log.
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu76 \
    libonig5 \
    libzip5 \
    && rm -rf /var/lib/apt/lists/*

COPY ./docker/production/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/docker-php-ext-* /usr/local/bin/

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# `COPY --chown` sets ownership in passing: a `RUN chown -R` would repack every file's
# content (vendor included) into its own layer. public/build stays root-owned — the app
# only ever reads the manifest, writes go to storage/ + bootstrap/cache/ (both chowned).
COPY --chown=www-data:www-data --from=builder /var/www /var/www
COPY --from=frontend /var/www/public/build /var/www/public/build

# COPY --chown owns the contents but not the destination directory itself — hand
# /var/www (the dir, non-recursive: a single metadata-only entry) to www-data:
# tinker creates psysh's .config straight in the project root.
RUN chown www-data:www-data /var/www

WORKDIR /var/www

USER www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]

FROM nginx:1.30-alpine AS nginx

# curl for the /up healthcheck (busybox wget can't do -f fail-on-4xx semantics cleanly).
RUN apk add --no-cache curl

COPY ./docker/production/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

COPY --from=frontend /var/www/public /var/www/public

WORKDIR /var/www/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
