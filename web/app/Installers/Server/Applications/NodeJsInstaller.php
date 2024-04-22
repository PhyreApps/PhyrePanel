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
            $commands[] = 'apt-get install -y nodejs' . $nodejsVersion;
        }

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
