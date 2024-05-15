<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CronJobResource\Pages;
use App\Models\CronJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CronJobResource extends Resource
{
    protected static ?string $model = CronJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?int $navigationSort = 98;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('schedule')
                    ->autofocus()
                    ->required()
                    ->label('Schedule'),
                Forms\Components\TextInput::make('command')
                    ->required()
                    ->label('Command'),
                Forms\Components\TextInput::make('user')
                    ->required()
                    ->label('User'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('schedule')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('command')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user')
                    ->searchable()
                    ->sortable(),

            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCronJobs::route('/'),
            'create' => Pages\CreateCronJob::route('/create'),
            'edit' => Pages\EditCronJob::route('/{record}/edit'),
        ];
    }
}
