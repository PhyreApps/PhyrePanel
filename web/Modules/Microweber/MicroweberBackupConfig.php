<?php

namespace Modules\Microweber;

use App\Backup\Abstract\BackupConfigBase;

class MicroweberBackupConfig extends BackupConfigBase
{
    public array $excludePaths = [
        '/storage/framework/cache',
        '/storage/framework/views',
        '/userfiles/cache',
        '/userfiles/media/thumbnails',
        '/storage/framework/sessions',
    ];

}
