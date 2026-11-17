#!/bin/sh
set -e

# 1. Configure Port for Nginx (Railway dynamically injects $PORT)
PORT=${PORT:-8080}
echo "==> Configuring Nginx to listen on port: ${PORT}..."
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/http.d/default.conf

# 2. Automatically adapt Railway DB Environment Variables if present
if [ -n "$MYSQL_URL" ] && [ -z "$DB_URL" ]; then
    echo "==> Detected Railway MySQL service via MYSQL_URL"
    export DB_CONNECTION=mysql
    export DB_URL="$MYSQL_URL"
elif [ -n "$MYSQLHOST" ] && [ -z "$DB_HOST" ]; then
    echo "==> Detected Railway MySQL service via MYSQLHOST"
    export DB_CONNECTION=mysql
    export DB_HOST="$MYSQLHOST"
    export DB_PORT="${MYSQLPORT:-3306}"
    export DB_DATABASE="$MYSQLDATABASE"
    export DB_USERNAME="$MYSQLUSER"
    export DB_PASSWORD="$MYSQLPASSWORD"
elif [ -n "$DATABASE_URL" ] && [ -z "$DB_URL" ]; then
    echo "==> Detected Railway PostgreSQL service via DATABASE_URL"
    export DB_CONNECTION=pgsql
    export DB_URL="$DATABASE_URL"
fi

# 3. SQLite Database Initialization (if using SQLite)
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    echo "==> Initializing SQLite database storage..."
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
        echo "==> Created fresh /var/www/html/database/database.sqlite"
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
fi

# 4. Storage & Cache Permissions
echo "==> Ensuring storage and bootstrap cache permissions..."
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Storage Symlink
php artisan storage:link --force || true

# 6. Cache Warm-up / Optimization
echo "==> Optimizing configuration..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

if [ "$APP_ENV" = "production" ]; then
    echo "==> Caching routes, configuration, and views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# 7. Database Migrations
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force

    # Run seeders if DB_SEED=true
    if [ "${DB_SEED:-false}" = "true" ]; then
        echo "==> Running database seeders..."
        php artisan db:seed --force
    fi
fi

echo "=========================================================="
echo " Sinar Roda Mas Backend is ready! Starting Supervisor..."
echo "=========================================================="
exec /usr/bin/supervisord -c /etc/supervisord.conf
