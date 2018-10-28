FROM php:5.6-apache

COPY vhost.conf /etc/apache2/sites-available/app.conf
RUN a2enmod rewrite
RUN a2dissite 000-default.conf && a2ensite app.conf
