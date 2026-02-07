#!/bin/sh
set -e

echo "🚀 Starting Laravel Application..."

# Copy public assets to shared volume if not populated
if [ ! -f "/var/www/html/public/index.php" ]; then
    echo "📂 Populating public assets volume..."
    cp -r /var/www/html/public_build/* /var/www/html/public/ 2>/dev/null || true
fi

# Wait for database to be ready (handled by docker-compose healthcheck, but double-check)
echo "⏳ Waiting for database connection..."
sleep 2

# Run Laravel optimizations
echo "🔧 Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations if needed (only in production with MIGRATE_ON_START=true)
if [ "$MIGRATE_ON_START" = "true" ]; then
    echo "📦 Running database migrations..."
    php artisan migrate --force
fi

echo "✅ Application ready!"

# Execute the main command
exec "$@"
