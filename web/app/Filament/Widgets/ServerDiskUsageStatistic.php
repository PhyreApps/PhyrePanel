<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServerDiskUsageStatistic extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'serverDiskUsageStatisticChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Disk Usage';

    protected static ?int $sort = 1;

    /**
     * Widget content height
     */
    protected static ?int $contentHeight = 100;

    /**
     * Widget Footer
     */
    protected function getFooter(): \Illuminate\View\View
    {
        $serverStatistic = new \App\Statistics\ServerStatistic();
        $serverStats = $serverStatistic->getCurrentStats();

        return view('charts.order-status.footer', ['data' => $serverStats]);
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $serverStatistic = new \App\Statistics\ServerStatistic();
        $serverStats = $serverStatistic->getCurrentStats();

        $userPercentage = $serverStats['disk']['usedPercentage'];
        $userPercentage = str_replace('%', '', $userPercentage);
        $userPercentage = floatval($userPercentage);

        return [
            'chart' => [
                'type' => 'radialBar',
                'height' => 200,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [$userPercentage],
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => 0,
                    'endAngle' => 360,
                    'hollow' => [
                        'size' => '60%',
                        'background' => 'transparent',
                    ],
                    'track' => [
                        'background' => 'transparent',
                        'strokeWidth' => '100%',
                    ],
                    'dataLabels' => [
                        'show' => true,
                        'name' => [
                            'show' => true,
                            'offsetY' => -10,
                            'fontWeight' => 600,
                            'fontFamily' => 'inherit',
                        ],
                        'value' => [
                            'show' => true,
                            'fontWeight' => 600,
                            'fontSize' => '24px',
                            'fontFamily' => 'inherit',
                        ],
                    ],

                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'horizontal',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#f59e0b'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 0.6,
                    'stops' => [30, 70, 100],
                ],
            ],
            'stroke' => [
                'dashArray' => 10,
            ],
            'labels' => ['Used Space'],
            'colors' => ['#16a34a'],

        ];
    }
}
