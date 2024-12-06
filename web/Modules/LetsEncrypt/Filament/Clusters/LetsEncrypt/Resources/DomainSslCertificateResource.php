<?php

namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources;


use App\Models\DomainSslCertificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource\Pages\CreateDomainSslCertificate;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource\Pages\EditDomainSslCertificate;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Resources\DomainSslCertificateResource\Pages\ListDomainSslCertificates;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncryptCluster;

class DomainSslCertificateResource extends Resource
{
    protected static ?string $model = DomainSslCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    protected static ?string $cluster = LetsEncryptCluster::class;

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
                Tables\Columns\TextColumn::make('relatedDomain.domain')
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
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
            'index' => ListDomainSslCertificates::route('/'),
            'create' => CreateDomainSslCertificate::route('/create'),
            'edit' => EditDomainSslCertificate::route('/{record}/edit'),
        ];
    }
}
