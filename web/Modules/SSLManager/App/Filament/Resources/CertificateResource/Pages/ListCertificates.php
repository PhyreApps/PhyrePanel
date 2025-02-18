<?php

namespace Modules\SSLManager\App\Filament\Resources\CertificateResource\Pages;

use Modules\SSLManager\App\Filament\Resources\CertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
