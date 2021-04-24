FROM php:7.4-fpm

RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++ zip unzip git gettext
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN docker-php-ext-install pdo_mysql mysqli

ADD ./php.ini-development /usr/local/etc/php/php.ini

#RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
WORKDIR /usr/share/nginx/html
#USER "1000:1000"
#CMD ["php-fpm"]

