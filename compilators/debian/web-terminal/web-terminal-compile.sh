#!/bin/bash

MAIN_DIR=$(pwd)

# Install dependencies
sudo apt-get update -y
sudo apt-get install -y build-essential dpkg-dev debhelper autotools-dev libgeoip-dev libssl-dev libpcre3-dev zlib1g-dev

sudo apt-get install -y npm nodejs

# Package main dir is the path of the clean debian package
# In PACKAGE_MAIN_DIR must exist only the directories that will be copied to the final debian package

sudo mkdir $MAIN_DIR/phyre-web-terminal-0.0.1
PACKAGE_MAIN_DIR=$MAIN_DIR/phyre-web-terminal-0.0.1

# Create debian package directories
sudo mkdir -p $PACKAGE_MAIN_DIR/DEBIAN
sudo mkdir -p $PACKAGE_MAIN_DIR/usr/local/phyre/web-terminal

# Copy web-terminal files
sudo cp $MAIN_DIR/.eslintrc.cjs $PACKAGE_MAIN_DIR/usr/local/phyre/web-terminal
sudo cp $MAIN_DIR/server.js $PACKAGE_MAIN_DIR/usr/local/phyre/web-terminal
sudo cp $MAIN_DIR/package.json $PACKAGE_MAIN_DIR/usr/local/phyre/web-terminal

cd $PACKAGE_MAIN_DIR/usr/local/phyre/web-terminal
sudo chmod +x $PACKAGE_MAIN_DIR/usr/local/phyre/web-terminal/server.js

# Compile web-terminal
cd $PACKAGE_MAIN_DIR/usr/local/phyre/web-terminal
sudo npm install

# Copy debian package META file
sudo cp $MAIN_DIR/control $PACKAGE_MAIN_DIR/DEBIAN
sudo cp $MAIN_DIR/postinst $PACKAGE_MAIN_DIR/DEBIAN
sudo cp $MAIN_DIR/postrm $PACKAGE_MAIN_DIR/DEBIAN

# Set debian package post files permissions
sudo chmod +x $PACKAGE_MAIN_DIR/DEBIAN/postinst
sudo chmod +x $PACKAGE_MAIN_DIR/DEBIAN/postrm


# Make debian package
sudo dpkg-deb --build $PACKAGE_MAIN_DIR
sudo dpkg --info $MAIN_DIR/phyre-web-terminal-0.0.1.deb
sudo dpkg --contents $MAIN_DIR/phyre-web-terminal-0.0.1.deb

# Move debian package to dist folder
sudo mkdir -p $MAIN_DIR/dist
sudo mv $MAIN_DIR/phyre-web-terminal-0.0.1.deb $MAIN_DIR/dist
