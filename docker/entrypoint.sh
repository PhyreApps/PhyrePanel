#!/usr/bin/env bash
set -e

# Wait for MySQL to be ready
echo "Waiting for database at $DB_HOST:$DB_PORT..."
while ! nc -z $DB_HOST $DB_PORT; do
  sleep 2
done
echo "Database is up!"

# Configure environment variables
phyre-php artisan phyre:set-ini-settings DB_DATABASE "$DB_DATABASE"
phyre-php artisan phyre:set-ini-settings DB_USERNAME "$DB_USERNAME"
phyre-php artisan phyre:set-ini-settings DB_PASSWORD "$DB_PASSWORD"
phyre-php artisan phyre:set-ini-settings DB_HOST "$DB_HOST"
phyre-php artisan phyre:set-ini-settings DB_CONNECTION "mysql"
phyre-php artisan phyre:set-ini-settings APP_ENV "$APP_ENV"
phyre-php artisan phyre:set-ini-settings APP_URL "$APP_URL"
phyre-php artisan phyre:set-ini-settings APP_NAME "$APP_NAME"

# Generate keys, migrate, and seed
phyre-php artisan phyre:key-generate
phyre-php artisan migrate --force
phyre-php artisan db:seed --force

# Start Nginx in foreground
echo "Starting Nginx..."
exec /usr/sbin/service phyre start && /usr/local/phyre/nginx/sbin/nginx -g "daemon off;"
#exec /usr/sbin/service phyre start

# Start Supervisor to manage all processes
#echo "Starting Supervisor to manage processes..."
#exec /usr/bin/supervisord -c /etc/supervisor/conf.d/phyre-supervisor.conf
