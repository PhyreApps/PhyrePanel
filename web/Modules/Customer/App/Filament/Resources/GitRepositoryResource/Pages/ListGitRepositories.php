<?php

namespace Modules\Customer\App\Filament\Resources\GitRepositoryResource\Pages;

use Modules\Customer\App\Filament\Resources\GitRepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGitRepositories extends ListRecords
{
    protected static string $resource = GitRepositoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('clone_new_repository')
                ->label('Clone New Repository')
                ->url('/customer/git-repositories/clone')
                ->icon('heroicon-o-plus'),
        ];
    }
}
