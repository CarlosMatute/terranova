#!/bin/bash
set -e

echo "Waiting for database connection..."
for i in $(seq 1 30); do
    php -r "new PDO('pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null && { echo "Database ready."; break; }
    echo "Attempt $i: database not ready yet..."
    sleep 2
done

php artisan app:setup
php artisan optimize

apache2-foreground
