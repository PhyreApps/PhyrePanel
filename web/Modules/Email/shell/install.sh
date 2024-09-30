sudo apt-get update -y

sudo DEBIAN_FRONTEND=noninteractive apt-get --no-install-recommends install dovecot-core dovecot-imapd dovecot-pop3d dovecot-lmtpd dovecot-mysql dovecot-sieve dovecot-managesieved -yq
sudo DEBIAN_FRONTEND=noninteractive apt-get --no-install-recommends install postfix postfix-mysql -yq
sudo apt-get --no-install-recommends install mailutils -yq
sudo apt-get --no-install-recommends install opendkim opendkim-tools postfix-policyd-spf-python postfix-pcre  -yq
sudo apt-get --no-install-recommends install spamassassin spamc -yq
sudo apt-get --no-install-recommends install clamav clamav-daemon -yq
sudo apt-get --no-install-recommends install amavisd-new -yq
sudo apt-get install libmysqlclient-dev libopendbx1-mysql -yq

# Install SASL
sudo apt-get install sasl2-bin -yq

# auto start SASL file
echo 'START=yes' > /etc/default/saslauthd

systemctl restart saslauthd

groupadd -g 5000 vmail && mkdir -p /var/mail/vmail
useradd -u 5000 vmail -g vmail -s /usr/sbin/nologin -d /var/mail/vmail
chown -R vmail:vmail /var/mail/vmail
mkdir -p /etc/postfix/sql


# Enable email ports
ufw allow 25
ufw allow 587
ufw allow 465
ufw allow 993

mkdir -p /etc/opendkim/keys
chown -R opendkim:opendkim /etc/opendkim
chmod go-rw /etc/opendkim/keys

echo "Done!"


