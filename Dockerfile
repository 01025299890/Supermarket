FROM php:8.2-cli

# تثبيت الامتدادات المطلوبة
RUN docker-php-ext-install gd pdo pdo_mysql

# نسخ المشروع
COPY . /app
WORKDIR /app

# تثبيت composer وتشغيل المشروع
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --optimize-autoloader --no-scripts --no-interaction

CMD php artisan serve --host=0.0.0.0 --port=$PORT
