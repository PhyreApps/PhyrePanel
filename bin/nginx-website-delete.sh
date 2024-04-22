#!/bin/bash

# Path to NGINX sites-available directory
SITES_AVAILABLE_DIR="/etc/nginx/sites-available"
SITES_ENABLED_DIR="/etc/nginx/sites-enabled"

# Delete the site
rm -rf $SITES_AVAILABLE_DIR/$1.conf
rm -rf $SITES_ENABLED_DIR/$1.conf
rm -rf /var/www/$1

# Reload NGINX
service nginx reload

echo "Deleted site $1"
echo "done!"
