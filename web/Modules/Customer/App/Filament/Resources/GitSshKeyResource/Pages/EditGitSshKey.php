<?php

namespace Modules\Customer\App\Filament\Resources\GitSshKeyResource\Pages;

use Modules\Customer\App\Filament\Resources\GitSshKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGitSshKey extends EditRecord
{
    protected static string $resource = GitSshKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
