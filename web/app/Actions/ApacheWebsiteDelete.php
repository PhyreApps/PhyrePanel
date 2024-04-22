<?php

namespace App\Actions;

use App\ShellApi;

class ApacheWebsiteDelete
{
    public $domain;

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function handle()
    {
        $apacheConf = '/etc/apache2/sites-available/'.$this->domain.'.conf';
        ShellApi::exec('rm -rf '.$apacheConf);

        $apacheConfEnabled = '/etc/apache2/sites-enabled/'.$this->domain.'.conf';
        ShellApi::exec('rm -rf '.$apacheConfEnabled);

        // SSL
        $apacheSSLConf = '/etc/apache2/sites-available/'.$this->domain.'-ssl.conf';
        ShellApi::exec('rm -rf '.$apacheSSLConf);

        $apacheSSLConfEnabled = '/etc/apache2/sites-enabled/'.$this->domain.'-ssl.conf';
        ShellApi::exec('rm -rf '.$apacheSSLConfEnabled);

    }
}
