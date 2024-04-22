<?php

namespace Modules\LetsEncrypt;

use App\VirtualHosts\ApacheVirtualHostConfigBase;

class LetsEncryptApacheVirtualHostConfig extends ApacheVirtualHostConfigBase
{
    public array $phpAdminValueOpenBaseDirs = [
        '/usr/share/letsencrypt',
    ];
}
