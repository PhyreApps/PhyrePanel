<?php

namespace Modules\SSLManager\App\Filament\Resources;

use App\Models\DomainSslCertificate;
use Modules\SSLManager\App\Filament\Resources\CertificateResource\Pages;
use Modules\SSLManager\App\Filament\Resources\CertificateResource\RelationManagers;
use Modules\SSLManager\App\Models\Certificate;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificateResource extends Resource
{
    protected static ?string $model = DomainSslCertificate::class; 

    protected static ?string $navigationGroup = 'SSL Manager'; 
    protected static ?string $navigationLabel = 'Certificates';
    protected static ?string $pluralModelLabel = 'Certificates';

    // sort in navigation
    protected static int $sort = 1;
    

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
                TextColumn::make('domain')
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
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }
}
