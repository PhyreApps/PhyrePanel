<?php

namespace Modules\Microweber;

use App\ModulePostInstall;
use Modules\Microweber\Filament\Clusters\Microweber\Pages\Version;

class PostInstall extends ModulePostInstall
{
    public $supportLog = false;
    public function run()
    {
        return true;
    }
}
