#!/bin/bash

# Fix Caddy Log Directory Permissions
echo "Fixing Caddy log directory permissions..."

# Create log directory if it doesn't exist
sudo mkdir -p /var/log/caddy

# Set broader permissions (777) to allow write access from any user
sudo chmod 777 /var/log/caddy

# Also ensure the caddy user owns the directory
sudo chown -R caddy:caddy /var/log/caddy

# Set permissions for existing log files to be writable
sudo find /var/log/caddy -name "*.log" -type f -exec chmod 666 {} \;

# Verify the permissions
echo "Current permissions for /var/log/caddy:"
ls -la /var/log/caddy

echo "Log directory permissions fixed!"

# Test write access
sudo -u caddy touch /var/log/caddy/test-write.log
if [ $? -eq 0 ]; then
    echo "✓ Write test successful"
    sudo rm -f /var/log/caddy/test-write.log
else
    echo "✗ Write test failed"
fi
