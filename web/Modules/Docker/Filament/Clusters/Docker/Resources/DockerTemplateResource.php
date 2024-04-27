<?php

namespace Modules\Docker\Filament\Clusters\Docker\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Docker\App\Models\DockerTemplate;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerTemplateResource\Pages\CreateDockerTemplate;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerTemplateResource\Pages\EditDockerTemplate;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerTemplateResource\Pages\ListDockerTemplates;

class DockerTemplateResource extends Resource
{

    protected static ?string $navigationGroup = 'Docker';

    protected static ?string $navigationLabel = 'Templates';

    protected static ?string $slug = 'docker/templates';


    protected static ?string $model = DockerTemplate::class;

    protected static ?string $navigationIcon = 'docker-templates';

    protected static ?int $navigationSort = 3;

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
            'index' => ListDockerTemplates::route('/'),
            'create' => CreateDockerTemplate::route('/create'),
            'edit' => EditDockerTemplate::route('/{record}/edit'),
        ];
    }
}
