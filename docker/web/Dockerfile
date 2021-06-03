FROM php:7.4-apache

# Install composer and its dependencies
RUN apt update
RUN apt install -y git zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP modules
RUN docker-php-ext-install mysqli pdo_mysql

# Enable apache mod rewrite
RUN a2enmod rewrite

# Copy Apache configuration
COPY dev.conf /etc/apache2/sites-available/dev.conf