<?php

namespace Modules\Email\App\Filament\Resources;

use App\Models\Domain;
use Modules\Email\App\Filament\Resources\DomainsResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Email\App\Http\Livewire\DkimSetup;
use Modules\Email\App\Models\EmailBox;
use Modules\Email\DkimDomainSetup;

class EmailDomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Email';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make('dkimSetup')
                    ->label('DKIM Setup')
                    ->form(function (Domain $record) {
                        return [
                            Forms\Components\Livewire::make('email::dkim-setup', [
                                'domain' => $record->domain,
                            ]),
                        ];
                    })
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomains::route('/'),
        ];
    }
}
