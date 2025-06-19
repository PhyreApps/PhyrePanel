#!/bin/bash

echo "Installing Caddy Web Server..."

# Update package index
sudo apt update -y

# Install required packages
sudo apt-get install net-tools curl -y


sudo groupadd --system caddy

sudo useradd --system \
    --gid caddy \
    --create-home \
    --home-dir /var/lib/caddy \
    --shell /usr/sbin/nologin \
    --comment "Caddy web server" \
    caddy


# Install Caddy
sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
sudo apt update
sudo apt install caddy -y

# Create Caddy directories
sudo mkdir -p /etc/caddy
sudo mkdir -p /var/log/caddy
sudo mkdir -p /var/lib/caddy

# Create basic Caddyfile
sudo tee /etc/caddy/Caddyfile > /dev/null <<EOF
{
    email admin@localhost
    admin off
}

# Default catch-all
:80 {
    respond "Caddy is running!"
}
EOF

# Set permissions
sudo chown -R caddy:caddy /var/lib/caddy
sudo chown -R caddy:caddy /var/log/caddy
sudo chmod 755 /etc/caddy
sudo chmod 644 /etc/caddy/Caddyfile



usermod -aG www-data caddy

sudo add-apt-repository ppa:longsleep/golang-backports -y
sudo apt update -y
sudo apt install golang-go -y


sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https -y
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/xcaddy/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-xcaddy-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/xcaddy/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-xcaddy.list
sudo apt update -y
sudo apt install xcaddy -y
cd /usr/bin
xcaddy build --with github.com/caddy-dns/cloudflare



sudo systemctl enable caddy
sudo systemctl start caddy

# Check status
sudo systemctl status caddy

echo "Done!"
