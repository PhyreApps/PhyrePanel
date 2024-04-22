<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServerMemoryStatistic extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'serverMemoryStatisticChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'RAM Usage';

    protected static ?int $sort = 2;

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

        return view('filament.widgets.server-memory-statistic', ['data' => $serverStats]);
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $serverStatistic = new \App\Statistics\ServerStatistic();
        $serverStats = $serverStatistic->getCurrentStats();

        $memoryUsedPercentage = 0;
        $memoryFreePercentage = 0;

        $totalMemory = $serverStats['memory']['totalGb'];
        $availableMemory = $serverStats['memory']['availableGb'];

        if ($totalMemory > 0) {
            $memoryUsedPercentage = ($totalMemory - $availableMemory) / $totalMemory * 100;
            $memoryFreePercentage = 100 - $memoryUsedPercentage;
        }

        $memoryUsedPercentage = round($memoryUsedPercentage, 0);
        $memoryFreePercentage = round($memoryFreePercentage, 0);

        return [
            'chart' => [
                'type' => 'radialBar',
                'height' => 200,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [$memoryUsedPercentage],
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
            'labels' => ['Used RAM'],
            'colors' => ['#16a34a'],

        ];
    }
}
