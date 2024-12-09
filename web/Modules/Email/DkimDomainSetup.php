<?php

namespace Modules\Email;

class DkimDomainSetup
{

    public static function run($domain)
    {
        $dkimPrivateKeyFile = '/etc/opendkim/keys/'.$domain.'/mail.private';
        $dkimTextFile = '/etc/opendkim/keys/'.$domain.'/mail.txt';

        if (is_file($dkimPrivateKeyFile)) {

            $dkimText = file_get_contents($dkimTextFile);
            $dkimText = str_replace("\r\n", "\n", $dkimText);

            return [
                'privateKey' => file_get_contents($dkimPrivateKeyFile),
                'text' => $dkimText,
            ];
        }

        shell_exec('sudo mkdir -p /etc/opendkim/keys/'.$domain);
        shell_exec('sudo chown -R opendkim:opendkim /etc/opendkim/keys/'.$domain);
        shell_exec('sudo chmod go-rw /etc/opendkim/keys/'.$domain);

        $output = shell_exec('sudo opendkim-genkey -b 2048 -D /etc/opendkim/keys/'.$domain.' -h rsa-sha256 -r -s mail -d '.$domain.' -v');

        $dkimPrivateKey = file_get_contents($dkimPrivateKeyFile);

        $dkimText = file_get_contents($dkimTextFile);
        $dkimText = str_replace("\r\n", "\n", $dkimText);

        return [
            'privateKey' => $dkimPrivateKey,
            'text' => $dkimText,
        ];
    }
}
