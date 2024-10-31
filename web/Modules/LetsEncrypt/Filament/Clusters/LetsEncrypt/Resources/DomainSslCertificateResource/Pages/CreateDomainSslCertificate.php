<?php

namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource\Pages;


use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDomainSslCertificate extends CreateRecord
{
    protected static string $resource = DomainSslCertificateResource::class;
}
