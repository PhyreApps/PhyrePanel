#!/bin/bash

DOMAIN=$1
USER=$2

# Path to NGINX sites-available directory
SITES_AVAILABLE_DIR="/etc/nginx/sites-available"
SITES_ENABLED_DIR="/etc/nginx/sites-enabled"

# Create the site
SERVER_ROOT="/var/www/$DOMAIN/public_html"

cp -f /usr/local/phyre/samples/ubuntu/nginx.conf.sample $SITES_AVAILABLE_DIR/$DOMAIN.conf
ln -s $SITES_AVAILABLE_DIR/$DOMAIN.conf $SITES_ENABLED_DIR/$DOMAIN.conf

mkdir -p $SERVER_ROOT
chown -R www-data:www-data $SERVER_ROOT

# Replace the domain name in the NGINX config
sed -i "s/%SERVER_NAME%/${DOMAIN}/g" $SITES_AVAILABLE_DIR/$DOMAIN.conf
sed -i "s/%USER%/${USER}/g" $SITES_AVAILABLE_DIR/$DOMAIN.conf

SERVER_ROOT_ESCAPED=$(printf '%s\n' "$SERVER_ROOT" | sed -e 's/[\/&]/\\&/g')
sed -i "s#%SERVER_ROOT%#${SERVER_ROOT_ESCAPED}#g" $SITES_AVAILABLE_DIR/$DOMAIN.conf

cp -f /usr/local/phyre/samples/sample-website-index.html $SERVER_ROOT/index.html
sed -i "s/%DOMAIN%/${DOMAIN}/g" $SERVER_ROOT/index.html


# Reload NGINX
service nginx reload

echo "Created site $DOMAIN"
echo "done!"
