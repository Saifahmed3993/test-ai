FROM php:8.1-apache

# تثبيت امتدادات PDO MySQL و MySQLi
RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
