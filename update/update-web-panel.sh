rm -rf /usr/local/phyre/update/web-panel-latest
rm -rf /usr/local/phyre/update/phyre-web-panel.zip

wget https://github.com/PhyreApps/PhyrePanelWebCompiledVersions/raw/main/phyre-web-panel.zip
ls -la
unzip -o phyre-web-panel.zip -d /usr/local/phyre/update/web-panel-latest

rm -rf /usr/local/phyre/web/vendor
rm -rf /usr/local/phyre/web/composer.lock
rm -rf /usr/local/phyre/web/routes
rm -rf /usr/local/phyre/web/public
rm -rf /usr/local/phyre/web/resources
rm -rf /usr/local/phyre/web/database
rm -rf /usr/local/phyre/web/config
rm -rf /usr/local/phyre/web/app
rm -rf /usr/local/phyre/web/bootstrap
rm -rf /usr/local/phyre/web/lang
rm -rf /usr/local/phyre/web/Modules
rm -rf /usr/local/phyre/web/thirdparty

cp -r /usr/local/phyre/update/web-panel-latest/vendor /usr/local/phyre/web/vendor
cp /usr/local/phyre/update/web-panel-latest/composer.lock /usr/local/phyre/web/composer.lock
cp -r /usr/local/phyre/update/web-panel-latest/routes /usr/local/phyre/web/routes
cp -r /usr/local/phyre/update/web-panel-latest/public /usr/local/phyre/web/public
cp -r /usr/local/phyre/update/web-panel-latest/resources /usr/local/phyre/web/resources
cp -r /usr/local/phyre/update/web-panel-latest/database /usr/local/phyre/web/database
cp -r /usr/local/phyre/update/web-panel-latest/config /usr/local/phyre/web/config
cp -r /usr/local/phyre/update/web-panel-latest/app /usr/local/phyre/web/app
cp -r /usr/local/phyre/update/web-panel-latest/bootstrap /usr/local/phyre/web/bootstrap
cp -r /usr/local/phyre/update/web-panel-latest/lang /usr/local/phyre/web/lang
cp -r /usr/local/phyre/update/web-panel-latest/Modules /usr/local/phyre/web/Modules
#cp -r /usr/local/phyre/update/web-panel-latest/thirdparty /usr/local/phyre/web/thirdparty

cp -r /usr/local/phyre/update/web-panel-latest/db-migrate.sh /usr/local/phyre/web/db-migrate.sh
chmod +x /usr/local/phyre/web/db-migrate.sh
#
cd /usr/local/phyre/web
#
#
#
#PHYRE_PHP=/usr/local/phyre/php/bin/php
##
#$PHYRE_PHP -v
#$PHYRE_PHP -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
#$PHYRE_PHP ./composer-setup.php
#$PHYRE_PHP -r "unlink('composer-setup.php');"

#rm -rf composer.lock
#COMPOSER_ALLOW_SUPERUSER=1 $PHYRE_PHP composer.phar i --no-interaction --no-progress
#COMPOSER_ALLOW_SUPERUSER=1 $PHYRE_PHP composer.phar dump-autoload --no-interaction

./db-migrate.sh

service phyre restart
