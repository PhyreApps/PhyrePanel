<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ServerMemoryStatisticCount extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected static string $view = 'filament.widgets.server-memory-statistic-count';

    protected function getViewData(): array
    {
        $serverStatistic = new \App\Statistics\ServerStatistic();
        $serverStats = $serverStatistic->getCurrentStats();

        return [
            'serverStats' => $serverStats,
        ];

    }
}
