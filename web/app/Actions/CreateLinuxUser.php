<?php

namespace App\Actions;

use App\ShellApi;

class CreateLinuxUser
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

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setAsWebUser()
    {
        $this->isWebUser = true;
    }

    public function handle()
    {
        $output = '';

        $username = $this->username;
        $password = $this->password;
        $email = $this->email;

        $command = '/usr/sbin/useradd "'.$username.'" -c "'.$email.'" --no-create-home';
        $output .= ShellApi::exec($command);

        $command = 'echo '.$username.':'.$password.' | sudo chpasswd -e';
        $output .= ShellApi::exec($command);

        return $output;
    }
}
