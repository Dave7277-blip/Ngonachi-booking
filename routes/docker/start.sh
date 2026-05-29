#!/bin/sh

# ── Generate app key if not set ──────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "No APP_KEY found — generating one..."
    php artisan key:generate --force
fi

# ── Run migrations ───────────────────────────────────────
echo "Running migrations..."
php artisan migrate --force

# ── Seed database (only if packages table is empty) ──────
PACKAGE_COUNT=$(php artisan tinker --execute="echo App\Models\Package::count();" 2>/dev/null || echo "0")
if [ "$PACKAGE_COUNT" = "0" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

# ── Cache config, routes, views ──────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Create storage symlink ───────────────────────────────
php artisan storage:link

# ── Start PHP-FPM in background ──────────────────────────
php-fpm -D

# ── Start Nginx in foreground ────────────────────────────
echo "Starting Nginx on port 8000..."
nginx -g "daemon off;"