<?php

namespace App\Installers\Server\Applications;

class DovecotInstaller
{
    public $rubyVersions = [];

    public $logFilePath = '/var/log/phyre/dovecot-installer.log';

    public function setLogFilePath($path)
    {
        $this->logFilePath = $path;
    }

    public function install()
    {
        $commands = [];
        $commands[] = 'echo "Installing dovecot..."';

        // postfix - internet site
        $commands[] = 'apt-get install -y telnet exim4 dovecot-core dovecot-imapd dovecot-pop3d dovecot-lmtpd';



        // /var/lib/roundcube
       // wget https://github.com/roundcube/roundcubemail/releases/download/1.6.0/roundcubemail-1.6.0-complete.tar.gz
       // $commands[] = 'apt-get install -y roundcube roundcube-core roundcube-mysql roundcube-plugins';

        $shellFileContent = '';
        foreach ($commands as $command) {
            $shellFileContent .= $command . PHP_EOL;
        }

        $shellFileContent .= 'echo "All packages installed successfully!"' . PHP_EOL;
        $shellFileContent .= 'echo "DONE!"' . PHP_EOL;
        $shellFileContent .= 'rm -f /tmp/dovecot-installer.sh';

        file_put_contents('/tmp/dovecot-installer.sh', $shellFileContent);

        shell_exec('bash /tmp/dovecot-installer.sh >> ' . $this->logFilePath . ' &');

    }
}
