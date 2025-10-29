FROM php:8.2-cli

# تثبيت المكتبات الأساسية اللي GD بيحتاجها
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    zlib1g-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql zip

# نسخ المشروع
COPY . /app
WORKDIR /app

# تثبيت composer وتشغيل المشروع
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --optimize-autoloader --no-scripts --no-interaction

CMD php artisan serve --host=0.0.0.0 --port=$PORT
