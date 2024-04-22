<?php
namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource\Pages;


use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource;

class CreateLetsEncryptCertificate extends CreateRecord
{
    protected static string $resource = LetsEncryptCertificateResource::class;
}
