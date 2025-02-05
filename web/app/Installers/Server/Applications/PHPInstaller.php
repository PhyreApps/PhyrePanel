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
            'apache2-suexec-custom',
            'libapache2-mod-ruid2',
        ];

        $dependenciesApache = implode(' ', $dependenciesListApache);
        $commands[] = 'apt-get install -yq ' . $dependenciesApache;

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
        $commands[] = 'a2enmod ruid2';
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

        $commands = $this->commands();
        
        foreach ($commands as $command) {
            $commandExecuted = shell_exec($command);
            @file_put_contents($this->logFilePath, $commandExecuted . PHP_EOL, FILE_APPEND);
        }

        file_put_contents($this->logFilePath, 'All packages installed successfully!');

        @unlink('/tmp/php-installer.sh');
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
