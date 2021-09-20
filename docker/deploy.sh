#!/usr/bin/env bash

cd ..

# Correct server configuration

#echo $"ServerName $1" > /etc/apache2/domain.conf

a2enmod rewrite && service apache2 reload

#sed -i $"s/APP_URL/$1/g" ./config/local.yaml.example
#cp ./config/local.yaml.example ./config/local.yaml

# Install PHP extensions, incl. xdebug

apt-get update
apt-get install -y git
apt-get install -y zip
docker-php-ext-install mysqli pdo_mysql bcmath mbstring

pecl install xdebug
docker-php-ext-enable xdebug

service apache2 restart


# Install dependencies

#bash -c "curl -sS https://getcomposer.org/installer | php"

#./composer.phar config -g github-oauth.github.com $2

#chmod -R 777 .
#chmod -R 755 ./docker

#./composer.phar install


# Configure SSH for xdebug

apt-get install -y openssh-server

#echo "root:T653@2eyY@7er6@upX4" | chpasswd
#echo "StrictHostKeyChecking=no" >> /etc/ssh/ssh_config
#echo "PermitRootLogin yes" >> /etc/ssh/sshd_config
service ssh start
#
#echo "Success"
#
#tail -f /dev/null
