# 1. Imagen base: PHP 8.3 con Apache ya configurado (como el "Apache + PHP" de XAMPP)
FROM php:8.3-apache

# 2. Instalamos la extensión para conectarnos a MySQL (en XAMPP ya viene incluida)
RUN docker-php-ext-install pdo_mysql

# 3. Copiamos nuestro código a la carpeta pública de Apache (el "htdocs" del contenedor)
COPY app/ /var/www/html/

# 4. Documentamos que el contenedor escucha en el puerto 80
EXPOSE 80
