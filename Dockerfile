# syntax=docker/dockerfile:1

###############################################################################
# Airport Incident Dashboard — multi-stage production image
#
#   Stage 1 (assets):  Node 20 builds the Vite/Tailwind/Alpine/Chart.js bundle.
#   Stage 2 (vendor):  Composer installs PHP dependencies (no dev) for prod.
#   Stage 3 (app):     PHP 8.3-FPM + nginx + supervisor serve the application.
#
# The container ships with MySQL drivers; the compose file wires it to MySQL 8.
# SQLite still works out of the box for the zero-config local workflow.
###############################################################################

# ----------------------------------------------------------------------------
# Stage 1 — Frontend assets
# ----------------------------------------------------------------------------
FROM node:20-alpine AS assets

WORKDIR /app

# Install JS dependencies against the lockfile for reproducible builds.
COPY package.json package-lock.json ./
RUN npm ci

# Build the production asset bundle into public/build.
COPY resources/ resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY public/ public/
RUN npm run build


# ----------------------------------------------------------------------------
# Stage 2 — PHP dependencies (Composer)
# ----------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

# Install production dependencies only; defer artisan scripts until the full
# application source is present in the final stage.
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction \
        --no-progress


# ----------------------------------------------------------------------------
# Stage 3 — Runtime (PHP-FPM + nginx + supervisor)
# ----------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS app

# System packages and PHP extensions required by Laravel 11 + MySQL.
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        icu-dev \
        oniguruma-dev \
        libzip-dev \
        sqlite-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        pdo_sqlite \
        intl \
        mbstring \
        bcmath \
        zip \
        opcache

# Production-friendly OPcache settings.
RUN { \
        echo "opcache.enable=1"; \
        echo "opcache.enable_cli=0"; \
        echo "opcache.memory_consumption=128"; \
        echo "opcache.max_accelerated_files=10000"; \
        echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Application source.
COPY . .

# Vendored PHP packages + built frontend assets from earlier stages.
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Finalize the autoloader and run package discovery now that the full app exists.
COPY --from=vendor /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && php artisan package:discover --ansi || true

# Ensure a SQLite database file exists for the zero-config fallback and fix perms.
RUN touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

# Service configuration.
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
