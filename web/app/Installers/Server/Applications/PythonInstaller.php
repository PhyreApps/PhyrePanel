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
        $commands[] = 'export DEBIAN_FRONTEND=noninteractive';
        foreach ($this->pythonVersions as $pythonVersion) {
            $commands[] = 'apt-get install -yq python' . $pythonVersion;
            $commands[] = 'apt-get install -yq python' . $pythonVersion . '-dev';
            $commands[] = 'apt-get install -yq python' . $pythonVersion . '-venv';
            $commands[] = 'apt-get install -yq python' . $pythonVersion . '-setuptools';
            $commands[] = 'apt-get install -yq python' . $pythonVersion . '-wheel';
        }

        // Install Apache Passenger
        $commands[] = 'curl https://oss-binaries.phusionpassenger.com/auto-software-signing-gpg-key.txt | gpg --dearmor | sudo tee /etc/apt/trusted.gpg.d/phusion.gpg >/dev/null';
        $commands[] = "sudo sh -c 'echo deb https://oss-binaries.phusionpassenger.com/apt/passenger jammy main > /etc/apt/sources.list.d/passenger.list'";
        $commands[] = 'apt-get update';
        $commands[] = 'sudo apt-get install -yq libapache2-mod-passenger';
        $commands[] = 'sudo a2enmod passenger';
        $commands[] = 'sudo service apache2 restart';

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
