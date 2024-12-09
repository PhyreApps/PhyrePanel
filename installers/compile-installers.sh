# Compile ubuntu-20.04 installers

# get content from file
INSTALL_BASE=$(cat ubuntu-20.04/install-partial/install_base.sh)
INSTALL_LOG=$(cat ubuntu-20.04/install-partial/install_log.sh)
DOWNLOAD_WEB=$(cat ubuntu-20.04/install-partial/download_web.sh)
INSTALL_WEB=$(cat ubuntu-20.04/install-partial/install_web.sh)

# create installer
rm -rf ubuntu-20.04/install.sh
echo "$INSTALL_BASE" >> ubuntu-20.04/install.sh
echo "$INSTALL_LOG" >> ubuntu-20.04/install.sh
echo "$DOWNLOAD_WEB" >> ubuntu-20.04/install.sh
echo "$INSTALL_WEB" >> ubuntu-20.04/install.sh


# Compile ubuntu-22.04 installers

# get content from file
INSTALL_BASE=$(cat ubuntu-22.04/install-partial/install_base.sh)
INSTALL_LOG=$(cat ubuntu-20.04/install-partial/install_log.sh)
DOWNLOAD_WEB=$(cat ubuntu-20.04/install-partial/download_web.sh)
INSTALL_WEB=$(cat ubuntu-20.04/install-partial/install_web.sh)

# create installer
rm -rf ubuntu-22.04/install.sh
echo "$INSTALL_BASE" >> ubuntu-22.04/install.sh
echo "$INSTALL_LOG" >> ubuntu-22.04/install.sh
echo "$DOWNLOAD_WEB" >> ubuntu-22.04/install.sh
echo "$INSTALL_WEB" >> ubuntu-22.04/install.sh
