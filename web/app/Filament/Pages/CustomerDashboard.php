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
                    'icon'=>'phyre_customer-php',
                    'menu'=>[
                        [
                            'title'=>'Email Accounts',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Forwarders',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Email Routing',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Autoresponders',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Default Address',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Mailing Lists',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Track Delivery',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Global Email Filters',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Email Filters',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Email Deliverability',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Address Importer',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Spam Filters',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Encryption',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'BoxTrapper',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Calendars and Contacts Configuration',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Calendars and Contacts Sharing',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Calendars and Contacts Management',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Email Disk Usage',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ],

                'billing_and_support'=>[
                    'title'=>'Billing & Support',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'News & Announcemnets',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage Biling Information',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Download Resources',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'View Email History',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'View Invoice History',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Search our Knowledgebase',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Check Network Status',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'View Billing Information',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage Profile',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Register New Domain',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Transfer a Domain',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Open Ticket',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'View Support Tickets',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Upgrade/Downgrade',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ],

                'files'=>[
                    'title'=>'Files',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'File Manager',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Images',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Directory Privacy',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Disk Usage',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Web Disk',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'FTP Accounts',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'FTP Connections',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Backup',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Backup Wizard',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Git Version Control',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'File and Directory Restoration',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                    ]
                ]


            ],
        ];

    }
}
