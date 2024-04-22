<?php

namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources;

use App\Models\Domain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource\Pages\CreateLetsEncryptCertificate;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource\Pages\EditLetsEncryptCertificate;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\LetsEncryptCertificateResource\Pages\ListLetsEncryptCertificates;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncryptCluster;
use Modules\LetsEncrypt\Models\LetsEncryptCertificate;

class LetsEncryptCertificateResource extends Resource
{
    protected static ?string $model = LetsEncryptCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = LetsEncryptCluster::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('domain_id')
                    ->label('Domain')
                    ->searchable()
                    ->options(
                    Domain::get()->pluck('domain', 'id')->toArray()
                ),
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
            'index' => ListLetsEncryptCertificates::route('/'),
            'create' => CreateLetsEncryptCertificate::route('/create'),
            'edit' => EditLetsEncryptCertificate::route('/{record}/edit'),
        ];
    }
}
