FROM composer:2.10 AS composer

WORKDIR /var/www
COPY laravel/composer.json laravel/composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress --prefer-dist

FROM node:24 AS builder

WORKDIR /var/www

COPY laravel/package.json laravel/package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY --from=composer /var/www/vendor /var/www/vendor
COPY laravel/ .

RUN npm run build

FROM nginx:1.30

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    && rm -rf /var/lib/apt/lists/*

COPY ./docker/production/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

COPY --from=builder /var/www/public /var/www/public

WORKDIR /var/www/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]