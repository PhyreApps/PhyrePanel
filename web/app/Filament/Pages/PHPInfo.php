<?php

namespace App\Filament\Pages;

use App\SupportedApplicationTypes;
use Filament\Pages\Page;

class PHPInfo extends Page
{

    protected static string $view = 'filament.pages.php-info';

    protected static ?string $navigationLabel = 'PHP Info';

    protected static ?string $slug = 'php-info';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'PHP Info';
    }



    protected function getViewData(): array
    {
        return [
            'installedPHPVersions' => SupportedApplicationTypes::getInstalledPHPVersions(),
        ];
    }

}
