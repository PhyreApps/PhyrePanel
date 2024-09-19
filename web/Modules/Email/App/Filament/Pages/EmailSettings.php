<?php

namespace Modules\Email\App\Filament\Pages;

use Filament\Pages\Page;

class EmailSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'email::filament.pages.email-settings';

    protected static ?string $navigationGroup = 'Email';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 4;

}
