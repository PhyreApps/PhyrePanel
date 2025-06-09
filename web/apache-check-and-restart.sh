#!/bin/bash

if ! systemctl is-active --quiet apache2; then
    echo "$(date): Apache is down. Restarting..." >> /var/log/apache_monitor.log
    systemctl enable apache2
    systemctl restart apache2
else
    echo "$(date): Apache is running."
fi
