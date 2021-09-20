#!/usr/bin/env bash

#cd /var/www/

# Perform necessary migrations and commands

#php artisan key:generate
#php artisan queue:table
#php artisan voyager:install
#php artisan migrate # Remove

# Start the services
pecl install redis && docker-php-ext-enable redis
service apache2 restart
#service ssh start
#cron
#apache2ctl -D FOREGROUND
