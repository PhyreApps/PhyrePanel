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
use Modules\Microweber\MicroweberComposerClientHelper;

class DownloadMicroweber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        set_time_limit(0);

        $sharedAppPath = config('microweber.sharedPaths.app');

        if (!is_dir(dirname($sharedAppPath))) {
            mkdir(dirname($sharedAppPath));
        }

        $shellPath = '/usr/local/phyre/web/vendor/microweber-packages/shared-server-scripts/shell-scripts';
        ShellApi::exec('chmod +x ' . $shellPath . '/*');

        $mwComposerClientHelper = new MicroweberComposerClientHelper();

        // Download core app
        $status = $mwComposerClientHelper->getMicroweberDownloaderInstance()->download(config('microweber.sharedPaths.app'));

        // Download modules
        $modulesDownloader = new MicroweberModuleConnectorsDownloader();
        $modulesDownloader->setComposerClient($mwComposerClientHelper->getComposerClientInstance());
        $status = $modulesDownloader->download(config('microweber.sharedPaths.modules'));

        // Download templates
        $templatesDownloader = new MicroweberTemplatesDownloader();
        $templatesDownloader->setComposerClient($mwComposerClientHelper->getComposerLicensedInstance());
        $status = $templatesDownloader->download(config('microweber.sharedPaths.templates'));

    }

}
