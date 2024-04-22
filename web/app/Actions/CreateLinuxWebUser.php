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

        $command = 'adduser --disabled-password --gecos "" "'.$username.'"';
        $output .= ShellApi::exec($command);

//        $command = 'groupadd '.$username;
//        $output .= ShellApi::exec($command);

        $command = 'usermod -a -G www-data '.$username;
        $output .= ShellApi::exec($command);

        $command = 'echo '.$username.':'.$password.' | chpasswd -e';
        $output .= ShellApi::exec($command);

        return $output;
    }
}
