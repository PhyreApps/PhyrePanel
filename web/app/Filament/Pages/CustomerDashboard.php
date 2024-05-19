<?php

namespace App\Filament\Pages;

use App\ModulesManager;
use Filament\Pages\Page;

class CustomerDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static string $view = 'filament.pages.customer-dashboard';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?string $navigationLabel = 'Customer Dashboard';

    protected static ?int $navigationSort = 1;

    protected function getViewData(): array
    {
        return [
            'menu' => [

                'email'=>[
                    'title'=>'Email',
                    'icon'=>'heroicon-o-start',
                    'menu'=>[
                        [
                            'title'=>'Email Accounts',
                            'icon'=>'heroicon-o-start',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Forwarders',
                            'icon'=>'heroicon-o-start',
                            'link'=>'#'
                        ]
                    ]
                ],

                'billing_and_support'=>[
                    'title'=>'Billing & Support',
                    'icon'=>'heroicon-o-start',
                    'menu'=>[
                        [
                            'title'=>'News & Announcemnets',
                            'icon'=>'heroicon-o-start',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage Biling Information',
                            'icon'=>'heroicon-o-start',
                            'link'=>'#'
                        ]
                    ]
                ]


            ],
        ];

    }
}
