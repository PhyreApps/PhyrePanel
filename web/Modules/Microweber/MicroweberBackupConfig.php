<?php

namespace Modules\Microweber;

use App\Backup\Abstract\BackupConfigBase;

class MicroweberBackupConfig extends BackupConfigBase
{
    public array $excludePaths = [

        'public_html/storage/framework/cache/',
        'public_html/storage/framework/views/',
        'public_html/userfiles/cache/',
        'public_html/userfiles/media/thumbnails/',
        'public_html/storage/framework/sessions/',
        'public_html/storage/cache/'
    ];

}
