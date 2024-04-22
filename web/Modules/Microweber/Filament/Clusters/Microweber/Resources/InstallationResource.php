<?php

namespace Modules\Microweber\Filament\Clusters\Microweber\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Microweber\App\Models\MicroweberInstallation;
use Modules\Microweber\Filament\Clusters\Microweber\Resources\InstallationResource\Pages\ListInstallations;
use Modules\Microweber\Filament\Clusters\MicroweberCluster;

class InstallationResource extends Resource
{
    protected static ?string $model = MicroweberInstallation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Microweber';

    protected static ?string $cluster = MicroweberCluster::class;

    protected static ?string $label = 'Installations';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain.domain')->label('Domain'),
                Tables\Columns\TextColumn::make('app_version')->label('Version'),
                Tables\Columns\TextColumn::make('installation_type')->label('Type'),
                //       Tables\Columns\TextColumn::make('installation_path')->label('Path'),
                Tables\Columns\TextColumn::make('template')->label('Template'),
                //                Tables\Columns\TextColumn::make('admin_email')->label('Admin Email'),

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
            'index' => ListInstallations::route('/'),
            //            'create' => Pages\CreateInstallation::route('/create'),
            //            'edit' => Pages\EditInstallation::route('/{record}/edit'),
        ];
    }
}
