#!/bin/bash

cp -n .env.local .env

composer install

exec $1
