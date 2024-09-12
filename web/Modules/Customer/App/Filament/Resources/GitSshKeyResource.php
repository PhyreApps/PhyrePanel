<?php

namespace Modules\Customer\App\Filament\Resources;

use App\Models\Domain;
use App\Models\GitSshKey;
use App\Models\Scopes\CustomerScope;
use Modules\Customer\App\Filament\Resources\GitSshKeyResource\Pages;
use Modules\Customer\App\Filament\Resources\GitSshKeyResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use phpseclib3\Crypt\RSA;

class GitSshKeyResource extends Resource
{
    protected static ?string $model = GitSshKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Git';

    public static function form(Form $form): Form
    {


        return $form
            ->schema([

                Forms\Components\Select::make('hosting_subscription_id')
                    ->label('Hosting Subscription')
                    ->options(
                        \App\Models\HostingSubscription::all()->pluck('domain', 'id')
                    )
                    ->disabled(function ($record) {
                        return $record;
                    })
                    ->columnSpanFull()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->columnSpanFull()
                    ->label('Name'),

                Forms\Components\Textarea::make('private_key')
                    ->required()
                    ->columnSpanFull()
                    ->label('Private Key')
                    ->rows(10)
                    ->disabled(function ($record) {
                        return $record;
                    })
                    ->hintAction(function ($record) {
                        if ($record) {
                            return null;
                        }
                        return Forms\Components\Actions\Action::make('generate')
                            ->icon('heroicon-m-key')
                            ->action(function (Forms\Set $set) {


                                $private = RSA::createKey();
                                $public = $private->getPublicKey();

                                $privateKeyString = $private->toString('OpenSSH');
                                $publicKey = $public->toString('OpenSSH');

                                $set('private_key', $privateKeyString);
                                $set('public_key', $publicKey);
                            });
                    }),

                Forms\Components\Textarea::make('public_key')
                    ->required()
                    ->columnSpanFull()
                    ->rows(10)
                    ->disabled(function ($record) {
                        return $record;
                    })
                    ->label('Public Key'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
            'index' => Pages\ListGitSshKeys::route('/'),
            'create' => Pages\CreateGitSshKey::route('/create'),
            'edit' => Pages\EditGitSshKey::route('/{record}/edit'),
        ];
    }
}
