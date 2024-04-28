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
cp .env.example .env

sed -i "s/^APP_URL=.*/APP_URL=127.0.0.1:8443" .env
sed -i "s/^APP_NAME=.*/APP_NAME=PHYRE_PANEL/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$PHYRE_PANEL_DB_NAME/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$PHYRE_PANEL_DB_USER/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$PHYRE_PANEL_DB_PASSWORD/" .env
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env

sed -i "s/^MYSQl_ROOT_USERNAME=.*/MYSQl_ROOT_USERNAME=$MYSQL_PHYRE_ROOT_USERNAME/" .env
sed -i "s/^MYSQL_ROOT_PASSWORD=.*/MYSQL_ROOT_PASSWORD=$MYSQL_PHYRE_ROOT_PASSWORD/" .env

phyre-php artisan key:generate
phyre-php artisan migrate
phyre-php artisan db:seed

chmod -R o+w /usr/local/phyre/web/storage/
chmod -R o+w /usr/local/phyre/web/bootstrap/cache/

CURRENT_IP=$(curl -s ipinfo.io/ip)

echo "PhyrePanel downloaded successfully."
echo "Please visit http://$CURRENT_IP:8443 to continue installation of the panel."
