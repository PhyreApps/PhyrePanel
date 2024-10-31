<?php

namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource;

class ListDomainSslCertificates extends ListRecords
{
    protected static string $resource = DomainSslCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
