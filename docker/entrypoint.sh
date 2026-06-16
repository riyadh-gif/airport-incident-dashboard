#!/usr/bin/env bash
set -euo pipefail

# -----------------------------------------------------------------------------
# Container entrypoint for the Airport Incident Dashboard.
#
# Runs first-boot housekeeping (app key, caches, migrations) before handing
# control to the CMD (supervisor -> php-fpm + nginx). Safe to run on every
# start: each step is idempotent.
# -----------------------------------------------------------------------------

cd /var/www/html

# Ensure an APP_KEY exists. In real deployments set APP_KEY via the environment;
# this only generates an ephemeral key when none is provided.
if [ -z "${APP_KEY:-}" ] && ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    php artisan key:generate --force --no-interaction || true
fi

# Wait for the database when using MySQL so migrations do not race the DB boot.
if [ "${DB_CONNECTION:-sqlite}" = "mysql" ]; then
    echo "Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306} ..."
    until php -r "exit(@fsockopen(getenv('DB_HOST') ?: 'mysql', (int)(getenv('DB_PORT') ?: 3306)) ? 0 : 1);"; do
        sleep 2
    done
fi

# Cache framework config/routes/views for production performance.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Apply database migrations (and seed on first run if requested).
php artisan migrate --force --no-interaction || true
if [ "${SEED_ON_START:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction || true
fi

exec "$@"
