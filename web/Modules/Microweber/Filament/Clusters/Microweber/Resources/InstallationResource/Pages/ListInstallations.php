<?php

namespace Modules\Microweber\Filament\Clusters\Microweber\Resources\InstallationResource\Pages;

use App\Models\Domain;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use MicroweberPackages\SharedServerScripts\MicroweberInstallationsScanner;
use Modules\Microweber\App\Actions\MicroweberScanner;
use Modules\Microweber\App\Models\MicroweberInstallation;
use Modules\Microweber\Filament\Clusters\Microweber\Resources\InstallationResource;

class ListInstallations extends ListRecords
{
    protected static string $resource = InstallationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Scan for installations')->action('scanForInstallations'),
        ];
    }

    public function scanForInstallations()
    {

        $newMwScan = new MicroweberScanner();
        $newMwScan->handle();

    }
}
