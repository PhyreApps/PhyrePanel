<?php

namespace App\Installers\Server\Applications;

class PythonInstaller
{
    public $pythonVersions = [];

    public $logFilePath = '/var/log/phyre/python-installer.log';

    public function setPythonVersions($versions)
    {
        $this->pythonVersions = $versions;
    }

    public function setLogFilePath($path)
    {
        $this->logFilePath = $path;
    }

    public function install()
    {
        $commands = [];
        foreach ($this->pythonVersions as $pythonVersion) {
            $commands[] = 'apt-get install -y python' . $pythonVersion;
            $commands[] = 'apt-get install -y python' . $pythonVersion . '-dev';
            $commands[] = 'apt-get install -y python' . $pythonVersion . '-venv';
            $commands[] = 'apt-get install -y python' . $pythonVersion . '-setuptools';
            $commands[] = 'apt-get install -y python' . $pythonVersion . '-wheel';
        }

        $shellFileContent = '';
        foreach ($commands as $command) {
            $shellFileContent .= $command . PHP_EOL;
        }
        $shellFileContent .= 'echo "All packages installed successfully!"' . PHP_EOL;
        $shellFileContent .= 'echo "DONE!"' . PHP_EOL;
        $shellFileContent .= 'rm -f /tmp/python-installer.sh';

        file_put_contents('/tmp/python-installer.sh', $shellFileContent);
        shell_exec('bash /tmp/python-installer.sh >> ' . $this->logFilePath . ' &');

    }
}
