<?php

namespace Modules\Customer\App\Filament\Resources;

use App\GitClient;
use App\Models\GitRepository;
use Faker\Provider\Text;
use Modules\Customer\App\Filament\Resources\GitRepositoryResource\Pages;
use Modules\Customer\App\Filament\Resources\GitRepositoryResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GitRepositoryResource extends Resource
{
    protected static ?string $model = GitRepository::class;

    protected static ?string $navigationIcon = 'phyre-git';

    protected static ?string $navigationGroup = 'Git';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->required()
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) use (&$branches, &$tags) {
                        $repoDetails = GitClient::getRepoDetailsByUrl($state);
                        if (isset($repoDetails['name'])) {
                            $set('name', $repoDetails['owner'] .'/'. $repoDetails['name']);
                        }
                    })
                    ->placeholder('Enter the URL of the repository'),


                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('Enter the name of the repository'),

                Forms\Components\Select::make('branch')
                    ->label('Branch')
                    ->required()
                    ->options(function (Forms\Get $get) {
                        $url = $get('url');
                        $repoDetails = GitClient::getRepoDetailsByUrl($url);
                        if (isset($repoDetails['name'])) {
                            return $repoDetails['branches'];
                        }
                    })
                    ->live()
                    ->columnSpanFull()
                    ->placeholder('Enter the branch of the repository'),

                Forms\Components\Select::make('tag')
                    ->label('Tag')
                    ->live()
                    ->options(function (Forms\Get $get) {
                        $url = $get('url');
                        $repoDetails = GitClient::getRepoDetailsByUrl($url);
                        if (isset($repoDetails['name'])) {
                            return $repoDetails['tags'];
                        }
                    })
                    ->columnSpanFull()
                    ->placeholder('Enter the tag of the repository'),

                Forms\Components\TextInput::make('dir')
                    ->label('Directory')
                    ->columnSpanFull()
                    ->required()
                    ->placeholder('Enter the directory of the repository'),
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
            'index' => Pages\ListGitRepositories::route('/'),
            'create' => Pages\CreateGitRepository::route('/create'),
            'edit' => Pages\EditGitRepository::route('/{record}/edit'),
        ];
    }
}
