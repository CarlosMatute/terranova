#!/bin/bash
set -e

# Fix APP_KEY: ensure it's a valid base64:... key for AES-256-CBC
APP_KEY=$(php artisan key:generate --show)
export APP_KEY

# Set APP_URL from Render's built-in env var (adapts to any Render URL)
if [ -n "$RENDER_EXTERNAL_URL" ]; then
    APP_URL="$RENDER_EXTERNAL_URL"
    export APP_URL
fi

echo "Waiting for database connection..."
for i in $(seq 1 30); do
    php artisan app:setup 2>&1 && { echo "Database ready & setup complete."; break; }
    echo "Attempt $i: database not ready yet..."
    sleep 2
done

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Keep DB alive (free Render PostgreSQL sleeps after 15 min)
while true; do php artisan app:keep-alive 2>/dev/null; sleep 300; done &

exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
