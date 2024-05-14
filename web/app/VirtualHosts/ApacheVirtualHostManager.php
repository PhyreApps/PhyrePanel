<?php

namespace App\VirtualHosts;

class ApacheVirtualHostManager
{
    public $registerConfigs = [];

    public function registerConfig($config, $module = null)
    {
        $this->registerConfigs[$module][] = $config;
    }

    public function getConfigs($forModules = [])
    {
        $allConfigs = [];
        foreach ($this->registerConfigs as $module => $configs) {
            if (empty($forModules)) {
                continue;
            }
            if (! in_array($module, $forModules)) {
                continue;
            }
            foreach ($configs as $config) {
                try {
                    $registerConfigInstance = app()->make($config);
                    $getConfig = $registerConfigInstance->getConfig();
                    if (! empty($getConfig)) {
                        foreach ($getConfig as $key => $value) {
                            if (! isset($allConfigs[$key])) {
                                $allConfigs[$key] = [];
                            }
                            $allConfigs[$key] = array_merge($allConfigs[$key], $value);
                        }
                    }
                } catch (\Exception $e) {
                    // can't create instance
                }
            }
        }

        return $allConfigs;
    }
}
