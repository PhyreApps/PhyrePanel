<?php

namespace Modules\Email\App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class EmailSettings extends BaseSettings
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'email::filament.pages.email-settings';

    protected static ?string $navigationGroup = 'Email';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 4;

    public function save() : void
    {
        parent::save();

        \Artisan::call('email:setup-email-server');

    }

    public function schema(): array
    {
        return [
            Section::make('Settings')
                ->schema([

                    TextInput::make('email.hostname')
                        ->label('Hostname')
                        ->helperText('The hostname of the SMTP server. Example: mail.yourdomain.com')
                        ->required(),

                    TextInput::make('email.domain')
                        ->label('Domain')
                        ->helperText('The domain of the SMTP server. Example: yourdomain.com')
                        ->required(),

                ]),
        ];
    }
}
