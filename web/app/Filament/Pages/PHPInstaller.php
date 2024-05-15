<?php

namespace App\Filament\Pages;

use App\Installers\Server\Applications\NodeJsInstaller;
use App\Installers\Server\Applications\PythonInstaller;
use App\Installers\Server\Applications\RubyInstaller;
use App\Livewire\Installer;
use App\SupportedApplicationTypes;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class PHPInstaller extends Installer
{

    protected static string $layout = 'filament-panels::components.layout.index';

    protected static string $view = 'filament.pages.php-installer';

    protected static ?string $navigationLabel = 'PHP Installer';

    protected static ?string $slug = 'php-installer';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'PHP Installer';
    }

    public function form(Form $form): Form
    {

        return $form
            ->schema([

                Wizard::make([

                    Wizard\Step::make('Step 1')
                        ->description('Install PHP, Addons, and Extensions')
                        ->schema([

                            // PHP Configuration
                            CheckboxList::make('server_php_versions')
                                ->default([
                                    '8.2'
                                ])
                                ->label('PHP Version')
                                ->options(SupportedApplicationTypes::getPHPVersions())
                                ->columns(5)
                                ->required(),

                            CheckboxList::make('server_php_modules')
                                ->label('PHP Modules')
                                ->columns(5)
                                ->options(SupportedApplicationTypes::getPHPModules()),

                        ])->afterValidation(function () {

                            $this->install_log = 'Prepare installation...';
                            if (is_file(storage_path('server-app-configuration.json'))) {
                                unlink(storage_path('server-app-configuration.json'));
                            }

                            $phpInstaller = new \App\Installers\Server\Applications\PHPInstaller();
                            $phpInstaller->setPHPVersions($this->server_php_versions);
                            $phpInstaller->setPHPModules($this->server_php_modules);
                            $phpInstaller->setLogFilePath(storage_path($this->install_log_file_path));
                            $phpInstaller->install();

                        }),

                    Wizard\Step::make('Step 2')
                        ->description('Finish installation')
                        ->schema([

                            TextInput::make('install_log')
                                ->view('livewire.installer-install-log')
                                ->label('Installation Log'),

                        ])

                ])->persistStepInQueryString()
                    //->startOnStep($startOnStep)
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                        <x-filament::button
                            type="submit"
                            size="sm"
                            color="primary"
                            wire:click="install"
                        >
                            Submit
                        </x-filament::button>
                    BLADE)))

            ]);

    }

    public function getRedirectLinkAfterInstall()
    {
        return '/admin/php-info';
    }

}
