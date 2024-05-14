#!/bin/bash

# Check if the user is root
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root. Exiting..."
   exit 1
fi

# Check if the user is running a 64-bit system
if [[ $(uname -m) != "x86_64" ]]; then
    echo "This script must be run on a 64-bit system. Exiting..."
    exit 1
fi

# Check if the user is running a supported shell
if [[ $(echo $SHELL) != "/bin/bash" ]]; then
    echo "This script must be run on a system running Bash. Exiting..."
    exit 1
fi

# Check if the user is running a supported OS
if [[ $(uname -s) != "Linux" ]]; then
    echo "This script must be run on a Linux system. Exiting..."
    exit 1
fi

# Check if the user is running a supported distro
if [[ $(cat /etc/os-release | grep -w "ID_LIKE" | cut -d "=" -f 2) != "debian" ]]; then
    echo "This script must be run on a Debian-based system. Exiting..."
    exit 1
fi

# Check if the user is running a supported distro version
DISTRO_VERSION=$(cat /etc/os-release | grep -w "VERSION_ID" | cut -d "=" -f 2)
DISTRO_VERSION=${DISTRO_VERSION//\"/} # Remove quotes from version string

DISTRO_NAME=$(cat /etc/os-release | grep -w "NAME" | cut -d "=" -f 2)
DISTRO_NAME=${DISTRO_NAME//\"/} # Remove quotes from name string
# Lowercase the distro name
DISTRO_NAME=$(echo $DISTRO_NAME | tr '[:upper:]' '[:lower:]')
# replace spaces
DISTRO_NAME=${DISTRO_NAME// /-}

INSTALLER_URL="https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/installers/${DISTRO_NAME}-${DISTRO_VERSION}/install.sh"

INSTALLER_CONTENT=$(wget ${INSTALLER_URL} 2>&1)
if [[ "$INSTALLER_CONTENT" =~ 404\ Not\ Found ]]; then
    echo "PhyrePanel not supporting this version of distribution"
    echo "Distro: ${DISTRO_NAME} Version: ${DISTRO_VERSION}"
    echo "Exiting..."
    exit 1
fi

# Check is PHYRE is already installed
if [ -d "/usr/local/phyre" ]; then
    echo "PhyrePanel is already installed. Exiting..."
    exit 0
fi

wget $INSTALLER_URL -O ./phyre-installer.sh
chmod +x ./phyre-installer.sh
bash ./phyre-installer.sh
