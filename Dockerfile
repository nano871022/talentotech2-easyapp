# Use an official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies for Node.js and other tools
RUN apt-get update && apt-get install -y \
    curl \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Angular CLI globally
RUN npm install -g @angular/cli

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the entire project into the container
COPY . /var/www/html/

# Install frontend dependencies and build the Angular app, replicating the deploy.yaml process
RUN npm install --prefix frontend && npm run build --prefix frontend -- --base-href /static/

# Expose port 80 to the outside world
EXPOSE 80

# The default command for the php:apache image is to start Apache
# CMD ["apache2-foreground"]