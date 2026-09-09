FROM composer:2.10 AS composer

WORKDIR /var/www
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress --prefer-dist

FROM node:24-alpine AS builder

WORKDIR /var/www

COPY package.json package-lock.json ./
RUN npm ci

COPY --from=composer /var/www/vendor /var/www/vendor
COPY . .

RUN npm run build

FROM nginx:1.30-alpine

COPY ./docker/production/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

COPY --from=builder /var/www/public /var/www/public

WORKDIR /var/www/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]