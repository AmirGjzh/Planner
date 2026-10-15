FROM node:24-slim AS node

FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    ca-certificates \
    # procps = `ps`: tree-kill (concurrently's dep) spawns it on Ctrl+C to kill the
    # dev process tree. The base image lacks it — without it, shutdown throws spawn ps ENOENT.
    procps \
    libonig-dev \
    libicu-dev \
    libzip-dev \
    libsqlite3-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_sqlite \
    intl \
    mbstring \
    zip \
    sockets \
    pcntl \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Pinned: unpinned pecl silently floats to a newer ext on rebuild. Bump deliberately.
RUN pecl install redis-6.3.0 && docker-php-ext-enable redis

# Composer: pinned phar + sha256 (replaces the composer:2.10 donor image, ~180MB, which
# floated patches within the minor). Keep identical to docker/production/Dockerfile;
# bump both values together. Checksum from
# https://getcomposer.org/download/<version>/composer.phar.sha256sum
RUN curl -fsSL https://getcomposer.org/download/2.10.3/composer.phar -o /usr/local/bin/composer \
    && echo "7a2d379d5b8ffdaa028580ef26494c36d2feef4b178d3dd1473a4dbc5e17c8d6  /usr/local/bin/composer" | sha256sum -c - \
    && chmod +x /usr/local/bin/composer

COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && node --version \
    && npm --version \
    && composer --version

# Chromium system libraries for pest-plugin-browser (runtime is non-root, so apt only works here).
# Version pinned to the `playwright` devDependency in laravel/package.json — bump both together.
RUN npx -y playwright@1.64.0 install-deps chromium \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Playwright volume mountpoint + host-UID ownership (no permission fights); trailing
# grep is a build gate — the image is useless without the redis ext.
RUN mkdir -p /home/workspace/.cache/ms-playwright && chown -R 1000:1000 /home/workspace && php -m | grep -q redis

ENV HOME=/home/workspace

WORKDIR /var/www

CMD ["sleep", "infinity"]