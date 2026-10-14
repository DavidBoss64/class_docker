#Aqui partimos de la imagen, su nombre_imagen:tag  el tag es la version de php y que traiga apache
FROM php:8.2-apache

#Aca instalamos la extencion de pdo_mysql para que php se pueda comunicar con la base de datos
RUN docker-php-ext-install pdo_mysql

#Ahora copiaremos los archivos de nuestro pc a la imagen 
COPY ./app/ /var/www/html/

#Aqui se escucha el puerto 80 del contenedor
EXPOSE 80