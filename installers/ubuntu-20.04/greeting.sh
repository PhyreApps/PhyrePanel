#!/bin/bash

CURRENT_IP=$(curl -s ipinfo.io/ip)

echo " \
  ____  _   ___   ______  _____   ____   _    _   _ _____ _
 |  _ \| | | \ \ / /  _ \| ____| |  _ \ / \  | \ | | ____| |
 | |_) | |_| |\ V /| |_) |  _|   | |_) / _ \ |  \| |  _| | |
 |  __/|  _  | | | |  _ <| |___  |  __/ ___ \| |\  | |___| |___
 |_|   |_| |_| |_| |_| \_\_____| |_| /_/   \_\_| \_|_____|_____
 WELCOME TO PHYRE PANEL!
 OS: Ubuntu 20.04
 You can login at: http://$CURRENT_IP:8443
"

# File can be saved at: /etc/profile.d/greeting.sh
