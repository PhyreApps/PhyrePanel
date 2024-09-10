<?php

namespace Modules\Customer\App\Filament\Resources\GitRepositoryResource\Pages;

use Modules\Customer\App\Filament\Resources\GitRepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGitRepository extends EditRecord
{
    protected static string $resource = GitRepositoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
