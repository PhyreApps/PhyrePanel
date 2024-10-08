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

mkdir -p /usr/local/phyre/ssl
cp /usr/local/phyre/web/server/ssl/phyre.crt /usr/local/phyre/ssl/phyre.crt
cp /usr/local/phyre/web/server/ssl/phyre.key /usr/local/phyre/ssl/phyre.key

service phyre start

CURRENT_IP=$(hostname -I | awk '{print $1}')

echo "PhyrePanel downloaded successfully."
echo "Please visit http://$CURRENT_IP:8443 to continue installation of the panel."
