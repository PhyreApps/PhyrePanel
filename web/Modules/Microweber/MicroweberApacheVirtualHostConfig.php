<?php

namespace Modules\Microweber;

use App\VirtualHosts\ApacheVirtualHostConfigBase;

class MicroweberApacheVirtualHostConfig extends ApacheVirtualHostConfigBase
{
    public array $phpAdminValueOpenBaseDirs = [
        '/usr/share/microweber',
    ];
}
