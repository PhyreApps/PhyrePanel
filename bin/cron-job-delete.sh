#!/bin/bash

username=$1
schedule=$2
command=$3

# Get the user's crontab, filter out the specific command and schedule, and install the updated crontab
crontab -u $username -l | grep -v -F "$schedule $command" | crontab -u $username -

echo "done!"
