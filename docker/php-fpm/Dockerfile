FROM php:7.3-fpm

RUN apt-get update
# RUN apt-get install -y mysql-client
RUN docker-php-ext-install -j$(nproc) pdo_mysql

COPY entrypoint.sh /
COPY configure-xdebug /usr/local/bin/
COPY xdebug.ini /usr/local/etc/php/conf.d/

RUN pecl install xdebug-beta

RUN apt-get install -y --no-install-recommends git zip unzip apt-utils
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

# Suppress warnings from Composer running in Docker
ENV COMPOSER_ALLOW_SUPERUSER=1

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php-fpm"]
