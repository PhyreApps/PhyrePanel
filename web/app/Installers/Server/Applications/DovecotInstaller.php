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
        $commands[] = 'apt-get install -y dovecot-core dovecot-imapd dovecot-pop3d dovecot-lmtpd';

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
