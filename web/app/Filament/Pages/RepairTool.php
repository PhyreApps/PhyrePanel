<?php

namespace App\Filament\Pages;

use App\Http\Livewire\RepairTool as RepairToolComponent;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

class RepairTool extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string $view = 'filament.pages.repair-tool';

    protected static ?string $navigationGroup = 'Server Management';


    public function runRepair()
    {
        Artisan::call('phyre:run-repair');
        session()->flash('message', 'RunRepair command executed successfully.');
    }

    public function runRenewSSL()
    {
        Artisan::call('ssl-manager:renew-ssl');
        session()->flash('message', 'RenewSSL command executed successfully.');
    }
    public function runDomainRepair()
    {
        Artisan::call('phyre:run-domain-repair');
        session()->flash('message', 'RunDomainRepair command executed successfully.');
    }
}
