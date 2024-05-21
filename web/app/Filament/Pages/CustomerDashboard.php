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
                    'icon'=>'phyre_customer-email',
                    'menu'=>[
                        [
                            'title'=>'Email Accounts',
                            'icon'=>'phyre_customer-email-account',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Forwarders',
                            'icon'=>'phyre_customer-email-forwarders',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Email Routing',
                            'icon'=>'phyre_customer-email-routing',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Autoresponders',
                            'icon'=>'phyre_customer-email-autoresponders',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Default Address',
                            'icon'=>'phyre_customer-email-default',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Mailing Lists',
                            'icon'=>'phyre_customer-email-list',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Track Delivery',
                            'icon'=>'phyre_customer-email-track',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Global Email Filters',
                            'icon'=>'phyre_customer-email-global-filter',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Email Filters',
                            'icon'=>'phyre_customer-email-filter',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Email Deliverability',
                            'icon'=>'phyre_customer-email-deliverability',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Address Importer',
                            'icon'=>'phyre_customer-email-importer',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Spam Filters',
                            'icon'=>'phyre_customer-email-spam-filters',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Encryption',
                            'icon'=>'phyre_customer-email-encryption',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'BoxTrapper',
                            'icon'=>'phyre_customer-email-box-trap',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Calendars and Contacts Configuration',
                            'icon'=>'phyre_customer-email-calendars-configuration',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Calendars and Contacts Sharing',
                            'icon'=>'phyre_customer-email-calendar-share',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Calendars and Contacts Management',
                            'icon'=>'phyre_customer-email-calendar-management',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Email Disk Usage',
                            'icon'=>'phyre_customer-email-disk',
                            'link'=>'#'
                        ]
                    ]
                ],

                'billing_and_support'=>[
                    'title'=>'Billing & Support',
                    'icon'=>'phyre_customer-billing',
                    'menu'=>[
                        [
                            'title'=>'News & Announcemnets',
                            'icon'=>'phyre_customer-billing-news-announcement',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage Biling Information',
                            'icon'=>'phyre_customer-billing-manage-information',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Download Resources',
                            'icon'=>'phyre_customer-billing-download-resources',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'View Email History',
                            'icon'=>'phyre_customer-billing-history',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'View Invoice History',
                            'icon'=>'phyre_customer-billing-invoice-history',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Search our Knowledgebase',
                            'icon'=>'phyre_customer-billing-search-knowledgebase',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Check Network Status',
                            'icon'=>'phyre_customer-billing-network-status',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'View Billing Information',
                            'icon'=>'phyre_customer-billing-information',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage Profile',
                            'icon'=>'phyre_customer-billing-manage-profile',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Register New Domain',
                            'icon'=>'phyre_customer-billing-register-domain',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Transfer a Domain',
                            'icon'=>'phyre_customer-billing-transfer-domain',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Open Ticket',
                            'icon'=>'phyre_customer-billing-open-ticket',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'View Support Tickets',
                            'icon'=>'phyre_customer-billing-support-ticket',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Upgrade/Downgrade',
                            'icon'=>'phyre_customer-billing-update',
                            'link'=>'#'
                        ]
                    ]
                ],

                'files'=>[
                    'title'=>'Files',
                    'icon'=>'phyre_customer-files',
                    'menu'=>[
                        [
                            'title'=>'File Manager',
                            'icon'=>'phyre_customer-file-manager',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Images',
                            'icon'=>'phyre_customer-file-images',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Directory Privacy',
                            'icon'=>'phyre_customer-files-directory-privacy',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Disk Usage',
                            'icon'=>'phyre_customer-file-disk',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Web Disk',
                            'icon'=>'phyre_customer-file-web-disk',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'FTP Accounts',
                            'icon'=>'phyre_customer-file-ftp',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'FTP Connections',
                            'icon'=>'phyre_customer-file-connection',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Backup',
                            'icon'=>'phyre_customer-file-backup',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Backup Wizard',
                            'icon'=>'phyre_customer-file-backup-wizard',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Git Version Control',
                            'icon'=>'phyre_customer-file-git',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'File and Directory Restoration',
                            'icon'=>'phyre_customer-file-directory-restoration',
                            'link'=>'#'
                        ],
                    ]
                ],

                'database'=>[
                    'title'=>'Database',
                    'icon'=>'phyre_customer-database',
                    'menu'=>[
                        [
                            'title'=>'phpMyAdmin',
                            'icon'=>'phyre_customer-database-php',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage My Database',
                            'icon'=>'phyre_customer-database-manage',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Database Wizard',
                            'icon'=>'phyre_customer-database-wizard',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Remote Database Access',
                            'icon'=>'phyre_customer-database-remote',
                            'link'=>'#'
                        ]
                    ]
                ],

                'domains'=>[
                    'title'=>'Domains',
                    'icon'=>'phyre_customer-domains',
                    'menu'=>[
                        [
                            'title'=>'WP Toolkit',
                            'icon'=>'phyre_customer-domains-wp',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Site Publisher',
                            'icon'=>'phyre_customer-domains-site',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Sitejet Builder',
                            'icon'=>'phyre_customer-domains-sitejet',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Domains',
                            'icon'=>'phyre_customer-domains-domain',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Redirects',
                            'icon'=>'phyre_customer-domains-redirect',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Zone Editor',
                            'icon'=>'phyre_customer-domains-zone',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Dynamic DNS',
                            'icon'=>'phyre_customer-domains-dynamic',
                            'link'=>'#'
                        ]
                    ]
                ],

                'metrics'=>[
                    'title'=>'Metrics',
                    'icon'=>'phyre_customer-metrics',
                    'menu'=>[
                        [
                            'title'=>'Visitors',
                            'icon'=>'phyre_customer-metrics-visitors',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Site Quality Monitoring',
                            'icon'=>'phyre_customer-metrics-site-quality',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Errors',
                            'icon'=>'phyre_customer-metrics-errors',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Bandwidth',
                            'icon'=>'phyre_customer-metrics-bandwidth',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Raw Access',
                            'icon'=>'phyre_customer-metrics-raw',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Awstats',
                            'icon'=>'phyre_customer-metrics-awstats',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Analog Stats',
                            'icon'=>'phyre_customer-metrics-analog',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Webalizer',
                            'icon'=>'phyre_customer-metrics-webalizer',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Webalizer FTP',
                            'icon'=>'phyre_customer-metrics-webalizer-ftp',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Metrics Editor',
                            'icon'=>'phyre_customer-metrics-editor',
                            'link'=>'#'
                        ]
                    ]
                ],

                'security'=>[
                    'title'=>'Security',
                    'icon'=>'phyre_customer-security',
                    'menu'=>[
                        [
                            'title'=>'SSH Access',
                            'icon'=>'phyre_customer-security-ssh',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'IP Blockers',
                            'icon'=>'phyre_customer-security-block',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'SSL/TLS',
                            'icon'=>'phyre_customer-security-ssl-tls',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Manage API Tokens',
                            'icon'=>'phyre_customer-security-api',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Hotlink Protection',
                            'icon'=>'phyre_customer-security-hotlink',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Leech Protection',
                            'icon'=>'phyre_customer-security-leech',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'SSL/TSL Status',
                            'icon'=>'phyre_customer-security-status',
                            'link'=>'#'
                        ]
                    ]
                ],

                'software'=>[
                    'title'=>'Software',
                    'icon'=>'phyre_customer-software',
                    'menu'=>[
                        [
                            'title'=>'PHP PEAR Packages',
                            'icon'=>'phyre_customer-software-packages',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Perl Modules',
                            'icon'=>'phyre_customer-software-perl',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Site Software',
                            'icon'=>'phyre_customer-software-site',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Optimaze Website',
                            'icon'=>'phyre_customer-software-optimaze',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'MultiPHP Manager',
                            'icon'=>'phyre_customer-software-manager',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'MultiPHP INI Editor',
                            'icon'=>'phyre_customer-software-editor',
                            'link'=>'#'
                        ]
                    ]
                ],

                'advanced'=>[
                    'title'=>'Advanced',
                    'icon'=>'phyre_customer-advanced',
                    'menu'=>[
                        [
                            'title'=>'Cron Jobs',
                            'icon'=>'phyre_customer-advanced-cron',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Track DNS',
                            'icon'=>'phyre_customer-advanced-dns',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Indexes',
                            'icon'=>'phyre_customer-advanced-indexes',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Error Pages',
                            'icon'=>'phyre_customer-advanced-error',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Apache Handlers',
                            'icon'=>'phyre_customer-advanced-apache',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'MIME Types',
                            'icon'=>'phyre_customer-advanced-mime',
                            'link'=>'#'
                        ]
                    ]
                ],

                'preferences'=>[
                    'title'=>'Preferences',
                    'icon'=>'phyre_customer-preferences',
                    'menu'=>[
                        [
                            'title'=>'Password & Security',
                            'icon'=>'phyre_customer-preferences-pass',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Change Language',
                            'icon'=>'phyre_customer-preferences-language',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'Contact Information',
                            'icon'=>'phyre_customer-preferences-contact',
                            'link'=>'#'
                        ],
                        [
                            'title'=>'User Manager',
                            'icon'=>'phyre_customer-preferences-user',
                            'link'=>'#'
                        ]
                    ]
                ]


            ],
        ];

    }
}
