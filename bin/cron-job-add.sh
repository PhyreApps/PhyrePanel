#!/bin/bash

username=$1
schedule=$2
command=$3

# Create a temporary file to hold the existing user's crontab
crontab -u $username -l > /tmp/temp_crontab

# Add a new cron job to the temporary file
echo "$schedule $command" >> /tmp/temp_crontab

# Install the modified crontab from the temporary file
crontab -u $username /tmp/temp_crontab

# Remove the temporary file
rm /tmp/temp_crontab

echo "done!"
