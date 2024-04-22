<?php

namespace App\Installers\Server\Applications;

class PHPInstaller
{
    public $phpVersions = [];
    public $phpModules = [];
    public $logFilePath = '/var/log/phyre/php-installer.log';

    public function setPHPVersions($versions)
    {
        $this->phpVersions = $versions;
    }

    public function setPHPModules($modules)
    {
        $this->phpModules = $modules;
    }

    public function setLogFilePath($path)
    {
        $this->logFilePath = $path;
    }

    public function install()
    {
        $commands = [];
        $commands[] = 'add-apt-repository -y ppa:ondrej/php';
        $commands[] = 'add-apt-repository -y ppa:ondrej/apache2';

        $dependenciesList = [
            'apache2',
            'apache2-suexec-custom',
            'libapache2-mod-ruid2'
        ];
        if (!empty($this->phpVersions)) {
            foreach ($this->phpVersions as $phpVersion) {
                $dependenciesList[] = 'libapache2-mod-php' . $phpVersion;
            }
            if (!empty($this->phpModules)) {
                foreach ($this->phpVersions as $phpVersion) {
                    $dependenciesList[] = 'php' . $phpVersion;
                    $dependenciesList[] = 'php' . $phpVersion . '-cgi';
                    $dependenciesList[] = 'php' . $phpVersion . '-{' . implode(',', $this->phpModules) . '}';
                }
            }
        }


        $dependencies = implode(' ', $dependenciesList);
        $commands[] = 'apt-get install -y ' . $dependencies;

        $lastItem = end($this->phpVersions);
        foreach ($this->phpVersions as $phpVersion) {
            if ($phpVersion == $lastItem) {
                $commands[] = 'sudo a2enmod php' . $phpVersion;
            } else {
                $commands[] = 'sudo a2dismod php' . $phpVersion;
            }
        }

        $commands[] = 'sudo a2enmod cgi';
        $commands[] = 'sudo a2enmod mime';
        $commands[] = 'sudo a2enmod rewrite';
        $commands[] = 'sudo a2enmod env';
        $commands[] = 'sudo a2enmod ssl';
        $commands[] = 'sudo a2enmod actions';
        $commands[] = 'sudo a2enmod headers';
        $commands[] = 'sudo a2enmod suexec';
        $commands[] = 'sudo a2enmod ruid2';
        $commands[] = 'sudo a2enmod proxy';
        $commands[] = 'sudo a2enmod proxy_http';

        // For Fast CGI
//        $commands[] = 'sudo a2enmod fcgid';
//        $commands[] = 'sudo a2enmod alias';
//        $commands[] = 'sudo a2enmod proxy_fcgi';
//        $commands[] = 'sudo a2enmod setenvif';

        $commands[] = 'ufw allow in "Apache Full"';
        $commands[] = 'systemctl restart apache2';

        $shellFileContent = '';
        foreach ($commands as $command) {
            $shellFileContent .= $command . PHP_EOL;
        }
        $shellFileContent .= 'echo "All packages installed successfully!"' . PHP_EOL;
        $shellFileContent .= 'echo "DONE!"' . PHP_EOL;
        $shellFileContent .= 'rm -f /tmp/php-installer.sh';

        file_put_contents('/tmp/php-installer.sh', $shellFileContent);

        shell_exec('bash /tmp/php-installer.sh >> ' . $this->logFilePath . ' &');

    }
}
