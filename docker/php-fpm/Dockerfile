FROM php:7.2-fpm

RUN apt-get update

# Install Composer
RUN apt-get install -y --no-install-recommends git zip unzip apt-utils
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

# Suppress warnings from Composer running in Docker
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY entrypoint.sh /
ENTRYPOINT ["/entrypoint.sh"]

CMD ["php-fpm"]
