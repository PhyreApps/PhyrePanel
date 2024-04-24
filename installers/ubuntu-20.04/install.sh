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
#!/bin/bash

wget https://github.com/PhyreApps/PhyrePanelWebCompiledVersions/raw/main/phyre-web-panel.zip
unzip -qq -o phyre-web-panel.zip -d /usr/local/phyre/web
rm -rf phyre-web-panel.zip

chmod 711 /home
chmod -R 750 /usr/local/phyre
#!/bin/bash

# Check dir exists
if [ ! -d "/usr/local/phyre/web" ]; then
  echo "PhyrePanel directory not found."
  return 1
fi

# Go to web directory
cd /usr/local/phyre/web

# Create MySQL user
MYSQL_PHYRE_ROOT_USERNAME="phyre"
MYSQL_PHYRE_ROOT_PASSWORD="$(tr -dc a-za-z0-9 </dev/urandom | head -c 32; echo)"

mysql -uroot -proot <<MYSQL_SCRIPT
  CREATE USER '$MYSQL_PHYRE_ROOT_USERNAME'@'%' IDENTIFIED BY '$MYSQL_PHYRE_ROOT_PASSWORD';
  GRANT ALL PRIVILEGES ON *.* TO '$MYSQL_PHYRE_ROOT_USERNAME'@'%' WITH GRANT OPTION;
  FLUSH PRIVILEGES;
MYSQL_SCRIPT


# Create database
PANEL_DB_PASSWORD="$(tr -dc a-za-z0-9 </dev/urandom | head -c 32; echo)"
PANEL_DB_NAME="phyre$(tr -dc a-za-z0-9 </dev/urandom | head -c 13; echo)"
PANEL_DB_USER="phyre$(tr -dc a-za-z0-9 </dev/urandom | head -c 13; echo)"

mysql -uroot -proot <<MYSQL_SCRIPT
  CREATE DATABASE $PANEL_DB_NAME;
  CREATE USER '$PANEL_DB_USER'@'localhost' IDENTIFIED BY '$PANEL_DB_PASSWORD';
  GRANT ALL PRIVILEGES ON $PANEL_DB_NAME.* TO '$PANEL_DB_USER'@'localhost';
  FLUSH PRIVILEGES;
MYSQL_SCRIPT

mysql_secure_installation --use-default

# Change mysql root password
MYSQL_ROOT_PASSWORD="$(tr -dc a-za-z0-9 </dev/urandom | head -c 32; echo)"
mysql -uroot -proot <<MYSQL_SCRIPT
  ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password by '$MYSQL_ROOT_PASSWORD';
  FLUSH PRIVILEGES;
MYSQL_SCRIPT

# Save mysql root password
echo "$MYSQL_ROOT_PASSWORD" > /root/.phyre_mysql_root_password

# Configure the application
cp .env.example .env

sed -i "s/^APP_URL=.*/APP_URL=127.0.0.1:8443" .env
sed -i "s/^APP_NAME=.*/APP_NAME=PHYRE_PANEL/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$PANEL_DB_NAME/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$PANEL_DB_USER/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$PANEL_DB_PASSWORD/" .env
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env

sed -i "s/^MYSQl_ROOT_USERNAME=.*/MYSQl_ROOT_USERNAME=$MYSQL_ROOT_USERNAME/" .env
sed -i "s/^MYSQL_ROOT_PASSWORD=.*/MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD/" .env

phyre-php artisan key:generate
phyre-php artisan migrate
phyre-php artisan db:seed

chmod -R o+w /usr/local/phyre/web/storage/
chmod -R o+w /usr/local/phyre/web/bootstrap/cache/

CURRENT_IP=$(curl -s ipinfo.io/ip)

echo "PhyrePanel downloaded successfully."
echo "Please visit http://$CURRENT_IP:8443 to continue installation of the panel."
