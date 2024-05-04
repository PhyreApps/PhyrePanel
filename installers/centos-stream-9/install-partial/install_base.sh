#!/bin/bash

INSTALL_DIR="/phyre/install"

yum update && yum install ca-certificates wget -y

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
    "libssl-dev"
    "zlib1g-dev"
)
# Check if the dependencies are installed
for DEPENDENCY in "${DEPENDENCIES_LIST[@]}"; do
    yum install -y $DEPENDENCY
done

# Start MySQL
service mysql start

wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/installers/centos-stream-9/greeting.sh
mv greeting.sh /etc/profile.d/phyre-greeting.sh

# Install PHYRE NGINX
wget https://github.com/PhyreApps/PhyrePanelNGINX/raw/main/compilators/centos/nginx/dist/phyre-nginx-1.25.5-1.el8.x86_64.rpm
sudo dnf install phyre-nginx-1.25.5-1.el8.x86_64.rpm

## Install PHYRE PHP
#wget https://github.com/PhyreApps/PhyrePanelNGINX/raw/main/compilators/centos/nginx/dist/phyre-nginx-1.25.5-1.el8.x86_64.rpm
#rpm -i phyre-nginx-1.25.5-1.el8.x86_64.rpm

#
#service phyre start
#
#PHYRE_PHP=/usr/local/phyre/php/bin/php
#
#ln -s $PHYRE_PHP /usr/bin/phyre-php
#
#curl -s https://phyrepanel.com/api/phyre-installation-log -X POST -H "Content-Type: application/json" -d '{"os": "ubuntu-22.04"}'
