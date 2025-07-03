<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostingSubscriptionResource\Pages;
use app\Filament\Resources\HostingSubscriptionResource\Pages\ManageHostingSubscriptionFileManager;
use app\Filament\Resources\HostingSubscriptionResource\Pages\ManageHostingSubscriptionFtpAccounts;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\HostingSubscription;
use App\Models\PhyreServer;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Wizard;

class HostingSubscriptionResource extends Resource
{
    protected static ?string $model = HostingSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = 'Hosting Services';

    protected static ?string $label = 'Hosting Accounts';

    protected static ?int $navigationSort = 2;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([


//                Tables\Columns\TextColumn::make('phyre_server_id')
//                    ->label('Server')
//                    ->badge()
//                    ->state(function ($record) {
//                        if ($record->phyre_server_id > 0) {
//                            $phyreServer = PhyreServer::where('id', $record->phyre_server_id)->first();
//                            if ($phyreServer) {
//                                return $phyreServer->name;
//                            }
//                        }
//                        return 'MAIN';
//                    })
//                    ->searchable()
//                    ->sortable(),

                Tables\Columns\TextColumn::make('domain')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('system_username')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Customer ID')
                    ->sortable(),

//                Tables\Columns\TextColumn::make('hostingPlan.name')
//                    ->searchable()
//                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable()


            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('domain')
                    ->attribute('id')
                    ->label('Domain')
                    ->searchable()
                    ->options(fn(): array => HostingSubscription::query()->pluck('domain', 'id')->all()),
                Tables\Filters\SelectFilter::make('customer_id')
                    ->searchable()
                    ->options(function (): array {
                        return Customer::query()->get(['id', 'name'])->mapWithKeys(function ($customer) {
                            return [$customer->id => "{$customer->name} (ID: {$customer->id})"];
                        })->all();
                    }),
                Tables\Filters\SelectFilter::make('system_username')
                    ->attribute('id')
                    ->label('System Username')
                    ->searchable()
                    ->options(fn(): array => HostingSubscription::query()->pluck('system_username', 'id')->all())
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('visit')
                    ->label('Open website')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn($record): string => 'http://' . $record->domain, true),
//                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            //   Pages\ViewHos::class,
            Pages\EditHostingSubscription::class
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
            'index' => Pages\ListHostingSubscriptions::route('/'),
//            'edit' => Pages\EditHostingSubscription::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['domain', 'system_username', 'customer.name', 'customer.id'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var HostingSubscription $record */

        return [
            'HostingSubscription' => $record->domain,
            'System Username' => $record->system_username,
            'Customer' => optional($record->customer)->name,
            'Customer ID' => optional($record->customer)->id,
        ];
    }

    /** @return Builder<HostingSubscription> */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }
}
