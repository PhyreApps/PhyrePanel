<?php

namespace Modules\Customer\App\Filament\Resources\GitRepositoryResource\Pages;

use Modules\Customer\App\Filament\Resources\GitRepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGitRepository extends CreateRecord
{
    protected static string $resource = GitRepositoryResource::class;

    protected static ?string $navigationLabel = 'Clone Git Repository';

    public function getTitle(): string
    {
        return 'Clone Git Repository';
    }
}
