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
    "libssl-dev"
    "zlib1g-dev"
)
# Check if the dependencies are installed
for DEPENDENCY in "${DEPENDENCIES_LIST[@]}"; do
    apt install -y $DEPENDENCY
done

# Start MySQL
service mysql start

wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/installers/ubuntu-20.04/greeting.sh
mv greeting.sh /etc/profile.d/phyre-greeting.sh

# Install PHYRE PHP
wget https://github.com/PhyreApps/PhyrePanelPHP/raw/main/compilators/debian/php/dist/phyre-php-8.2.0-ubuntu-20.04.deb
dpkg -i phyre-php-8.2.0-ubuntu-20.04.deb

# Install PHYRE NGINX
wget https://github.com/PhyreApps/PhyrePanelNGINX/raw/main/compilators/debian/nginx/dist/phyre-nginx-1.24.0-ubuntu-20.04.deb
dpkg -i phyre-nginx-1.24.0-ubuntu-20.04.deb

service phyre start

PHYRE_PHP=/usr/local/phyre/php/bin/php

ln -s $PHYRE_PHP /usr/bin/phyre-php
