#!/bin/bash
set -e

chown -R www-data:www-data /var/log

configure-xdebug
cp -n .env.local .env
composer install
php artisan migrate
exec $1
