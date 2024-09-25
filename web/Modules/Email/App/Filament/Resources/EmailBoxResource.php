<?php

namespace Modules\Email\App\Filament\Resources;

use App\Models\Domain;
use Modules\Email\App\Filament\Resources\EmailBoxResource\Pages;
use Modules\Email\App\Filament\Resources\EmailBoxResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Email\App\Models\EmailBox;

class EmailBoxResource extends Resource
{
    protected static ?string $model = EmailBox::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Email';

    protected static ?string $label = 'Boxes';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->disabled(function ($record) {
                        if ($record) {
                            return $record->exists;
                        }
                    })->columnSpanFull(),

                Forms\Components\Select::make('domain')
                    ->label('Domain')
                    ->options(Domain::get()->pluck('domain', 'domain')->toArray())
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->placeholder('Password for POP3/IMAP')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('name')
                    ->placeholder('Full Name')
                    ->columnSpanFull(),

//
//                Forms\Components\TextInput::make('maildir')
//                    ->label('Maildir'),
                Forms\Components\TextInput::make('quota')
                    ->placeholder('MB (max: 10)')
                    ->columnSpanFull()
                    ->default(10)
                    ->label('Quota'),
//
//                Forms\Components\TextInput::make('local_part')
//                    ->label('Local Part'),
//

                Forms\Components\TextInput::make('phone')
                    ->columnSpanFull()
                    ->label('Phone'),

                Forms\Components\Checkbox::make('active')
                    ->label('Active')
                    ->columnSpanFull(),

//                Forms\Components\TextInput::make('token')
//                    ->label('Token'),
//                Forms\Components\DateTimePicker::make('token_validity')
//                    ->label('Token Validity'),
//                Forms\Components\DateTimePicker::make('password_expiry')
//                    ->label('Password Expiry'),

//                Forms\Components\Checkbox::make('smtp_active')
//                    ->label('Smtp Active'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quotaFormated')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEmailBoxes::route('/'),
            'create' => Pages\CreateEmailBox::route('/create'),
            'edit' => Pages\EditEmailBox::route('/{record}/edit'),
        ];
    }
}
