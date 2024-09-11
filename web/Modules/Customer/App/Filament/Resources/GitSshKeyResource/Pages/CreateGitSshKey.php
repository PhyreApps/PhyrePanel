<?php

namespace Modules\Customer\App\Filament\Resources\GitSshKeyResource\Pages;

use Modules\Customer\App\Filament\Resources\GitSshKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGitSshKey extends CreateRecord
{
    protected static string $resource = GitSshKeyResource::class;
}
