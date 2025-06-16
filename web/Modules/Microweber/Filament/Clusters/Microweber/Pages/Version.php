<?php

namespace Modules\Microweber\Filament\Clusters\Microweber\Pages;

use App\ShellApi;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use MicroweberPackages\ComposerClient\Client;
use MicroweberPackages\SharedServerScripts\MicroweberAppPathHelper;
use MicroweberPackages\SharedServerScripts\MicroweberDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberModuleConnectorsDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberTemplatesDownloader;
use Modules\Microweber\Filament\Clusters\MicroweberCluster;
use Modules\Microweber\Jobs\DownloadMicroweber;
use Modules\Microweber\MicroweberComposerClientHelper;

class Version extends Page
{
    protected static ?string $navigationGroup = 'Microweber';

    protected static ?string $cluster = MicroweberCluster::class;

    protected static ?string $navigationIcon = 'mw-mw_logo_small_white';

    protected static string $view = 'microweber::filament.admin.pages.version';

    protected static ?int $navigationSort = 1;

    public $currentVersionOfApp = 0;

    public $latestVersionOfApp = 0;

    public $latestDownloadDateOfApp = 0;

    public $supportedTemplates = [];
    public $supportedModules = [];

    public $supportedLanguages = [];

    public $downloadingNow = false;

    protected function getViewData(): array
    {
        $mwComposerClientHelper = new MicroweberComposerClientHelper();

        $sharedPath = new MicroweberAppPathHelper();
        $sharedPath->setPath(config('microweber.sharedPaths.app'));

        $this->supportedLanguages = $sharedPath->getSupportedLanguages();
        $this->supportedTemplates = $sharedPath->getSupportedTemplates();
        $this->supportedModules = $sharedPath->getSupportedModules();
        $this->latestVersionOfApp = $mwComposerClientHelper->getMicroweberDownloaderInstance()->getVersion();
        $this->currentVersionOfApp = $sharedPath->getCurrentVersion();
        $this->latestDownloadDateOfApp = $sharedPath->getCreatedAt();

        $findJob = DB::table('jobs')->where('payload', 'like', '%DownloadMicroweber%')->get();
        if ($findJob->count() > 0) {
            $this->downloadingNow = true;
        }

        return [
            'appVersion' => $this->currentVersionOfApp,
            'latestAppVersion' => $this->latestVersionOfApp,
            'latestAppDownloadDate' => $this->latestDownloadDateOfApp,
            'totalAppTemplates' => count($this->supportedTemplates),
            'appTemplates' => $this->supportedTemplates,
            'totalAppModules' => count($this->supportedModules),
            'appModules' => $this->supportedModules,
            'supportedLanguages' => $this->supportedLanguages,
            'supportedTemplates' => $this->supportedTemplates,
        ];

    }

    public function checkForUpdates()
    {
        $findJob = DB::table('jobs')->where('payload', 'like', '%DownloadMicroweber%')->get();
        if ($findJob->count() > 0) {
            foreach ($findJob as $job) {
                DB::table('jobs')->where('id', $job->id)->delete();
            }
        }

        $debug = 1;
        if ($debug) {
            $dm = new DownloadMicroweber();
            $dm->handle();
        } else {
            DownloadMicroweber::dispatch();
        }
    }
}
