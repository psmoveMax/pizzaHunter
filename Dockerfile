# Используем официальный образ PHP с Apache
FROM php:8.0-apache

# Устанавливаем расширения PHP, необходимые для Laravel
RUN docker-php-ext-install pdo_mysql mysqli bcmath

# Включаем модуль Apache Rewrite, необходимый для маршрутизации Laravel
RUN a2enmod rewrite

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию для нашего приложения
WORKDIR /var/www/html

# Копируем исходный код в контейнер
COPY . /var/www/html

# Устанавливаем зависимости Composer
RUN composer install --no-interaction --no-dev --prefer-dist

# Копируем конфигурацию виртуального хоста для Apache (если есть)
# COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Устанавливаем права на директории storage и bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Открываем 80 порт
EXPOSE 80

# Запускаем Apache в фоновом режиме
CMD ["apache2-foreground"]
