FROM php:8.3.33-fpm

WORKDIR /var/www/html
    RUN apt-get update \
        && apt-get install -y unzip libzip-dev \
        && rm -rf /var/lib/apt/lists/*
    RUN docker-php-ext-install pdo pdo_mysql zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
    RUN composer install --no-dev --optimize-autoloader
COPY . .

EXPOSE 9000

CMD ["php-fpm"]