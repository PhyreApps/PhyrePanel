PHYRE_PHP=/usr/local/phyre/php/bin/php



printf "Updating the panel...\n"
wget https://raw.githubusercontent.com/PhyreApps/PhyrePanelNGINX/main/compilators/debian/nginx/nginx.conf -O /usr/local/phyre/nginx/conf/nginx.conf

mkdir -p /usr/local/phyre/ssl
cp /usr/local/phyre/web/server/ssl/phyre.crt /usr/local/phyre/ssl/phyre.crt
cp /usr/local/phyre/web/server/ssl/phyre.key /usr/local/phyre/ssl/phyre.key

systemctl restart phyre
systemctl status phyre

printf "Updating the database...\n"
$PHYRE_PHP artisan migrate
#$PHYRE_PHP artisan l5-swagger:generate
