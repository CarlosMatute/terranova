FROM php:8.1-apache

RUN apt-get update && apt-get install -y libpq-dev unzip libzip-dev && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_pgsql zip \
    && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html
WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env \
    && php artisan key:generate \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN echo "Listen \${PORT}" > /etc/apache2/ports.conf \
    && printf "<VirtualHost *:\${PORT}>\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        Options Indexes FollowSymLinks\n        AllowOverride All\n        Require all granted\n    </Directory>\n    ErrorLog \${APACHE_LOG_DIR}/error.log\n    CustomLog \${APACHE_LOG_DIR}/access.log combined\n</VirtualHost>\n" > /etc/apache2/sites-available/000-default.conf

ENTRYPOINT ["/entrypoint.sh"]
