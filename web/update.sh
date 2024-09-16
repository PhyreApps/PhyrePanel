PHYRE_PHP=/usr/local/phyre/php/bin/php




printf "Updating the database...\n"
$PHYRE_PHP artisan migrate
#$PHYRE_PHP artisan l5-swagger:generate
