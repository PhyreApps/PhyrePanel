<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UpdateLog extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static string $view = 'filament.pages.update-log';
    protected static bool $shouldRegisterNavigation = false;

    public function getLogUpdate()
    {
    }

    protected function getViewData(): array
    {
        return [

        ];
    }
}
