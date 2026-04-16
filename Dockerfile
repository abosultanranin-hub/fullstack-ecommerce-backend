FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean

RUN a2enmod rewrite
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN echo "APP_KEY=base64:$(openssl rand -base64 32)" > .env
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN rm -f .env
RUN rm -f bootstrap/cache/config.php bootstrap/cache/packages.php bootstrap/cache/services.php

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN printf '<Directory /var/www/html/public>\n    AllowOverride All\n</Directory>\n' >> /etc/apache2/apache2.conf

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
RUN touch storage/logs/laravel.log
RUN chmod -R 777 storage bootstrap/cache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD bash -c "\
  rm -f /var/www/html/bootstrap/cache/config.php && \
  rm -f /var/www/html/bootstrap/cache/packages.php && \
  rm -f /var/www/html/bootstrap/cache/services.php && \
  env | grep -E '^(APP_|DB_|SESSION_|SANCTUM_|FRONTEND_|DATABASE_|MAIL_|CACHE_|QUEUE_|LOG_|STRIPE_|PAYPAL_)' > /var/www/html/.env && \
  echo '' >> /var/www/html/.env && \
  chmod -R 777 /var/www/html/storage && \
  php artisan config:clear 2>&1 || true && \
  php artisan migrate --force 2>&1 || true && \
  php artisan db:seed --force 2>&1 || true && \
  php artisan storage:link 2>&1 || true && \
  apache2-foreground"
