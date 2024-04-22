<?php
namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource;

class EditLetsEncryptCertificate extends EditRecord
{
    protected static string $resource = LetsEncryptCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
