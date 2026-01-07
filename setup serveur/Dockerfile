FROM php:8.2-apache

ENV http_proxy="http://cache.univ-pau.fr:3128"
ENV https_proxy="http://cache.univ-pau.fr:3128"

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli

RUN a2enmod rewrite

CMD["apache2-foreground"]