<?php

namespace App\Filament\Pages\Settings;

use App\Helpers;
use App\Jobs\ApacheBuild;
use App\MasterDomain;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Storage;
use Monarobase\CountryList\CountryList;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use Symfony\Component\Console\Input\Input;

class GeneralSettings extends BaseSettings
{

    protected static bool $shouldRegisterNavigation = false;

    public function save() : void
    {
        $oldMasterDomain = setting('general.master_domain');

        parent::save();

        // Overwrite supervisor config file
        $workersCount = (int) setting('general.supervisor_workers_count');
        $supervisorConf = view('actions.samples.ubuntu.supervisor-conf', [
            'workersCount' => $workersCount
        ])->render();

        // Overwrite supervisor config file
        file_put_contents('/etc/supervisor/conf.d/phyre.conf', $supervisorConf);

        // Restart supervisor
        shell_exec('service supervisor restart');

        file_put_contents('/var/www/html/index.html', setting('general.master_domain_page_html'));

        $rebuildApache = false;
        if ($oldMasterDomain != setting('general.master_domain')) {
            $rebuildApache = true;
        }
        if ($rebuildApache) {
            $apacheBuild = new ApacheBuild(true);
            $apacheBuild->handle();
        }

    }

    public function schema(): array|Closure
    {

        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('general.brand_name'),
                            TextInput::make('general.brand_logo_url'),
                            ColorPicker::make('general.brand_primary_color'),

                            TextInput::make('general.master_domain')->regex('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i'),

                            TextInput::make('general.master_email'),
                            Select::make('general.master_country')
                                ->searchable()
                                ->options(function () {
                                    $countryList = new CountryList();
                                    return $countryList->getList();
                                }),
                            TextInput::make('general.master_locality'),
                            TextInput::make('general.organization_name'),
                        ]),
                    Tabs\Tab::make('Apache Web Pages')
                        ->schema([
                            Textarea::make('general.master_domain_page_html'),
                            Textarea::make('general.domain_suspend_page_html'),
                            Textarea::make('general.domain_created_page_html'),
                        ]),

                    Tabs\Tab::make('Backups')
                        ->schema([
                            TextInput::make('general.backup_path')
                                ->default(Storage::path('backups'))
                        ]),

                    Tabs\Tab::make('Supervisor')
                        ->schema([
                            TextInput::make('general.supervisor_workers_count')
                                ->numeric()
                                ->helperText('Number of workers to run supervisor processes. Default is 4.')
                                ->default(4)
                        ]),
                ]),
        ];
    }
}
