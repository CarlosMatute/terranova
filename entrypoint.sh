#!/bin/bash
set -e

echo "Waiting for database connection..."
for i in $(seq 1 30); do
    php artisan app:setup 2>&1 && { echo "Database ready & setup complete."; break; }
    echo "Attempt $i: database not ready yet..."
    sleep 2
done

php artisan optimize

apache2-foreground
