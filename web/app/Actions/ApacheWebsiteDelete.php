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
        if (empty($this->domain)) {
            return false;
        }
        
        $apacheConf = '/etc/apache2/sites-available/'.$this->domain.'.conf';
        shell_exec('rm -rf '.$apacheConf);

        $apacheConfEnabled = '/etc/apache2/sites-enabled/'.$this->domain.'.conf';
        shell_exec('rm -rf '.$apacheConfEnabled);

        // SSL
        $apacheSSLConf = '/etc/apache2/sites-available/'.$this->domain.'-ssl.conf';
        shell_exec('rm -rf '.$apacheSSLConf);

        $apacheSSLConfEnabled = '/etc/apache2/sites-enabled/'.$this->domain.'-ssl.conf';
        shell_exec('rm -rf '.$apacheSSLConfEnabled);

    }
}
