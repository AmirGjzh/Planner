FROM composer:2.10 AS composer

FROM php:8.4-fpm AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    libonig-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    intl \
    mbstring \
    zip \
    bcmath \
    gd \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install redis && docker-php-ext-enable redis

WORKDIR /var/www

COPY . /var/www

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress --prefer-dist \
    && php artisan package:discover --ansi

FROM node:24 AS frontend

WORKDIR /var/www

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY --from=builder /var/www/vendor /var/www/vendor
COPY . .

RUN npm run build

FROM php:8.4-fpm AS production

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY ./docker/production/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/docker-php-ext-* /usr/local/bin/

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=builder /var/www /var/www
COPY --from=frontend /var/www/public/build /var/www/public/build

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

USER www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]