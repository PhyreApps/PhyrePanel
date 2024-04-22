<?php

namespace App\Filament\Resources\RemoteDatabaseServerResource\Pages;

use App\Filament\Resources\RemoteDatabaseServerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRemoteDatabaseServer extends CreateRecord
{
    protected static string $resource = RemoteDatabaseServerResource::class;

    protected static ?string $title = 'Add Remote Database Server';

    protected static ?string $breadcrumb = 'Add';
}
