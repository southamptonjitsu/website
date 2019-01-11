FROM php:7.2-apache

RUN apt-get update -y && apt-get install -y git gnupg unzip zip zlib1g-dev

RUN docker-php-ext-install zip

COPY vhost.conf /etc/apache2/sites-available/app.conf
RUN a2enmod rewrite
RUN a2dissite 000-default.conf && a2ensite app.conf

COPY . /var/www/html
