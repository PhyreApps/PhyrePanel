<?php

namespace Modules\Microweber;

use MicroweberPackages\ComposerClient\Client;
use MicroweberPackages\SharedServerScripts\MicroweberDownloader;

class MicroweberComposerClientHelper
{
    public function getComposerClientInstance()
    {
        // The module connector must have own instance of composer client
        $composerClient = new Client();
        $composerClient->packageServers = [
            'https://market.microweberapi.com/packages/microweberserverpackages/packages.json',
        ];

        return $composerClient;
    }

    public function getComposerLicensedInstance()
    {
        $composerClientLicensed = new Client();
        $composerClientLicensed->addLicense([
            'local_key' => setting('whitelabel_license_key')
        ]);

        return $composerClientLicensed;
    }

    public function getMicroweberDownloaderInstance()
    {
        $coreDownloader = new MicroweberDownloader();

        if (setting('microweber.update_app_channel') == 'development') {
            $coreDownloader->setReleaseSource(MicroweberDownloader::DEV_RELEASE);
        } else {
            $coreDownloader->setReleaseSource(MicroweberDownloader::STABLE_RELEASE);
        }

        $coreDownloader->setComposerClient($this->getComposerClientInstance());

        return $coreDownloader;
    }

}
