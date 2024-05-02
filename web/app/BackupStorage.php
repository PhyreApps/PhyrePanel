<?php

namespace App;

use App\Models\Domain;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BackupStorage
{
    public static function getDomainPath($domainId)
    {
        $findDomain = Domain::where('id', $domainId)->first();
        if ($findDomain) {
            return $findDomain->domain_root . '/backups';
        }

        return false;
    }
    public static function getDomainInstance($domainId)
    {
        $domainPath = self::getDomainPath($domainId);

        if ($domainPath) {
            $storageBuild = Storage::build([
                'driver' => 'local',
                'root' => $domainPath,
            ]);
            $storageBuild->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
                return URL::temporarySignedRoute(
                    'backup.download',
                    $expiration,
                    array_merge($options, ['path' => $path])
                );
            });
            return $storageBuild;
        }

        return false;
    }

    public static function getPath()
    {
        $rootPath = storage_path('app');
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
