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

wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/installers/ubuntu-24.04/greeting.sh -O /etc/profile.d/phyre-greeting.sh

# Install PHYRE PHP
wget https://github.com/PhyreApps/PhyrePanelPHP/raw/main/compilators/debian/php/dist/phyre-php-8.2.0-ubuntu-24.04.deb
dpkg -i phyre-php-8.2.0-ubuntu-24.04.deb

# Install PHYRE NGINX
wget https://github.com/PhyreApps/PhyrePanelNGINX/raw/main/compilators/debian/nginx/dist/phyre-nginx-1.24.0-ubuntu-24.04.deb
dpkg -i phyre-nginx-1.24.0-ubuntu-24.04.deb

PHYRE_PHP=/usr/local/phyre/php/bin/php

ln -s $PHYRE_PHP /usr/bin/phyre-php

curl -s https://phyrepanel.com/api/phyre-installation-log -X POST -H "Content-Type: application/json" -d '{"os": "ubuntu-24.04"}'
#!/bin/bash

HOSTNAME=$(hostname)
IP_ADDRESS=$(hostname -I | cut -d " " -f 1)

DISTRO_VERSION=$(cat /etc/os-release | grep -w "VERSION_ID" | cut -d "=" -f 2)
DISTRO_VERSION=${DISTRO_VERSION//\"/} # Remove quotes from version string

DISTRO_NAME=$(cat /etc/os-release | grep -w "NAME" | cut -d "=" -f 2)
DISTRO_NAME=${DISTRO_NAME//\"/} # Remove quotes from name string

LOG_JSON='{"os": "'$DISTRO_NAME-$DISTRO_VERSION'", "host_name": "'$HOSTNAME'", "ip": "'$IP_ADDRESS'"}'

curl -s https://phyrepanel.com/api/phyre-installation-log -X POST -H "Content-Type: application/json" -d "$LOG_JSON"
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


mysql -uroot -proot <<MYSQL_SCRIPT
  SET GLOBAL validate_password.policy = LOW;
  SET GLOBAL validate_password.length = 6;
  SET GLOBAL validate_password.mixed_case_count = 0;
  SET GLOBAL validate_password.number_count = 0;
  SET GLOBAL validate_password.special_char_count = 0;
  FLUSH PRIVILEGES;
MYSQL_SCRIPT

# Create MySQL user
MYSQL_PHYRE_ROOT_USERNAME="phyre"
MYSQL_PHYRE_ROOT_PASSWORD="$(tr -dc a-za-z0-9 </dev/urandom | head -c 32; echo)"

mysql -uroot -proot <<MYSQL_SCRIPT
  CREATE USER '$MYSQL_PHYRE_ROOT_USERNAME'@'%' IDENTIFIED BY '$MYSQL_PHYRE_ROOT_PASSWORD';
  GRANT ALL PRIVILEGES ON *.* TO '$MYSQL_PHYRE_ROOT_USERNAME'@'%' WITH GRANT OPTION;
  FLUSH PRIVILEGES;
MYSQL_SCRIPT


# Create database
PHYRE_PANEL_DB_PASSWORD="$(tr -dc a-za-z0-9 </dev/urandom | head -c 32; echo)"
PHYRE_PANEL_DB_NAME="phyre$(tr -dc a-za-z0-9 </dev/urandom | head -c 13; echo)"
PHYRE_PANEL_DB_USER="phyre$(tr -dc a-za-z0-9 </dev/urandom | head -c 13; echo)"

mysql -uroot -proot <<MYSQL_SCRIPT
  CREATE DATABASE $PHYRE_PANEL_DB_NAME;
  CREATE USER '$PHYRE_PANEL_DB_USER'@'localhost' IDENTIFIED BY '$PHYRE_PANEL_DB_PASSWORD';
  GRANT ALL PRIVILEGES ON $PHYRE_PANEL_DB_NAME.* TO '$PHYRE_PANEL_DB_USER'@'localhost';
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
echo "$MYSQL_ROOT_PASSWORD" > /root/.mysql_root_password

# Configure the application
phyre-php artisan phyre:set-ini-settings APP_ENV "local"
phyre-php artisan phyre:set-ini-settings APP_URL "127.0.0.1:8443"
phyre-php artisan phyre:set-ini-settings APP_NAME "PHYRE_PANEL"
phyre-php artisan phyre:set-ini-settings DB_DATABASE "$PHYRE_PANEL_DB_NAME"
phyre-php artisan phyre:set-ini-settings DB_USERNAME "$PHYRE_PANEL_DB_USER"
phyre-php artisan phyre:set-ini-settings DB_PASSWORD "$PHYRE_PANEL_DB_PASSWORD"
phyre-php artisan phyre:set-ini-settings DB_CONNECTION "mysql"
phyre-php artisan phyre:set-ini-settings MYSQL_ROOT_USERNAME "$MYSQL_PHYRE_ROOT_USERNAME"
phyre-php artisan phyre:set-ini-settings MYSQL_ROOT_PASSWORD "$MYSQL_PHYRE_ROOT_PASSWORD"
phyre-php artisan phyre:key-generate

phyre-php artisan migrate
phyre-php artisan db:seed

phyre-php artisan phyre:set-ini-settings APP_ENV "production"

chmod -R o+w /usr/local/phyre/web/storage/
chmod -R o+w /usr/local/phyre/web/bootstrap/cache/


service phyre start

CURRENT_IP=$(hostname -I | awk '{print $1}')

echo "PhyrePanel downloaded successfully."

# Parse argument --dont-ask
if [ "$1" == "--dont-ask" ]; then
  echo "PhyrePanel is now available at https://$CURRENT_IP:8443"
  exit 0
else
  phyre-php artisan phyre:install-apache
  phyre-php artisan phyre:setup-master-domain-ssl
fi
