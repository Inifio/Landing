#!/bin/bash

cp -n .env.local .env

composer install

php artisan migrate

exec $1
