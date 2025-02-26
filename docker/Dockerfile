# --------------------------------------------------
# 1) Base Image
# --------------------------------------------------
FROM ubuntu:24.04

# So apt doesn't prompt us
ENV DEBIAN_FRONTEND=noninteractive

# --------------------------------------------------
# 2) Install base dependencies
# --------------------------------------------------
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      ca-certificates \
      wget \
      curl \
      netcat \
      mysql-client \
      unzip \
      gnupg2 \
      lsb-release \
      apt-transport-https \
      software-properties-common \
      libpng-dev \
      libwebp-dev \
      libjpeg-turbo8 \
      libfreetype6 \
      supervisor && \
    rm -rf /var/lib/apt/lists/*

# --------------------------------------------------
# 3) Install Phyre .deb packages
# --------------------------------------------------
# 3.1 - Add any system libs needed by Phyre (from your script):
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      openssl \
      libonig-dev \
      libzip-dev \
      libcurl4-openssl-dev \
      libsodium23 \
      libpq5 \
      libssl-dev \
      zlib1g-dev && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /phyre/install

# 3.2 - Install Phyre PHP
RUN wget -q https://github.com/PhyreApps/PhyrePanelPHP/raw/main/compilators/debian/php/dist/phyre-php-8.2.0-ubuntu-22.04.deb && \
    dpkg -i phyre-php-8.2.0-ubuntu-22.04.deb || (apt-get install -f -y && dpkg -i phyre-php-8.2.0-ubuntu-22.04.deb) && \
    rm -f phyre-php-8.2.0-ubuntu-22.04.deb

# 3.3 - Install Phyre NGINX
RUN wget -q https://github.com/PhyreApps/PhyrePanelNGINX/raw/main/compilators/debian/nginx/dist/phyre-nginx-1.24.0-ubuntu-22.04.deb && \
    dpkg -i phyre-nginx-1.24.0-ubuntu-22.04.deb || (apt-get install -f -y && dpkg -i phyre-nginx-1.24.0-ubuntu-22.04.deb) && \
    rm -f phyre-nginx-1.24.0-ubuntu-22.04.deb && \
    ln -s /usr/local/phyre/nginx/sbin/phyre-nginx /usr/local/phyre/nginx/sbin/nginx

# --------------------------------------------------
# 4) Symlink & SSL setup
# --------------------------------------------------
RUN ln -s /usr/local/phyre/php/bin/php /usr/bin/phyre-php

RUN mkdir -p /usr/local/phyre/ssl && \
    wget -q https://raw.githubusercontent.com/PhyreApps/PhyrePanel/refs/heads/main/web/server/ssl/phyre.crt -O /usr/local/phyre/ssl/phyre.crt && \
    wget -q https://raw.githubusercontent.com/PhyreApps/PhyrePanel/refs/heads/main/web/server/ssl/phyre.key -O /usr/local/phyre/ssl/phyre.key && \
    chmod 644 /usr/local/phyre/ssl/phyre.crt && \
    chmod 600 /usr/local/phyre/ssl/phyre.key

# --------------------------------------------------
# 5) Download Phyre Web Panel
# --------------------------------------------------
RUN wget -q https://github.com/PhyreApps/PhyrePanelWebCompiledVersions/raw/main/phyre-web-panel.zip && \
    unzip -qq -o phyre-web-panel.zip -d /usr/local/phyre/web && \
    rm -rf phyre-web-panel.zip

# --------------------------------------------------
# 6) Add a custom entrypoint script
#    that will configure Phyre and run Nginx in foreground
# --------------------------------------------------
WORKDIR /usr/local/phyre/web

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Supervisord config (optional if you want to manage multiple processes)
# (But here we can simply run Phyre Nginx in the foreground)
#COPY supervisord.conf /etc/supervisor/conf.d/phyre-supervisor.conf

# Expose HTTP (80) and HTTPS (8443)
EXPOSE 80 8443

# --------------------------------------------------
# 7) Set up final Docker config
# --------------------------------------------------
# We'll run our entrypoint which configures the panel, then runs Nginx
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["phyre-php", "--version"]
