<?php

namespace Modules\Email\App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Modules\Email\App\Enums\ServiceStatus;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Modules\Email\App\Models\EmailHealthStatus;

class EmailHealthStatusPage extends Page implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static string $view = 'email::filament.pages.email-health-status';
    protected static ?string $navigationGroup = 'Email';
    protected static ?string $navigationLabel = 'Email Health Status';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service')
                    ->label('Service')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->query(fn () => EmailHealthStatus::query());
    }
}
