<?php
namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource;

class ListLetsEncryptCertificates extends ListRecords
{
    protected static string $resource = LetsEncryptCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
