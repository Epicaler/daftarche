#!/bin/sh
set -e

cd /var/www/html

# Run as the host user (www-data was remapped to your UID in the image)
as_user() { su-exec www-data "$@"; }

if [ ! -f vendor/autoload.php ]; then
    echo "Installing PHP dependencies (first run)..."
    as_user composer install --no-interaction --prefer-dist
fi

if [ "$1" = "php-fpm" ]; then
    if [ ! -f .env ]; then
        echo ".env not found. Create it first: cp .env.example .env" >&2
        exit 1
    fi

    # First run: generate APP_KEY into .env and use it for this process too
    if [ -z "$APP_KEY" ]; then
        if ! grep -q '^APP_KEY=base64:' .env; then
            echo "Generating APP_KEY..."
            as_user php artisan key:generate --force
        fi
        APP_KEY=$(grep '^APP_KEY=' .env | cut -d= -f2-)
        export APP_KEY
    fi

    echo "Waiting for database at ${DB_HOST}:${DB_PORT:-3306}..."
    i=0
    until mariadb-admin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl --silent 2>/dev/null; do
        i=$((i + 1))
        [ "$i" -ge 60 ] && echo "Database not reachable" >&2 && exit 1
        sleep 2
    done

    mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
    # No config/route/view caching: the code is live-mounted, so changes must apply without rebuilding.
    # (optimize:clear is avoided: it also clears the database cache, whose table doesn't exist on a fresh install)
    as_user php artisan config:clear
    as_user php artisan route:clear
    as_user php artisan view:clear
    as_user php artisan migrate --force
    as_user php artisan db:seed --force
fi

exec "$@"
