# Usar una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar las extensiones de PHP necesarias
# pdo_mysql para la conexión con MySQL
RUN docker-php-ext-install pdo_mysql

# Habilitar el módulo rewrite de Apache para URLs amigables
RUN a2enmod rewrite

# Copiar el código de la aplicación al directorio web de Apache
COPY . /var/www/html/

# Establecer los permisos correctos para los archivos de la aplicación
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Exponer el puerto 80 para el servidor web Apache
EXPOSE 80