#!/bin/bash

# Replace 'username' with the actual username you want to retrieve cron jobs for
username=$1

# Get the user's crontab entries and convert them to JSON
crontab -u $username -l | grep -v -e '^#' -e '^\s*$' | awk '{print "{\"schedule\":\"" $1 " " $2 " " $3 " " $4 " " $5 "\", \"command\":\"" substr($0, index($0,$6)) "\"}"}' | jq -s .
