#!/bin/bash
set -e

php artisan app:setup
php artisan optimize

apache2-foreground
