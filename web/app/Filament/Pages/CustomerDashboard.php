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
                            'icon'=>'phyre_customer-email-deliverability',
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
                ],

                'database'=>[
                    'title'=>'Database',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'phpMyAdmin',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage My Database',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Database Wizard',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Remote Database Access',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ],

                'domains'=>[
                    'title'=>'Domains',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'WP Toolkit',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Site Publisher',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Sitejet Builder',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Domains',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Redirects',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Zone Editor',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Dynamic DNS',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ],

                'metrics'=>[
                    'title'=>'Metrics',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'Visitors',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Site Quality Monitoring',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Errors',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Bandwidth',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Raw Access',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Awstats',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Analog Stats',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Webalizer',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Webalizer FTP',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Metrics Editor',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ],

                'security'=>[
                    'title'=>'Security',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'SSH Access',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'IP Blockers',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'SSL/TLS',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage API Tokens',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Hotlink Protection',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Leech Protection',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'SSL/TSL Status',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ],

                'software'=>[
                    'title'=>'Software',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'PHP PEAR Packages',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Perl Modules',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Site Software',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Optimaze Website',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'MultiPHP Manager',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'MultiPHP INI Editor',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ],

                'advanced'=>[
                    'title'=>'Advanced',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'Cron Jobs',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Track DNS',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Indexes',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Error Pages',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Apache Handlers',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'MIME Types',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ],

                'preferences'=>[
                    'title'=>'Preferences',
                    'icon'=>'heroicon-o-star',
                    'menu'=>[
                        [
                            'title'=>'Password & Security',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Change Language',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Contact Information',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'User Manager',
                            'icon'=>'heroicon-o-star',
                            'link'=>'#'
                        ]
                    ]
                ]


            ],
        ];

    }
}
