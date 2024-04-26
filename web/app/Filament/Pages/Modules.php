<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Modules extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string $view = 'filament.pages.modules';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?string $navigationLabel = 'Extensions';

    protected static ?int $navigationSort = 1;

    public $installLogPulling = false;
    public $installLog = '';

    protected function getViewData(): array
    {

        $scanModules = scandir(base_path('Modules'));
        $scanModules = array_diff($scanModules, ['.', '..']);

        $modules = [];
        foreach ($scanModules as $key => $module) {
            if (!is_dir(base_path('Modules/' . $module))) {
                unset($modules[$key]);
            }
            $moduleJson = file_get_contents(base_path('Modules/' . $module . '/module.json'));
            $moduleJson = json_decode($moduleJson, true);
            if (isset($moduleJson['hidden']) && $moduleJson['hidden'] == true) {
                continue;
            }
            $category = 'All';
            $logoIcon = 'heroicon-o-puzzle-piece';
            if (isset($moduleJson['logoIcon'])) {
                $logoIcon = $moduleJson['logoIcon'];
            }
            if (isset($moduleJson['category'])) {
                $category = $moduleJson['category'];
            }
            $modules[$category][] = [
                'name' => $module,
                'description' => 'A drag and drop website builder and a powerful next-generation CMS.',
                'url' => url('admin/' . $module),
                'iconUrl' => url('images/modules/' . $module . '.png'),
                'logoIcon' => $logoIcon,
                'category' => 'Content Management',
            ];
        }

        return [
            'categories' => $modules,
        ];

    }

    public function getInstallLog()
    {
        $this->installLog = time();
    }

    public function openInstallModal($module)
    {

        $this->installLogPulling = true;

        $this->dispatch('open-modal', id: 'install-module-modal', props: ['module' => $module]);
    }
}
