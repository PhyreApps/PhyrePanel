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
                Forms\Components\TextInput::make('dir')
                    ->label('Directory')
                    ->required()
                    ->columnSpanFull(),

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('domain.domain'),

//                Tables\Columns\TextColumn::make('name')
//                    ->searchable()
//                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->sortable(),
//
//                Tables\Columns\TextColumn::make('branch')
//                    ->searchable()
//                    ->sortable(),
//
//                Tables\Columns\TextColumn::make('tag')
//                    ->searchable()
//                    ->sortable(),
//
//                Tables\Columns\TextColumn::make('clone_from')
//                    ->searchable()
//                    ->sortable(),

                Tables\Columns\TextColumn::make('dir')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('pull')
                        ->hidden(fn (GitRepository $record) => $record->status !== 'cloned')
                        ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (GitRepository $record) {

                        $gitRepository = GitRepository::find($record->id);
                        $gitRepository->clone();

                    }),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListGitRepositories::route('/'),
//            'edit' => Pages\EditGitRepository::route('/{record}/edit'),
        ];
    }
}
