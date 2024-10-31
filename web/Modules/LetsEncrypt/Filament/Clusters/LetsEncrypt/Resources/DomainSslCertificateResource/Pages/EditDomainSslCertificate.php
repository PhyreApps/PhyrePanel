<?php

namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource\Pages;

use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDomainSslCertificate extends EditRecord
{
    protected static string $resource = DomainSslCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
