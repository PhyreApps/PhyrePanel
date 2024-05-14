<?php

namespace App;

use App\Models\Domain;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BackupStorage
{
    public static function getPath()
    {
        $rootPath = '/var/lib/phyre/backups/system';
        $customBackupPath = setting('general.backup_path');
        if (!empty($customBackupPath)) {
            $rootPath = $customBackupPath;
        }
        return $rootPath;
    }

    public static function getInstance($path = false)
    {
        $rootPath = self::getPath();
        if ($path) {
            $rootPath = $path;
        }

        $storageBuild = Storage::build([
            'driver' => 'local',
            'throw' => false,
            'root' => $rootPath,
        ]);
        $storageBuild->buildTemporaryUrlsUsing(function ($path, $expiration, $options) use($rootPath) {
            return URL::temporarySignedRoute(
                'backup.download',
                $expiration,
                array_merge($options, [
                    'path' => $path,
                    'root_path' => $rootPath,
                ])
            );
        });

        return $storageBuild;
    }
}
