<?php

namespace App\Livewire\Components;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Livewire\Component;

class QuickServiceRestartMenu extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function restartApache()
    {
        shell_exec('sudo service apache2 restart');
    }

    public function restartSupervisor()
    {
        shell_exec('sudo service supervisor restart');
    }

    public function restartMysql()
    {
        shell_exec('sudo service mysql restart');
    }

    public function restartPhyreServices()
    {
        shell_exec('sudo service phyre restart');
    }

    public function render(): View
    {
        return view('filament.quick-service-restart-menu');
    }
}
