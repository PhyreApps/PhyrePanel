#!/bin/bash

echo "Creating MySQL user and database"

PASS=$3
if [ -z "$3" ]; then
PASS=`openssl rand -base64 8`
fi

mysql -u root <<MYSQL_SCRIPT
CREATE DATABASE $1;
CREATE USER '$2'@'localhost' IDENTIFIED BY '$PASS';
GRANT ALL PRIVILEGES ON $1.* TO '$2'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

echo "MySQL user and database created."
echo "Database: $1"
echo "Username: $2"
echo "Password: $PASS"
echo "Success!"

