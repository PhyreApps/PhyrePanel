<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PHPInfo extends Page
{

    protected static string $view = 'filament.pages.php-info';

    protected static ?string $navigationLabel = 'PHP Info';

    protected static ?string $slug = 'php-info';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'PHP Info';
    }


    public function getInstalledPHPModules($phpVersion)
    {
        $modules = [];
        $getModules = shell_exec('php' . $phpVersion . ' -m');
        if (!empty($getModules)) {
            $getModules = explode("\n", $getModules);
            if (is_array($getModules)) {
                $getModules = array_filter($getModules);
                foreach ($getModules as $module) {
                    if ($module == '[PHP Modules]') {
                        continue;
                    }
                    if ($module == '[Zend Modules]') {
                        continue;
                    }
                    $modules[] = $module;
                }
                $modules = array_unique($modules);
            }
        }
        return $modules;
    }

    protected function getViewData(): array
    {
        $installedPHPVersions = [];

        $getPHPVersions = shell_exec('sudo update-alternatives --list php');
        if (!empty($getPHPVersions)) {
            $getPHPVersions = explode("\n", $getPHPVersions);
            if (is_array($getPHPVersions)) {
                $getPHPVersions = array_filter($getPHPVersions);
                foreach ($getPHPVersions as $phpVersion) {
                    $phpVersion = str_replace('/usr/bin/php', '', $phpVersion);
                    $phpVersion = str_replace('php', '', $phpVersion);
                    $phpVersion = str_replace('.', '', $phpVersion);
                    $phpVersion = substr($phpVersion, 0, 1) . '.' . substr($phpVersion, 1);
                    $installedPHPVersions[] = [
                        'version' => $phpVersion,
                        'modules' => $this->getInstalledPHPModules($phpVersion) ?? 'No modules found.',
                    ];
                }
            }
        }

        return [
            'installedPHPVersions' => $installedPHPVersions,
        ];
    }

}
