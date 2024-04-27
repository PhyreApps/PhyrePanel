<?php

namespace Modules\Docker\Filament\Clusters\Docker\Resources;

use Modules\Customer\App\Filament\Resources\DockerTemplateResource\Pages;
use Modules\Customer\App\Filament\Resources\DockerTemplateResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Docker\App\Models\DockerTemplate;

class DockerTemplateResource extends Resource
{

    protected static ?string $navigationGroup = 'Docker';

    protected static ?string $navigationLabel = 'Templates';

    protected static ?string $slug = 'docker/templates';


    protected static ?string $model = DockerTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                //
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
            'index' => Pages\ListDockerTemplates::route('/'),
            'create' => Pages\CreateDockerTemplate::route('/create'),
            'edit' => Pages\EditDockerTemplate::route('/{record}/edit'),
        ];
    }
}
