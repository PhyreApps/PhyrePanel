<?php

namespace App\Filament\Pages;

use App\Models\Module;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class Modules extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string $view = 'filament.pages.modules';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?string $navigationLabel = 'Extensions';

    protected static ?int $navigationSort = 1;

    public $installLogPulling = false;
    public $installLog = '';
    public $installModule = '';
    public $installLogFilePath = '';

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
        $this->installLog = '';
        if (file_exists($this->installLogFilePath)) {
            $this->installLog = file_get_contents($this->installLogFilePath);
            $this->installLog = str_replace("\n", "<br>", $this->installLog);
        }

        if (Str::contains($this->installLog, 'Done')) {
            $this->installLogPulling = false;
            $newModule = new Module();
            $newModule->name = $this->installModule;
            $newModule->namespace = 'Modules\\' . $this->installModule;
            $newModule->installed = 1;
            $newModule->save();

            $this->dispatch('close-modal', id: 'install-module-modal');
        }
    }

    public function openInstallModal($module)
    {
        $this->installModule = $module;
        $this->installLogPulling = true;
        $this->installLogFilePath = storage_path('logs/' . $module . '-install.log');

        file_put_contents($this->installLogFilePath, 'Installing ' . $module . '...' . PHP_EOL);

        $postInstall = app()->make('Modules\\' . $module . '\\PostInstall');
        if (method_exists($postInstall, 'run')) {
            $postInstall->setLogFile($this->installLogFilePath);
            $postInstall->run();
        } else {
            $newModule = new Module();
            $newModule->name = $module;
            $newModule->namespace = 'Modules\\' . $module;
            $newModule->installed = 1;
            $newModule->save();
        }

        $this->dispatch('open-modal', id: 'install-module-modal', props: ['module' => $module]);
    }
}
