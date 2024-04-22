<?php

namespace Modules\Microweber\Filament\Clusters\Microweber\Pages;

use App\ShellApi;
use Filament\Pages\Page;
use MicroweberPackages\ComposerClient\Client;
use MicroweberPackages\SharedServerScripts\MicroweberAppPathHelper;
use MicroweberPackages\SharedServerScripts\MicroweberDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberModuleConnectorsDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberTemplatesDownloader;
use Modules\Microweber\Filament\Clusters\MicroweberCluster;

class Version extends Page
{
    protected static ?string $navigationGroup = 'Microweber';

    protected static ?string $cluster = MicroweberCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'microweber::filament.admin.pages.version';

    protected static ?int $navigationSort = 1;

    public $currentVersionOfApp = 0;

    public $latestVersionOfApp = 0;

    public $latestDownloadDateOfApp = 0;

    public $supportedTemplates = [];

    public $supportedLanguages = [];

    protected function getViewData(): array
    {
        $release = $this->__getMicroweberDownloaderInstance()->getRelease();

        $sharedPath = new MicroweberAppPathHelper();
        $sharedPath->setPath(config('microweber.sharedPaths.app'));

        $this->supportedLanguages = $sharedPath->getSupportedLanguages();
        $this->supportedTemplates = $sharedPath->getSupportedTemplates();
        $this->latestVersionOfApp = $this->__getMicroweberDownloaderInstance()->getVersion();
        $this->currentVersionOfApp = $sharedPath->getCurrentVersion();
        $this->latestDownloadDateOfApp = $sharedPath->getCreatedAt();

        return [
            'appVersion' => $this->currentVersionOfApp,
            'latestAppVersion' => $this->latestVersionOfApp,
            'latestAppDownloadDate' => $this->latestDownloadDateOfApp,
            'totalAppTemplates' => count($this->supportedTemplates),
            'appTemplates' => $this->supportedTemplates,
            'supportedLanguages' => $this->supportedLanguages,
            'supportedTemplates' => $this->supportedTemplates,
        ];

    }

    public function checkForUpdates()
    {
        $sharedAppPath = config('microweber.sharedPaths.app');

        if (! is_dir(dirname($sharedAppPath))) {
            mkdir(dirname($sharedAppPath));
        }

        $shellPath = '/usr/local/phyre/web/vendor/microweber-packages/shared-server-scripts/shell-scripts';
        ShellApi::exec('chmod +x '.$shellPath.'/*');

        // Download core app
        $status = $this->__getMicroweberDownloaderInstance()
            ->download(config('microweber.sharedPaths.app'));

        // Download modules
        $modulesDownloader = new MicroweberModuleConnectorsDownloader();
        $modulesDownloader->setComposerClient($this->__getComposerClientInstance());
        $status = $modulesDownloader->download(config('microweber.sharedPaths.modules'));

        // Download templates
        $templatesDownloader = new MicroweberTemplatesDownloader();
        $templatesDownloader->setComposerClient($this->__getComposerLicensedInstance());
        $status = $templatesDownloader->download(config('microweber.sharedPaths.templates'));

    }

    private function __getComposerClientInstance()
    {
        // The module connector must have own instance of composer client
        $composerClient = new Client();
        $composerClient->packageServers = [
            'https://market.microweberapi.com/packages/microweberserverpackages/packages.json',
        ];

        return $composerClient;
    }

    private function __getComposerLicensedInstance()
    {
        $composerClientLicensed = new Client();
        $composerClientLicensed->addLicense([
            'local_key' => setting('whitelabel_license_key')
        ]);

        return $composerClientLicensed;
    }

    private function __getMicroweberDownloaderInstance()
    {
        $coreDownloader = new MicroweberDownloader();

        $updateAppChannel = 'stable';
        if ($updateAppChannel == 'stable') {
            $coreDownloader->setReleaseSource(MicroweberDownloader::STABLE_RELEASE);
        } else {
            $coreDownloader->setReleaseSource(MicroweberDownloader::DEV_RELEASE);
        }

        $coreDownloader->setComposerClient($this->__getComposerClientInstance());

        return $coreDownloader;
    }
}
