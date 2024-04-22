<?php

namespace Modules\Microweber;

use Modules\Microweber\Filament\Clusters\Microweber\Pages\Version;

class PostInstall
{
    public function run()
    {
        $version = new Version();
        $version->checkForUpdates();

        return true;
    }
}
