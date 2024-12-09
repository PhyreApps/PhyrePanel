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

        // Clear log file
        file_put_contents($this->logFilePath, '');

        $commands = [];
        $commands[] = 'echo "Starting PHP Installation..."';
        $commands[] = 'export DEBIAN_FRONTEND=noninteractive';
        $commands[] = 'apt-get install -yq sudo';
        $commands[] = 'add-apt-repository -y ppa:ondrej/php';
        $commands[] = 'add-apt-repository -y ppa:ondrej/apache2';
        $commands[] = 'apt-get update -yq';


        $apacheCommands = [];
        $apacheCommands[] = 'a2enmod cgi';
        $apacheCommands[] = 'a2enmod deflate';
        $apacheCommands[] = 'a2enmod expires';
        $apacheCommands[] = 'a2enmod mime';
        $apacheCommands[] = 'a2enmod rewrite';
        $apacheCommands[] = 'a2enmod env';
        $apacheCommands[] = 'a2enmod ssl';
        $apacheCommands[] = 'a2enmod actions';
        $apacheCommands[] = 'a2enmod headers';
        $apacheCommands[] = 'a2enmod suexec';
        $apacheCommands[] = 'a2enmod ruid2';
        $apacheCommands[] = 'a2enmod proxy';
        $apacheCommands[] = 'a2enmod proxy_http';

        // For Fast CGI
//        $apacheCommands[] = 'a2enmod fcgid';
//        $apacheCommands[] = 'a2enmod alias';
//        $apacheCommands[] = 'a2enmod proxy_fcgi';
//        $apacheCommands[] = 'a2enmod setenvif';

        // $apacheCommands[] = 'ufw allow in "Apache Full"';


        $dependenciesListApache = [
            'apache2',
            'apache2-suexec-custom',
            'libapache2-mod-ruid2',
        ];

        $dependenciesApache = implode(' ', $dependenciesListApache);
        $commands[] = 'apt-get install -yq ' . $dependenciesApache;
        $commands = array_merge($commands, $apacheCommands);

        if (!empty($this->phpVersions)) {
            foreach ($this->phpVersions as $phpVersion) {

                $dependenciesListPHP = [];
                $dependenciesListPHP[] = 'php'.$phpVersion;
                $dependenciesListPHP[] = 'libapache2-mod-php'.$phpVersion;
                $dependenciesListPHP[] = 'php'.$phpVersion;
                $dependenciesListPHP[] = 'php'.$phpVersion.'-cgi';

                if (!empty($this->phpModules)) {
                    $dependenciesListPHP[] = 'php'.$phpVersion.'-{'.implode(',',
                            $this->phpModules).'}';
                }

                $dependenciesPHP = implode(' ', $dependenciesListPHP);
                $commands[] = 'apt-get install -yq '.$dependenciesPHP;
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


        $commands[] = 'systemctl restart apache2';
        $commands[] = 'phyre-php /usr/local/phyre/web/artisan phyre:run-repair';
        $commands[] = 'apt-get autoremove -yq';

        $shellFileContent = '';
        foreach ($commands as $command) {
            $shellFileContent .= $command . PHP_EOL;
        }

        $shellFileContent .= 'echo "All packages installed successfully!"' . PHP_EOL;
        $shellFileContent .= 'echo "DONE!"' . PHP_EOL;
        $shellFileContent .= 'rm -f /tmp/php-installer.sh';

        file_put_contents('/tmp/php-installer.sh', $shellFileContent);

        shell_exec('chmod +x /tmp/php-installer.sh');
        shell_exec('bash /tmp/php-installer.sh >> ' . $this->logFilePath . ' &');

    }

    public function installIonCube()
    {

        // 64  bit
        // $ wget https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz
        // tar -zxvf ioncube_loaders_lin_x86*
        //  cd ioncube/
        // php -i | grep extension_dir
        // sudo cp /tmp/ioncube/ioncube_loader_lin_7.4.so /usr/lib/php/20190902

//         sudo vi /etc/php/8.2/cli/php.ini 		#for PHP CLI
//         sudo vi /etc/php/8.2/fpm/php.ini		#for PHP-FPM & Nginx
//         sudo vi /etc/php/8.2/apache2/php.ini	        #for Apache2

        // zend_extension = /usr/lib/php/20190902/ioncube_loader_lin_8.2.so

        // command to add zend_extension to the php.ini file -cphp8.2-cgi.ini
        // sudo echo "zend_extension = /usr/lib/php/20190902/ioncube_loader_lin_8.2.so" | sudo tee -a /etc/php/8.2/cgi/php.ini
        // sudo echo "zend_extension = /usr/lib/php/20190902/ioncube_loader_lin_8.2.so" | sudo tee -a /etc/php/8.2/apache2/php.ini
        // sudo echo "zend_extension = /usr/lib/php/20190902/ioncube_loader_lin_8.2.so" | sudo tee -a /etc/php/8.2/cli/php.ini
    }
}
