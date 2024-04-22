#!/bin/bash

# Path to NGINX sites-available directory
sites_available_dir="/etc/nginx/sites-available"

# Array to hold site configurations
declare -a sites_array=()

# Loop through NGINX site configuration files and collect data
for file in "$sites_available_dir"/*; do
    if [ -f "$file" ] && [ "$(basename "$file")" != "default" ]; then
        server_name=$(awk '$1 == "server_name" {gsub(/;/, "", $2); print $2; exit}' "$file")
        root=$(awk '$1 == "root" {gsub(/;/, "", $2); print $2; exit}' "$file")

        # Append site data to the array
        sites_array+=("{\"file\": \"$file\", \"server_name\": \"$server_name\", \"root\": \"$root\"}")
    fi
done

# Convert array to JSON
json_output=$(printf '%s\n' "${sites_array[@]}" | jq -s '.')

# Output JSON
echo "$json_output"
