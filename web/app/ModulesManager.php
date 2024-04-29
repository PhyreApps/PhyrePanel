<?php

namespace App;

use App\Events\ModuleIsInstalled;
use App\Models\Module;

class ModulesManager
{
    public static function getModules()
    {
        $scanModules = scandir(base_path('Modules'));
        $scanModules = array_diff($scanModules, ['.', '..']);

        $modules = [];
        foreach ($scanModules as $key => $module) {

            $moduleInfo = self::getModuleInfo($module);
            if (empty($moduleInfo)) {
                continue;
            }

            $modules[] = $moduleInfo;

        }

        return $modules;
    }

    public static function getModuleInfo($module)
    {
        if (!is_dir(base_path('Modules/' . $module))) {
            return [];
        }
        $moduleJson = file_get_contents(base_path('Modules/' . $module . '/module.json'));
        $moduleJson = json_decode($moduleJson, true);
        if (isset($moduleJson['hidden']) && $moduleJson['hidden'] == true) {
            return [];
        }
        $category = 'All';
        $logoIcon = 'heroicon-o-puzzle-piece';
        if (isset($moduleJson['logoIcon'])) {
            $logoIcon = $moduleJson['logoIcon'];
        }
        if (isset($moduleJson['category'])) {
            $category = $moduleJson['category'];
        }
        $adminUrl = '';
        if (isset($moduleJson['adminUrl'])) {
            $adminUrl = $moduleJson['adminUrl'];
        }

        $url = '';
        $installed = 0;
        $findModule = Module::where('name', $module)->first();
        if ($findModule) {
            $installed = 1;
        }
        return [
            'name' => $module,
            'description' => 'A drag and drop website builder and a powerful next-generation CMS.',
            'url' => $url,
            'adminUrl' => $adminUrl,
            'iconUrl' => url('images/modules/' . $module . '.png'),
            'logoIcon' => $logoIcon,
            'category' => $category,
            'installed'=>$installed,
        ];
    }

    public static function saveInstalledModule($module)
    {
        $findModule = Module::where('name', $module)->first();
        if ($findModule) {
            return;
        }

        $newModule = new Module();
        $newModule->name = $module;
        $newModule->namespace = 'Modules\\' . $module;
        $newModule->installed = 1;
        $newModule->save();

        event(new ModuleIsInstalled($newModule));
    }
}
