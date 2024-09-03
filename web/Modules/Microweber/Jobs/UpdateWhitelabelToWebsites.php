<?php

namespace Modules\Microweber\Jobs;

use App\ShellApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MicroweberPackages\ComposerClient\Client;
use MicroweberPackages\SharedServerScripts\MicroweberDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberModuleConnectorsDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberTemplatesDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberWhitelabelWebsiteApply;
use MicroweberPackages\SharedServerScripts\MicroweberWhitelabelSettingsUpdater;
use Modules\Microweber\App\Models\MicroweberInstallation;
use Modules\Microweber\MicroweberComposerClientHelper;

class UpdateWhitelabelToWebsites implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public static $displayName = 'Update Whitelabel To Websites';
    public static $displayDescription = 'Apply whitelabel settings to all websites';

    public function handle(): void
    {
        set_time_limit(0);

        $mwInstallations = MicroweberInstallation::all();
        if ($mwInstallations->isEmpty()) {
            return;
        }

        $sharedAppPath = config('microweber.sharedPaths.app');
        $whitelabelSettings = setting('microweber.whitelabel');

        $whitelabel = new MicroweberWhitelabelSettingsUpdater();
        $whitelabel->setPath($sharedAppPath);
        $whitelabel->apply($whitelabelSettings);

        foreach ($mwInstallations as $mwInstallation) {

            $whitelabelApply = new MicroweberWhitelabelWebsiteApply();
            $whitelabelApply->setWebPath($mwInstallation->installation_path);
            $whitelabelApply->setSharedPath($sharedAppPath);
            $whitelabelApply->apply();

        }

    }

}
