<?php

namespace App\Actions;

use App\ShellApi;

class GetLinuxUser
{
    public $username;

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function handle()
    {
        $username = $this->username;
        $user = ShellApi::exec('getent passwd '.$username);
        if (empty($user)) {
            return null;
        }

        $user = explode(':', $user);

        return $user;
    }
}
