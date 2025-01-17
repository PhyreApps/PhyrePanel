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
        $whitelabelSettings['website_manager_url'] = setting('microweber.website_manager_url');

        if (isset($whitelabelSettings['brand_favicon'])) {
            $faviconUrl = $whitelabelSettings['brand_favicon'];
            $faviconContent = file_get_contents($faviconUrl);
            $faviconPath = $sharedAppPath . '/public/favicon.ico';
            file_put_contents($faviconPath, $faviconContent);
            $faviconSecondPath = $sharedAppPath . '/favicon.ico';
            file_put_contents($faviconSecondPath, $faviconContent);
        }


        $whitelabel = new MicroweberWhitelabelSettingsUpdater();
        $whitelabel->setPath($sharedAppPath);
        $whitelabel->apply($whitelabelSettings);

        foreach ($mwInstallations as $mwInstallation) {

            // TODO
            try {
                if (isset($whitelabelSettings['brand_favicon'])) {
                    $faviconUrl = $whitelabelSettings['brand_favicon'];
                    $faviconContent = file_get_contents($faviconUrl);
                    $faviconPath = $mwInstallation->installation_path . '/public/favicon.ico';
                    file_put_contents($faviconPath, $faviconContent);
                    $faviconSecondPath = $mwInstallation->installation_path . '/favicon.ico';
                    file_put_contents($faviconSecondPath, $faviconContent);
                }
            } catch (\Exception $e) {
                // \Log::error('Error updating favicon for website: ' . $mwInstallation->installation_path);
            }

            try {
                $whitelabelApply = new MicroweberWhitelabelWebsiteApply();
                $whitelabelApply->setWebPath($mwInstallation->installation_path);
                $whitelabelApply->setSharedPath($sharedAppPath);
                $whitelabelApply->apply();
            } catch (\Exception $e) {
             //   \Log::error('Error applying whitelabel to website: ' . $mwInstallation->installation_path);
            }

        }

    }

}
