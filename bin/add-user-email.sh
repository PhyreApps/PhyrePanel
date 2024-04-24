#!/bin/bash
USAGE="Usage: $0 EMAIL PASSWORD [BASEDIR]";

if [ ! -n "$2" ]
then
	echo $USAGE;
	exit 1;
fi

USERNAME=$(echo "$1" | cut -f1 -d@);
DOMAIN=$(echo "$1" | cut -f2 -d@);
ADDRESS=$1;
PASSWD=$2;

if [ -n "$3" ]
then
	if [ ! -d "$3" ]
	then
		echo $USAGE;
		echo "BASEDIR must be a valid directory!";
		echo "I would have tried, $(postconf | grep ^virtual_mailbox_base | cut -f3 -d' ')";
		exit 2;
	else
	BASEDIR="$3";
	fi
else
	BASEDIR="$(postconf | grep ^virtual_mailbox_base | cut -f3 -d' ')";
fi

if [ -f /etc/postfix/vmailbox ]
then
	echo "Adding Postfix user configuration..."
	echo $ADDRESS $DOMAIN/$USERNAME/ >> /etc/postfix/vmailbox
	postmap /etc/postfix/vmailbox

	if [ $? -eq 0 ]
	then
		echo "Adding Dovecot user configuration..."

		echo $ADDRESS::5000:5000::$BASEDIR/$DOMAIN/$ADDRESS

		echo $ADDRESS::5000:5000::$BASEDIR/$DOMAIN/$ADDRESS>> $BASEDIR/$DOMAIN/passwd
		echo $ADDRESS":"$(doveadm pw -p $PASSWD) >> $BASEDIR/$DOMAIN/shadow
		chown vmail:vmail $BASEDIR/$DOMAIN/passwd && chmod 775 $BASEDIR/$DOMAIN/passwd
		chown vmail:vmail $BASEDIR/$DOMAIN/shadow && chmod 775 $BASEDIR/$DOMAIN/shadow
		/etc/init.d/postfix reload
	fi
fi
