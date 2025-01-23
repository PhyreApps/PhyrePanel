<?php

namespace Modules\Microweber\Jobs;

use App\ShellApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use MicroweberPackages\ComposerClient\Client;
use MicroweberPackages\SharedServerScripts\MicroweberDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberModuleConnectorsDownloader;
use MicroweberPackages\SharedServerScripts\MicroweberTemplatesDownloader;
use Modules\Microweber\MicroweberComposerClientHelper;

class DownloadMicroweber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public static $displayName = 'Download Microweber';
    public static $displayDescription = 'Download Microweber core, modules and templates';

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
        $instance = $mwComposerClientHelper->getComposerClientInstance();
        $instance->prepareHeaders();

        // Download core app
        $status = $mwComposerClientHelper->getMicroweberDownloaderInstance()->download(config('microweber.sharedPaths.app'));

        // Download modules
        $modulesDownloader = new MicroweberModuleConnectorsDownloader();
        $modulesDownloader->setComposerClient($instance);
        $status = $modulesDownloader->download(config('microweber.sharedPaths.modules'));

        // Download templates
        $templatesDownloader = new MicroweberTemplatesDownloader();
        $templatesDownloader->setComposerClient($instance);
        $status = $templatesDownloader->download(config('microweber.sharedPaths.templates'));

        Artisan::call('microweber:reinstall-installations');

    }

}
