<?php

namespace Modules\Microweber;

use App\Backup\Abstract\BackupConfigBase;

class MicroweberBackupConfig extends BackupConfigBase
{
    public array $excludePaths = [

        '/home/*/public_html/storage/framework/cache/*',
        '/home/*/public_html/storage/framework/views/*',
        '/home/*/public_html/userfiles/cache/*',
        '/home/*/public_html/userfiles/media/thumbnails/*',
        '/home/*/public_html/storage/framework/sessions/*',

        '/home/*/domains/*/public_html/storage/framework/cache/*',
        '/home/*/domains/*/public_html/storage/framework/views/*',
        '/home/*/domains/*/public_html/userfiles/cache/*',
        '/home/*/domains/*/public_html/userfiles/media/thumbnails/*',
        '/home/*/domains/*/public_html/storage/framework/sessions/*',

    ];

}
