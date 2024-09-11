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

                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Order')
                        ->schema([
                            // ...
                        ]),
                    Forms\Components\Wizard\Step::make('Delivery')
                        ->schema([
                            // ...
                        ]),
                    Forms\Components\Wizard\Step::make('Billing')
                        ->schema([
                            // ...
                        ]),
                ])->columnSpanFull()

            ]);
    }

    public static function ___form(Form $form): Form
    {
        $gitSSHKeys = \App\Models\GitSshKey::all()->pluck('name', 'id');
        return $form
            ->schema([

                Forms\Components\Select::make('git_ssh_key_id')
                    ->label('SSH Key')
                    ->options($gitSSHKeys)
                    ->columnSpanFull()
                    ->live()
                    ->required(),






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
            'edit' => Pages\EditGitRepository::route('/{record}/edit'),
        ];
    }
}
