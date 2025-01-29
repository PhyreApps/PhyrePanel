#!/bin/bash

INSTALL_DIR="/phyre/install"

apt-get update && apt-get install ca-certificates

mkdir -p $INSTALL_DIR

cd $INSTALL_DIR

DEPENDENCIES_LIST=(
    "openssl"
    "jq"
    "curl"
    "wget"
    "unzip"
    "zip"
    "tar"
    "mysql-common"
    "mysql-server"
    "mysql-client"
    "lsb-release"
    "gnupg2"
    "ca-certificates"
    "apt-transport-https"
    "software-properties-common"
    "supervisor"
    "libonig-dev"
    "libzip-dev"
    "libcurl4-openssl-dev"
    "libsodium23"
    "libpq5"
    "apache2"
    "libapache2-mod-ruid2"
    "libapache2-mod-php"
    "libssl-dev"
    "zlib1g-dev"
)
# Check if the dependencies are installed
for DEPENDENCY in "${DEPENDENCIES_LIST[@]}"; do
    apt install -yq $DEPENDENCY
done

# Start MySQL
service mysql start

mkdir -p /usr/local/phyre/ssl

wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/refs/heads/main/web/server/ssl/phyre.crt -O /usr/local/phyre/ssl/phyre.crt
wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/refs/heads/main/web/server/ssl/phyre.key -O /usr/local/phyre/ssl/phyre.key

sudo chmod 644 /usr/local/phyre/ssl/phyre.crt
sudo chmod 600 /usr/local/phyre/ssl/phyre.key

wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/installers/ubuntu-22.04/greeting.sh -O /etc/profile.d/phyre-greeting.sh

# Install PHYRE PHP
wget https://github.com/PhyreApps/PhyrePanelPHP/raw/main/compilators/debian/php/dist/phyre-php-8.2.0-ubuntu-22.04.deb
dpkg -i phyre-php-8.2.0-ubuntu-22.04.deb

# Install PHYRE NGINX
wget https://github.com/PhyreApps/PhyrePanelNGINX/raw/main/compilators/debian/nginx/dist/phyre-nginx-1.24.0-ubuntu-22.04.deb
dpkg -i phyre-nginx-1.24.0-ubuntu-22.04.deb

PHYRE_PHP=/usr/local/phyre/php/bin/php

ln -s $PHYRE_PHP /usr/bin/phyre-php

curl -s https://phyrepanel.com/api/phyre-installation-log -X POST -H "Content-Type: application/json" -d '{"os": "ubuntu-22.04"}'
