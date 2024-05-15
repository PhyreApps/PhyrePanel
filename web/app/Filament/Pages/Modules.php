<?php

namespace App\Filament\Pages;

use App\Models\Module;
use App\ModulesManager;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class Modules extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';

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
        $modules = [];
        $scanModules = ModulesManager::getModules();
        foreach ($scanModules as $module) {
            $modules[$module['category']][] = $module;
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

            ModulesManager::saveInstalledModule($this->installModule);

            $moduleInfo = ModulesManager::getModuleInfo($this->installModule);
            if (isset($moduleInfo['adminUrl'])) {
                return $this->redirect($moduleInfo['adminUrl']);
            }

            $this->dispatch('close-modal', id: 'install-module-modal');
        }
    }

    public function openUninstallModal($module)
    {
        $findModule = Module::where('name', $module)->first();
        if ($findModule) {
            $findModule->delete();
        }
    }

    public function openInstallModal($module)
    {
        $this->installModule = $module;
        $this->installLogPulling = true;
        $this->installLogFilePath = storage_path('logs/' . $module . '-install.log');

        file_put_contents($this->installLogFilePath, 'Installing ' . $module . '...' . PHP_EOL);

        try {
            $postInstall = app()->make('Modules\\' . $module . '\\PostInstall');
            if (method_exists($postInstall, 'run')) {

                if ($postInstall->isSupportLog()) {
                    $postInstall->setLogFile($this->installLogFilePath);
                }

                $postInstall->run();

                if ($postInstall->isSupportLog()) {
                    $this->dispatch('open-modal', id: 'install-module-modal', props: ['module' => $module]);
                    return;
                }

            }
        } catch(\Exception $e) {
           // dd($e->getMessage());
        }

        ModulesManager::saveInstalledModule($module);

        $this->installLogPulling = false;

        $moduleInfo = ModulesManager::getModuleInfo($module);
        if (isset($moduleInfo['adminUrl'])) {
            return $this->redirect($moduleInfo['adminUrl']);
        }


    }
}
