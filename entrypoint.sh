#!/bin/bash
set -e

php artisan key:generate --force
php artisan app:setup
php artisan optimize

apache2-foreground
