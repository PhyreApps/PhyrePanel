# syntax=docker/dockerfile:1

FROM ubuntu:22.04
LABEL maintainer="CloudVisionApps1"

# Install initial dependencies
RUN apt-get update \
    && apt-get install -y wget dos2unix ca-certificates \
    && apt-get install -y \
        jq \
        curl \
        wget \
        unzip \
        zip \
        tar \
        mysql-common \
        mysql-server \
        mysql-client \
        lsb-release \
        gnupg2 \
        ca-certificates \
        apt-transport-https \
        software-properties-common \
        supervisor \
        libonig-dev \
        libsodium23 \
        libpq5 \
        libzip-dev \
        libcurl4-openssl-dev \
        libssl-dev \
        zlib1g-dev

# Start MySQL service
RUN service mysql start

# Set up environment variables
ENV INSTALL_DIR="/phyre/install" \
    MYSQL_PHYRE_ROOT_USERNAME="phyre" \
    PHYRE_PHP="/usr/local/phyre/php/bin/php"

# Create installation directory
RUN mkdir -p $INSTALL_DIR

# Change directory to installation directory
WORKDIR $INSTALL_DIR

# Install PHYRE PHP
RUN wget https://github.com/CloudVisionApps/PhyrePanelPHPDist/raw/main/debian/php/dist/phyre-php-8.2.0.deb \
    && dpkg -i phyre-php-8.2.0.deb

# Install PHYRE NGINX
RUN wget https://github.com/CloudVisionApps/PhyrePanelNginxDist/raw/main/debian/nginx/dist/phyre-nginx-1.24.0.deb \
    && dpkg -i phyre-nginx-1.24.0.deb

# Start PHYRE service
RUN service phyre start

# Create symbolic link for PHYRE PHP
RUN ln -s $PHYRE_PHP /usr/bin/phyre-php

# Set permissions
RUN chmod 711 /home \
    && chmod -R 750 /usr/local/phyre

# Change directory to web directory
WORKDIR /usr/local/phyre/web

# Create MySQL user
RUN mysql -uroot -proot -e "CREATE USER '$MYSQL_PHYRE_ROOT_USERNAME'@'%' IDENTIFIED BY '$MYSQL_PHYRE_ROOT_PASSWORD'; \
    GRANT ALL PRIVILEGES ON *.* TO '$MYSQL_PHYRE_ROOT_USERNAME'@'%' WITH GRANT OPTION; \
    FLUSH PRIVILEGES;"

# Create database
RUN PANEL_DB_PASSWORD="$(tr -dc a-za-z0-9 </dev/urandom | head -c 32; echo)" \
    && PANEL_DB_NAME="phyre$(tr -dc a-za-z0-9 </dev/urandom | head -c 13; echo)" \
    && PANEL_DB_USER="phyre$(tr -dc a-za-z0-9 </dev/urandom | head -c 13; echo)" \
    && mysql -uroot -proot -e "CREATE DATABASE $PANEL_DB_NAME; \
    CREATE USER '$PANEL_DB_USER'@'localhost' IDENTIFIED BY '$PANEL_DB_PASSWORD'; \
    GRANT ALL PRIVILEGES ON $PANEL_DB_NAME.* TO '$PANEL_DB_USER'@'localhost'; \
    FLUSH PRIVILEGES;"

# Secure MySQL installation
RUN mysql_secure_installation --use-default

# Change MySQL root password
RUN MYSQL_ROOT_PASSWORD="$(tr -dc a-za-z0-9 </dev/urandom | head -c 32; echo)" \
    && mysql -uroot -proot -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password by '$MYSQL_ROOT_PASSWORD'; \
    FLUSH PRIVILEGES;"

# Save MySQL root password
RUN echo "$MYSQL_ROOT_PASSWORD" > /root/.phyre_mysql_root_password

# Configure the application
RUN cp .env.example .env \
    && sed -i "s/^APP_URL=.*/APP_URL=http://127.0.0.1:8443/" .env \
    && sed -i "s/^APP_NAME=.*/APP_NAME=PHYRE_PANEL/" .env \
    && sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$PANEL_DB_NAME/" .env \
    && sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$PANEL_DB_USER/" .env \
    && sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$PANEL_DB_PASSWORD/" .env \
    && sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env \
    && sed -i "s/^MYSQl_ROOT_USERNAME=.*/MYSQl_ROOT_USERNAME=$MYSQL_ROOT_USERNAME/" .env \
    && sed -i "s/^MYSQL_ROOT_PASSWORD=.*/MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD/" .env

# Generate application key and migrate database
RUN phyre-php artisan key:generate \
    && phyre-php artisan migrate \
    && phyre-php artisan db:seed

# Set permissions for storage and cache directories
RUN chmod -R o+w /usr/local/phyre/web/storage/ \
    && chmod -R o+w /usr/local/phyre/web/bootstrap/cache/

# Get current IP address
RUN CURRENT_IP=$(curl -s ipinfo.io/ip) \
    && echo "PhyrePanel downloaded successfully." \
    && echo "Please visit http://$CURRENT_IP:8443 to continue installation of the panel."
