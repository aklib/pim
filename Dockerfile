FROM php:7.4-fpm

RUN apt-get update && apt-get install -y zlib1g-dev libxml2-dev libonig-dev libicu-dev libz-dev g++ zip unzip git gettext iputils-ping libmemcached-dev && \
    pecl install memcached && \
    docker-php-ext-enable memcached

#RUN pecl install -o -f redis \
#&&  rm -rf /tmp/pear \
#&&  docker-php-ext-enable redis

#RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl json dom mbstring pdo pdo_mysql opcache

COPY ./docker/php-fpm/php.ini-production /usr/local/etc/php/php.ini
COPY ./docker/php-fpm/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./ /usr/share/nginx/html/
COPY ./docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'c31c1e292ad7be5f49291169c0ac8f683499edddcfd4e42232982d0fd193004208a58ff6f353fde0012d35fdd72bc394') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /usr/share/nginx/html
RUN composer install
RUN chmod 777 -R data/

#RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
#USER "1000:1000"
#CMD ["php-fpm"]
