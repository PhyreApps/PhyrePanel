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

    public function commands()
    {
        $commands = [];
        $commands[] = 'echo "Starting PHP Installation..."';
        $commands[] = 'export DEBIAN_FRONTEND=noninteractive';
        $commands[] = 'apt-get install -yq sudo';
        $commands[] = 'add-apt-repository -y ppa:ondrej/php';
        $commands[] = 'add-apt-repository -y ppa:ondrej/apache2';
        $commands[] = 'apt-get update -yq';


        $dependenciesListApache = [
            'apache2',
            'apache2-suexec-custom'
        ];

        $dependenciesApache = implode(' ', $dependenciesListApache);
        $commands[] = 'apt-get install -yq ' . $dependenciesApache;

        if (!empty($this->phpVersions)) {
            foreach ($this->phpVersions as $phpVersion) {

                $commands[] = 'apt-get install -yq php'.$phpVersion;
                $commands[] = 'apt-get install -yq php'.$phpVersion.'-cgi';
                if (!empty($this->phpModules)) {
                    foreach ($this->phpModules as $module) {
                        $commands[] = 'apt-get install -yq php'.$phpVersion.'-' . $module;
                    }
                }
                $commands[] = 'apt-get install -yq libapache2-mod-php'.$phpVersion;
            }

        }


        $lastItem = end($this->phpVersions);
        foreach ($this->phpVersions as $phpVersion) {
            if ($phpVersion == $lastItem) {
                $commands[] = 'a2enmod php' . $phpVersion;
            } else {
                $commands[] = 'a2dismod php' . $phpVersion;
            }
        }

        $commands[] = 'a2enmod cgi';
        $commands[] = 'a2enmod deflate';
        $commands[] = 'a2enmod expires';
        $commands[] = 'a2enmod mime';
        $commands[] = 'a2enmod rewrite';
        $commands[] = 'a2enmod env';
        $commands[] = 'a2enmod ssl';
        $commands[] = 'a2enmod actions';
        $commands[] = 'a2enmod headers';
        $commands[] = 'a2enmod suexec';
        $commands[] = 'a2enmod proxy';
        $commands[] = 'a2enmod proxy_http';

        // For Fast CGI
//        $commands[] = 'a2enmod fcgid';
//        $commands[] = 'a2enmod alias';
//        $commands[] = 'a2enmod proxy_fcgi';
//        $commands[] = 'a2enmod setenvif';

        // $commands[] = 'ufw allow in "Apache Full"';


        $commands[] = 'systemctl restart apache2';
        $commands[] = 'phyre-php /usr/local/phyre/web/artisan phyre:run-repair';
        $commands[] = 'apt-get autoremove -yq';

        return $commands;
    }

    public function install()
    {
        // Clear log file
        file_put_contents($this->logFilePath, '');

        $shellFileContent = 'phyre-php /usr/local/phyre/web/artisan phyre:install-apache' . PHP_EOL;

        $shellFileContent .= 'echo "All packages installed successfully!"' . PHP_EOL;
        $shellFileContent .= 'echo "DONE!"' . PHP_EOL;
        $shellFileContent .= 'rm -f /tmp/php-installer.sh';

        file_put_contents('/tmp/php-installer.sh', $shellFileContent);
        shell_exec('chmod +x /tmp/php-installer.sh');

        shell_exec('sudo bash /tmp/php-installer.sh >> ' . $this->logFilePath . ' &');

    }
}
