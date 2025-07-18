FROM php:8.1-apache

# Install required PHP extensions (e.g., mysqli for db.php)
RUN docker-php-ext-install mysqli

# Copy all your files to the Apache web directory
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
