#!/bin/bash

CURRENT_IP=$(hostname -I | awk '{print $1}')

echo " \
  ____  _   ___   ______  _____   ____   _    _   _ _____ _
 |  _ \| | | \ \ / /  _ \| ____| |  _ \ / \  | \ | | ____| |
 | |_) | |_| |\ V /| |_) |  _|   | |_) / _ \ |  \| |  _| | |
 |  __/|  _  | | | |  _ <| |___  |  __/ ___ \| |\  | |___| |___
 |_|   |_| |_| |_| |_| \_\_____| |_| /_/   \_\_| \_|_____|_____
 WELCOME TO PHYRE PANEL!
 OS: Ubuntu 22.04
 You can login at: https://$CURRENT_IP:8443
"

# File can be saved at: /etc/profile.d/greeting.sh
