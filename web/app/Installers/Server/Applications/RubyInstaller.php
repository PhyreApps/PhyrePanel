<?php

namespace App\Installers\Server\Applications;

class RubyInstaller
{
    public $rubyVersions = [];

    public $logFilePath = '/var/log/phyre/ruby-installer.log';

    public function setRubyVersions($versions)
    {
        $this->rubyVersions = $versions;
    }

    public function setLogFilePath($path)
    {
        $this->logFilePath = $path;
    }

    public function install()
    {
        $commands = [];
        foreach ($this->rubyVersions as $rubyVersion) {
            $commands[] = 'apt-get install -y ruby' . $rubyVersion;
            $commands[] = 'apt-get install -y ruby' . $rubyVersion . '-dev';
            $commands[] = 'apt-get install -y ruby' . $rubyVersion . '-bundler';
        }

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
        $shellFileContent .= 'rm -f /tmp/ruby-installer.sh';

        file_put_contents('/tmp/ruby-installer.sh', $shellFileContent);

        shell_exec('bash /tmp/ruby-installer.sh >> ' . $this->logFilePath . ' &');

    }
}
