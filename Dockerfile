FROM php:8.2-apache

# Enable Apache rewrite module (REQUIRED for clean URLs)
RUN a2enmod rewrite

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Install MySQL extension
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

# Optional but good
RUN docker-php-ext-install pdo pdo_mysql
