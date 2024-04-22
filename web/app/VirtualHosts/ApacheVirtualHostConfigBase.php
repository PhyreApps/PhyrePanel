<?php

namespace App\VirtualHosts;

abstract class ApacheVirtualHostConfigBase
{
    public array $phpAdminValueOpenBaseDirs = [
    ];

    public function getConfig()
    {
        $configValues = [];
        $configValues['phpAdminValueOpenBaseDirs'] = $this->phpAdminValueOpenBaseDirs;

        return $configValues;
    }
}
