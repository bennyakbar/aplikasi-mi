#!/bin/sh
set -e

echo "🚀 Starting TU App Laravel Application..."
echo "============================================"

# Function to fix permissions
fix_permissions() {
    echo "🔐 Fixing storage permissions..."
    # Create directories if they don't exist
    mkdir -p /var/www/html/storage/framework/{cache,sessions,views}
    mkdir -p /var/www/html/storage/logs
    mkdir -p /var/www/html/bootstrap/cache
    
    # Set permissions (these directories are mounted as volumes)
    chmod -R 777 /var/www/html/storage 2>/dev/null || true
    chmod -R 777 /var/www/html/bootstrap/cache 2>/dev/null || true
    
    # Create log file if it doesn't exist
    touch /var/www/html/storage/logs/laravel.log 2>/dev/null || true
    chmod 666 /var/www/html/storage/logs/laravel.log 2>/dev/null || true
}

# Always fix permissions first
fix_permissions

# Copy public assets to shared volume - ALWAYS sync to ensure latest assets
echo "📂 Synchronizing public assets..."
if [ -d "/var/www/html/public_build" ]; then
    cp -rf /var/www/html/public_build/* /var/www/html/public/ 2>/dev/null || true
    echo "✅ Public assets synchronized"
fi

# Wait for database to be ready (docker-compose healthcheck ensures it's up, but we double-check)
echo "⏳ Waiting for database connection..."
max_attempts=15
attempt=1
while [ $attempt -le $max_attempts ]; do
    # Simple PHP connection test
    if php -r "try { new PDO('pgsql:host=db;dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'OK'; exit(0); } catch(Exception \$e) { exit(1); }" 2>/dev/null; then
        echo "✅ Database connection successful"
        break
    fi
    echo "   Attempt $attempt/$max_attempts - waiting..."
    sleep 2
    attempt=$((attempt + 1))
done

if [ $attempt -gt $max_attempts ]; then
    echo "⚠️  Warning: Could not verify database connection, continuing anyway..."
fi

# Run Laravel optimizations
echo "🔧 Optimizing Laravel..."
php artisan config:cache 2>/dev/null || echo "⚠️  Config cache failed, continuing..."
php artisan route:cache 2>/dev/null || echo "⚠️  Route cache failed, continuing..."
php artisan view:cache 2>/dev/null || echo "⚠️  View cache failed, continuing..."

# Run migrations if needed
if [ "$MIGRATE_ON_START" = "true" ]; then
    echo "📦 Running database migrations..."
    php artisan migrate --force 2>/dev/null || echo "⚠️  Migration failed or already up-to-date"
fi

# Seed database if requested (first-time setup)
if [ "$SEED_ON_START" = "true" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force 2>/dev/null || echo "⚠️  Seeding failed or already seeded"
fi

echo "============================================"
echo "✅ TU App is ready!"
echo "============================================"

# Execute the main command
exec "$@"
