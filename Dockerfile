# Use an official PHP-FPM image as a base
FROM php:8.2-fpm

# Set the working directory in the container
WORKDIR /var/www

# Install necessary PHP extensions
# pdo_mysql for database connectivity
# opcache for performance
RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable opcache

# Copy the backend application files into the container
# This assumes the Docker build context is the project root
COPY backend/ /var/www/

# Set permissions for the application files
# The user www-data is the default user for PHP-FPM
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www

# Expose the port on which PHP-FPM listens
EXPOSE 9000

# The default command for the php-fpm image is to start the FPM server
CMD ["php-fpm"]