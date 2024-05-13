<?php

namespace Modules\Customer\App\Filament\Resources;

use App\Models\CronJob;
use App\Models\HostingSubscription;
use Modules\Customer\App\Filament\Resources\CronJobResource\Pages;
use Modules\Customer\App\Filament\Resources\CronJobResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CronJobResource extends Resource
{
    protected static ?string $model = CronJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Hosting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('hosting_subscription_id')
                    ->label('Hosting Subscription')
                    ->options(
                        \App\Models\HostingSubscription::all()->pluck('domain', 'id')
                    )
                    ->live()
                    ->disabled(function ($record) {
                        return $record;
                    })
                    ->columnSpanFull()
                    ->required(),

                Forms\Components\TextInput::make('schedule')
                    ->autofocus()
                    ->required()
                    ->columnSpanFull()
                    ->helperText('The schedule to run the command. Example: * * * * *')
                    ->label('Schedule'),
                Forms\Components\TextInput::make('command')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('The command to run. Example: ls -la')
                    ->label('Command'),
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
                Tables\Columns\TextColumn::make('hostingSubscription.domain')
                    ->searchable()
                    ->sortable(),
            ])
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
