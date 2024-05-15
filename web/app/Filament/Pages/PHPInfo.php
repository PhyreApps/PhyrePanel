<?php

namespace app\Filament\Pages;

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

class PHPInfo extends Installer
{

    protected static string $view = 'filament.pages.php-info';

    protected static ?string $navigationLabel = 'PHP Info';

    protected static ?string $slug = 'php-info';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'PHP Info';
    }


}
