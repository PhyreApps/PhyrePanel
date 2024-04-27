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
use Riodwanto\FilamentAceEditor\AceEditor;

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
        $dockerTemplate = request()->get('docker_template', null);

        try {
            $dockerTemplateContent = file_get_contents(module_path('Docker', 'resources/views/docker-templates/' . $dockerTemplate . '.yml'));
        } catch (\Exception $e) {
            $dockerTemplateContent = '';
        }

        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->unique('docker_templates', 'name')
                    ->default(ucfirst($dockerTemplate))
                    ->required()
                    ->placeholder('Enter the name of the template'),

//                Forms\Components\TextInput::make('description')
//                    ->label('Description')
//                    ->placeholder('Enter the description of the template'),

                Forms\Components\Select::make('docker_template')
                        ->label('Docker Template')
                        ->columnSpanFull()
                        ->hidden(function($record) {
                            return $record;
                        })
                        ->live()
                        ->default($dockerTemplate)
                        ->options([
                            'microweber' => 'Microweber',
                            'wordpress' => 'Wordpress',
                     //       'opencart' => 'Opencart',
                            'prestashop' => 'Prestashop',
                       //     'magento' => 'Magento',
                            'drupal' => 'Drupal',
                            'joomla' => 'Joomla',
                            'redis' => 'Redis',
                            'mysql' => 'Mysql',
                            'postgres' => 'Postgres',
                            'mongo' => 'Mongo',
                    ])->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $old, ?string $state) {

                        return redirect('/admin/docker/templates/create?docker_template=' . $state);
                        //$set('docker_compose', $state);
                    }),

//                Forms\Components\Textarea::make('docker_compose')
//                    ->label('Docker compose')
//                    ->required()
//                    ->columnSpanFull()
//                    ->placeholder('Enter the Dockerfile content'),

                AceEditor::make('docker_compose')
                    ->mode('yml')
                    ->theme('github')
                    ->default($dockerTemplateContent)
                    ->columnSpanFull()
                    ->darkTheme('dracula'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
              //  Tables\Columns\TextColumn::make('description'),
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
