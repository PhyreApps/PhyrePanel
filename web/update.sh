PHYRE_PHP=/usr/local/phyre/php/bin/php

rm -rf /usr/local/phyre/update/web
mkdir -p /usr/local/phyre/update/web

rm -rf /usr/local/phyre/update/phyre-web-panel.zip
wget https://github.com/PhyreApps/PhyrePanelWebCompiledVersions/raw/refs/heads/main/phyre-web-panel.zip -O /usr/local/phyre/update/phyre-web-panel.zip

unzip /usr/local/phyre/update/phyre-web-panel.zip -d /usr/local/phyre/update/web

rm -rf /usr/local/phyre/web/app
rm -rf /usr/local/phyre/web/Modules
rm -rf /usr/local/phyre/web/bootstrap
rm -rf /usr/local/phyre/web/config
rm -rf /usr/local/phyre/web/database
rm -rf /usr/local/phyre/web/public
rm -rf /usr/local/phyre/web/resources
rm -rf /usr/local/phyre/web/routes
rm -rf /usr/local/phyre/web/tests
rm -rf /usr/local/phyre/web/vendor
rm -rf /usr/local/phyre/web/composer.json
rm -rf /usr/local/phyre/web/composer.lock
rm -rf /usr/local/phyre/web/package.json

cp -r /usr/local/phyre/update/web/app /usr/local/phyre/web/app
cp -r /usr/local/phyre/update/web/Modules /usr/local/phyre/web/Modules
cp -r /usr/local/phyre/update/web/bootstrap /usr/local/phyre/web/bootstrap
cp -r /usr/local/phyre/update/web/config /usr/local/phyre/web/config
cp -r /usr/local/phyre/update/web/database /usr/local/phyre/web/database
cp -r /usr/local/phyre/update/web/public /usr/local/phyre/web/public
cp -r /usr/local/phyre/update/web/resources /usr/local/phyre/web/resources
cp -r /usr/local/phyre/update/web/routes /usr/local/phyre/web/routes
cp -r /usr/local/phyre/update/web/tests /usr/local/phyre/web/tests
cp -r /usr/local/phyre/update/web/vendor /usr/local/phyre/web/vendor
cp /usr/local/phyre/update/web/composer.json /usr/local/phyre/web/composer.json
cp /usr/local/phyre/update/web/composer.lock /usr/local/phyre/web/composer.lock
cp /usr/local/phyre/update/web/package.json /usr/local/phyre/web/package.json



systemctl stop phyre
apt remove phyre-nginx -y

OS=$(lsb_release -si)
OS_LOWER=$(echo $OS | tr '[:upper:]' '[:lower:]')
OS_VERSION=$(lsb_release -sr)

rm -rf /usr/local/phyre/update/nginx
mkdir -p /usr/local/phyre/update/nginx
wget https://github.com/PhyreApps/PhyrePanelNGINX/raw/main/compilators/debian/nginx/dist/phyre-nginx-1.24.0-$OS_LOWER-$OS_VERSION.deb -O /usr/local/phyre/update/nginx/phyre-nginx-1.24.0-$OS_LOWER-$OS_VERSION.deb
dpkg -i /usr/local/phyre/update/nginx/phyre-nginx-1.24.0-$OS_LOWER-$OS_VERSION.deb

#
printf "Updating the panel...\n"
wget https://raw.githubusercontent.com/PhyreApps/PhyrePanelNGINX/main/compilators/debian/nginx/nginx.conf -O /usr/local/phyre/nginx/conf/nginx.conf
#
# mkdir -p /usr/local/phyre/ssl
# cp /usr/local/phyre/web/server/ssl/phyre.crt /usr/local/phyre/ssl/phyre.crt
# cp /usr/local/phyre/web/server/ssl/phyre.key /usr/local/phyre/ssl/phyre.key

systemctl restart phyre
#systemctl status phyre

printf "Updating the database...\n"
$PHYRE_PHP /usr/local/phyre/web/artisan migrate
#$PHYRE_PHP artisan l5-swagger:generate
