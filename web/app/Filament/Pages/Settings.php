<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?int $navigationSort = 4;

    protected function getViewData(): array
    {
        $links =  [
            'Security'=>[
                'title'=>'Security',
                'icon'=>'heroicon-o-lock-closed',
                'links' =>[
                    [
                        'title'=>'Users',
                        'icon'=>'heroicon-o-user',
                        'url'=> route('filament.admin.resources.users.index')
                    ],
                    [
                        'title'=>'API Keys',
                        'icon'=>'heroicon-o-key',
                        'url'=> route('filament.admin.resources.api-keys.index')
                    ],
                ]
            ],
//            'Assistance and Troubleshooting'=>[
//                'title'=>'Assistance and Troubleshooting',
//                'icon'=>'heroicon-o-lifebuoy',
//                'links' =>[
//                    [
//                        'title'=>'Logs',
//                        'icon'=>'heroicon-o-clipboard-list',
//                        'url'=> ''
//                    ],
//                    [
//                        'title'=>'Support',
//                        'icon'=>'heroicon-o-chat-alt',
//                        'url'=>''
//                    ],
//                ]
//            ],
            'Tools & Resources'=>[
                'title'=>'Tools & Resources',
                'icon'=>'heroicon-o-cog',
                'links' =>[
                    [
                        'title'=>'Backups',
                        'icon'=>'heroicon-o-cloud-upload',
                        'url'=> route('filament.admin.resources.backups.index')
                    ],
                    [
                        'title'=>'Cron Jobs',
                        'icon'=>'heroicon-o-clock',
                        'url'=> route('filament.admin.resources.cron-jobs.index')
                    ],
                ]
            ],
            'General Settings'=>[
                'title'=>'General Settings',
                'icon'=>'heroicon-o-cog',
                'links' =>[
                    [
                        'title'=>'Server Settings',
                        'icon'=>'heroicon-o-cog',
                        'url'=> route('filament.admin.pages.general-settings')
                    ],
                ]
            ],
            'Server Management'=>[
                'title'=>'Server Management',
                'icon'=>'heroicon-o-server',
                'links'=> [

                        [
                            'title'=>'PHP Info',
                            'icon'=>'heroicon-o-information-circle',
                            'url'=> route('filament.admin.pages.php-info')
                        ],
                        [
                            'title'=>'PHP Installer',
                            'icon'=>'heroicon-o-cog',
                            'url'=> route('filament.admin.pages.php-installer')
                        ]

//                    [
//                        'title'=>'Server Information',
//                        'icon'=>'heroicon-o-information-circle',
//                        'url'=> ''
//                    ],
//                    [
//                        'title'=>'Server Status',
//                        'icon'=>'heroicon-o-check-circle',
//                        'url'=>''
//                    ],
//                    [
//                        'title'=>'Server Resources',
//                        'icon'=>'heroicon-o-chart-pie',
//                        'url'=>''
//                    ],
//                    [
//                        'title'=>'Server Logs',
//                        'icon'=>'heroicon-o-clipboard-list',
//                        'url'=>''
//                    ],
//                    [
//                        'title'=>'Server Updates',
//                        'icon'=>'heroicon-o-cloud-upload',
//                        'url'=>''
//                    ],
                ]
            ],
            'Statistics'=> [
                'title'=>'Statistics',
                'icon'=>'heroicon-o-chart-bar',
                'links'=> [
                    [
                        'title'=>'Server Statistics',
                        'icon'=>'heroicon-o-chart-bar',
                        'url'=> route('filament.admin.pages.dashboard')
                    ],
                    [
                        'title'=>'Customers Statistics',
                        'icon'=>'heroicon-o-chart-bar',
                        'url'=> route('filament.admin.resources.customers.index')
                    ],
                ]
            ],
//            'Mail'=> [
//                'title'=>'Mail',
//                'icon'=>'heroicon-o-envelope',
//                'links'=> [
//                    [
//                        'title'=>'Mail Settings',
//                        'icon'=>'heroicon-o-cog',
//                        'url'=> ''
//                    ],
//                    [
//                        'title'=>'Mail Logs',
//                        'icon'=>'heroicon-o-clipboard-list',
//                        'url'=>''
//                    ],
//                ]
//            ],
//            'Applications & Databases'=>[
//                'title'=>'Applications & Databases',
//                'icon'=>'heroicon-o-cube',
//                'links'=> [
//                    [
//                        'title'=>'Applications',
//                        'icon'=>'heroicon-o-cube',
//                        'url'=> ''
//                    ],
//                    [
//                        'title'=>'Databases',
//                        'icon'=>'heroicon-o-database',
//                        'url'=>''
//                    ],
//                ]
//            ],
            'Phyre'=> [
                'title'=>'Phyre',
                'icon'=>'heroicon-o-fire',
                'links'=> [
//                    [
//                        'title'=>'Phyre Settings',
//                        'icon'=>'heroicon-o-cog',
//                        'url'=> ''
//                    ],
//                    [
//                        'title'=>'Phyre Logs',
//                        'icon'=>'heroicon-o-clipboard-list',
//                        'url'=>''
//                    ],
                    [
                        'title'=>'Phyre Updates',
                        'icon'=>'heroicon-o-cloud-upload',
                        'url'=> route('filament.admin.pages.phyre-updates')
                    ]
                ]
            ],
            'Phyre Apperance'=> [
                'title'=>'Phyre Apperance',
                'icon'=>'heroicon-o-paint-brush',
                'links'=> [
                    [
                        'title'=>'Phyre Logo',
                        'icon'=>'heroicon-o-photograph',
                        'url'=> route('filament.admin.pages.phyre-apperance')
                    ],
                    [
                        'title'=>'Phyre Theme',
                        'icon'=>'heroicon-o-color-swatch',
                        'url'=> route('filament.admin.pages.phyre-apperance')
                    ],
                ]
            ]
        ];

        return [
            'linkGroups' => $links
        ];
    }
}
