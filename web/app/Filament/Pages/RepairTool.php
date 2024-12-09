<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class RepairTool extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string $view = 'filament.pages.repair-tool';

    protected static ?string $navigationGroup = 'Server Management';


}
