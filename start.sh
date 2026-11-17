#!/usr/bin/env bash
set -e

echo "=========================================================="
echo " Starting Sinar Roda Mas via Nixpacks (No Docker needed) "
echo "=========================================================="

# 1. Automatic Database detection from Railway plugins
if [ -n "$MYSQL_URL" ] && [ -z "$DB_URL" ]; then
    echo "==> Detected Railway MySQL service via MYSQL_URL"
    export DB_CONNECTION=mysql
    export DB_URL="$MYSQL_URL"
elif [ -n "$DATABASE_URL" ] && [ -z "$DB_URL" ]; then
    echo "==> Detected Railway PostgreSQL service via DATABASE_URL"
    export DB_CONNECTION=pgsql
    export DB_URL="$DATABASE_URL"
fi

# 2. SQLite Database Setup
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    echo "==> Preparing SQLite database..."
    mkdir -p database
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
        echo "==> Created fresh database/database.sqlite"
    fi
fi

# 3. Storage and Cache Permissions
echo "==> Setting up storage directories..."
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 4. Storage Link
php artisan storage:link || true

# 5. Production Optimization & Cache
echo "==> Caching configuration, routes, and views..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 6. Database Migrations & Seeding
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force

    if [ "${DB_SEED:-false}" = "true" ]; then
        echo "==> Running database seeders (initial users & catalog)..."
        php artisan db:seed --force
    fi
fi

# 7. Start Laravel server on dynamic Railway $PORT
TARGET_PORT="${PORT:-8080}"
echo "==> Sinar Roda Mas is LIVE on port: ${TARGET_PORT}"
exec php artisan serve --host=0.0.0.0 --port="${TARGET_PORT}"
