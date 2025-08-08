FROM php:8.1-apache

COPY . /var/www/html/
EXPOSE 80

RUN docker-php-ext-install pdo pdo_mysql mysqli
CMD ["apache2-foreground"] 
