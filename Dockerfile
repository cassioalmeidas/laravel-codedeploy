FROM php:7.4-apache

WORKDIR /var/www/html

# Install composer and its dependencies
RUN apt update
RUN apt install -y git zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP modules
RUN docker-php-ext-install mysqli pdo_mysql

# Enable apache mod rewrite
RUN a2enmod rewrite

# Copy Apache configuration
COPY docker/web/000-default.conf /etc/apache2/sites-available/000-default.conf

# Build source codes
COPY www .
RUN composer install

EXPOSE 80