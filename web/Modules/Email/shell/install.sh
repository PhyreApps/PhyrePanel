sudo apt-get update -y
sudo apt-get install dovecot-core dovecot-imapd dovecot-pop3d dovecot-lmtpd dovecot-mysql dovecot-sieve dovecot-managesieved -y
sudo apt-get install postfix postfix-mysql -y
sudo apt-get install mailutils -y
sudo apt-get install opendkim opendkim-tools -y
sudo apt-get install spamassassin spamc -y
sudo apt-get install clamav clamav-daemon -y
sudo apt-get install amavisd-new -y

# Enable email ports
ufw allow 25
ufw allow 587
ufw allow 465
ufw allow 993

echo "Done!"


