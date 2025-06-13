<?php

namespace Modules\Microweber;

use MicroweberPackages\ComposerClient\Client;
use MicroweberPackages\SharedServerScripts\MicroweberDownloader;

class MicroweberComposerClientHelper
{
    public function getComposerClientInstance()
    {
        $packageServers = [
            'https://modules.microweberapi.com/packages/microweberserverpackages/packages.json',
            'https://modules.microweberapi.com/packages/microweber/packages.json',
        ];

        $marketplaceRepositoriesUrls = setting('microweber.whitelabel.marketplace_repositories_urls');
        if ($marketplaceRepositoriesUrls) {
            $marketplaceRepositoriesUrls = explode("\n", $marketplaceRepositoriesUrls);
            $packageServers = array_merge($packageServers, $marketplaceRepositoriesUrls);
        }

        // The module connector must have own instance of composer client
        $composerClient = new Client();
        $composerClient->packageServers = $packageServers;

        $lic = setting('whitelabel_license_key');

        if($lic) {
            $composerClient->addLicense([
                'local_key' => $lic
            ]);
        }
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

        if (setting('microweber.update_app_channel') == 'beta') {
            $coreDownloader->setReleaseSource(MicroweberDownloader::DEV_RELEASE);
        } else {
            $coreDownloader->setReleaseSource(MicroweberDownloader::STABLE_RELEASE);
        }

        $coreDownloader->setComposerClient($this->getComposerClientInstance());

        return $coreDownloader;
    }

}
