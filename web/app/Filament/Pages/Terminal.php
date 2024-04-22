<?php

namespace app\Filament\Pages;

use Filament\Pages\Page;

class Terminal extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static string $view = 'filament.pages.terminal';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?string $navigationLabel = 'Terminal';

    protected static ?int $navigationSort = 1;

}
