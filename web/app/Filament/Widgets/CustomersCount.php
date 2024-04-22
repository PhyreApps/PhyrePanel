<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Domain;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomersCount extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected function getStats(): array
    {

        //        $serverStatistic = new \App\Statistics\ServerStatistic();
        //        $serverStats = $serverStatistic->getCurrentStats();

        $customersCount = Customer::count();
        $websiteCount = Domain::count();

        return [
            //            Stat::make('Total Memory', $serverStats['memory']['total']),
            //            Stat::make('Used Memory', $serverStats['memory']['used']),
            //            Stat::make('Free Memory', $serverStats['memory']['free']),
            //            Stat::make('Shared Memory', $serverStats['memory']['shared']),
            //            Stat::make('Buffer Cache', $serverStats['memory']['buffCache']),
            //            Stat::make('Available Memory', $serverStats['memory']['available']),
            //

            Stat::make('Websites', $websiteCount)->icon('heroicon-o-globe-alt'),
            Stat::make('Customers', $customersCount)->icon('heroicon-o-users'),
            Stat::make('Active Customers', $customersCount)->icon('heroicon-o-user-group'),
            Stat::make('Inactive Customers', 0)->icon('heroicon-o-user-minus'),
        ];
    }
}
