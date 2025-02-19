<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;

class CreateHostingSubscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.create-hosting-subscription';

    protected static ?string $navigationGroup = 'Hosting Services';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'hosting-subscriptions/create';

    protected static ?string $title = 'Create Hosting Account';

    public function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Validation')
                    ->schema([
                        TextInput::make('domain')
                            ->required()
                            ->regex('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i')
                            ->disabled(function ($record) {
                                if (isset($record->exists)) {
                                    return $record->exists;
                                } else {
                                    return false;
                                }
                            })
                            ->suffixIcon('heroicon-m-globe-alt')
                            ->columnSpanFull(),
                    ])->afterValidation(function ($data) {

                        dd(3);

                    }),
                Wizard\Step::make('Customer Information')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(
                                \App\Models\Customer::all()->pluck('name', 'id')
                            )
                            ->required()->columnSpanFull(),
                    ]),
                Wizard\Step::make('Building Hosting Account')
                    ->schema([

                        Select::make('hosting_plan_id')
                            ->label('Hosting Plan')
                            ->options(
                                \App\Models\HostingPlan::all()->pluck('name', 'id')
                            )
                            ->required()->columnSpanFull(),

                        Checkbox::make('advanced')
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('system_username')
                            ->hidden(fn(Get $get): bool => !$get('advanced'))
                            ->disabled(function ($record) {
                                if (isset($record->exists)) {
                                    return $record->exists;
                                } else {
                                    return false;
                                }
                            })
                            ->suffixIcon('heroicon-m-user'),

                        TextInput::make('system_password')
                            ->hidden(fn(Get $get): bool => !$get('advanced'))
                            ->disabled(function ($record) {
                                if (isset($record->exists)) {
                                    return $record->exists;
                                } else {
                                    return false;
                                }
                            })
                            ->suffixIcon('heroicon-m-lock-closed'),
                    ]),
            ])->columnSpanFull(),

        ]);
    }

}
