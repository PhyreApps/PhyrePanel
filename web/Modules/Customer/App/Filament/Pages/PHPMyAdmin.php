<?php

namespace Modules\Customer\App\Filament\Pages;

use App\Models\HostingSubscription;
use Filament\Pages\Page;

class PHPMyAdmin extends Page
{
    protected static ?string $navigationIcon = 'phyre_customer-database-php';

    protected static string $view = 'customer::filament.pages.phpmyadmin';

    protected static ?string $navigationGroup = 'Hosting';

    protected static ?string $navigationLabel = 'phpMyAdmin';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'phpmyadmin';

    protected static ?string $title = 'phpMyAdmin';


    protected function getViewData(): array
    {
        $findHostingSubscriptions = HostingSubscription::all();

        return [
            'hostingSubscriptions' => $findHostingSubscriptions,
        ];
    }

}
