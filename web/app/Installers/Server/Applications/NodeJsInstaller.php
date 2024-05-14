<?php

namespace App\Installers\Server\Applications;

class NodeJsInstaller
{
    public $nodejsVersions = [];

    public $logFilePath = '/var/log/phyre/nodejs-installer.log';

    public function setNodeJsVersions($versions)
    {
        $this->nodejsVersions = $versions;
    }

    public function setLogFilePath($path)
    {
        $this->logFilePath = $path;
    }

    public function install()
    {
        $commands = [];
        $commands[] = 'apt-get install -y npm';
        foreach ($this->nodejsVersions as $nodejsVersion) {
            $commands[] = 'curl -sL https://deb.nodesource.com/setup_'.$nodejsVersion.'.x -o /tmp/nodesource'.$nodejsVersion.'_setup.sh';
            $commands[] = 'sudo bash /tmp/nodesource'.$nodejsVersion.'_setup.sh';
        }
        $commands[] = 'sudo apt-get install -y install nodejs';

        // Install Apache Passenger
        $commands[] = 'curl https://oss-binaries.phusionpassenger.com/auto-software-signing-gpg-key.txt | gpg --dearmor | sudo tee /etc/apt/trusted.gpg.d/phusion.gpg >/dev/null';
        $commands[] = "sudo sh -c 'echo deb https://oss-binaries.phusionpassenger.com/apt/passenger jammy main > /etc/apt/sources.list.d/passenger.list'";
        $commands[] = 'apt-get update';
        $commands[] = 'sudo apt-get install -y libapache2-mod-passenger';
        $commands[] = 'sudo a2enmod passenger';
        $commands[] = 'sudo service apache2 restart';

        $shellFileContent = '';
        foreach ($commands as $command) {
            $shellFileContent .= $command . PHP_EOL;
        }
        $shellFileContent .= 'echo "All packages installed successfully!"' . PHP_EOL;
        $shellFileContent .= 'echo "DONE!"' . PHP_EOL;
        $shellFileContent .= 'rm -f /tmp/nodejs-installer.sh';

        file_put_contents('/tmp/nodejs-installer.sh', $shellFileContent);

        shell_exec('bash /tmp/nodejs-installer.sh >> ' . $this->logFilePath . ' &');

    }
}
