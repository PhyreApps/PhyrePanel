<?php

namespace App\Actions;

use App\ShellApi;

class CreateLinuxWebUser
{
    public $username;

    public $email;

    public $password;

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function handle()
    {
        $output = '';

        $username = $this->username;
        $password = $this->password;

        $command = 'sudo adduser --disabled-password --gecos "" "'.$username.'"';
        $output .= shell_exec($command);

        $command = 'sudo usermod -a -G www-data '.$username;
        $output .= shell_exec($command);

        $command = 'sudo echo '.$username.':'.$password.' | chpasswd -e';
        $output .= shell_exec($command);

        $homeDir = '/home';
        if (substr(sprintf('%o', fileperms($homeDir)), -4) !== '0711') {
            $command = 'sudo chmod 711 /home';
            $output .= shell_exec($command);
        }

        $command = 'sudo chmod 711 /home/'.$username;
        $output .= shell_exec($command);

        return $output;
    }
}
