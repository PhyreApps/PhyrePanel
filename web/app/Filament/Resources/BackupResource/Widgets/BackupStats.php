<?php

namespace app\Filament\Resources\BackupResource\Widgets;

use App\BackupStorage;
use App\Filament\Resources\BackupResource\Pages\ListBackups;
use App\Filament\Resources\Shop\OrderResource\Pages\ListOrders;
use App\Models\Backup;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Cache;

class BackupStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListBackups::class;
    }

    protected function getStats(): array
    {

        $stats = Cache::remember('backup-stats', 300, function () {
            $usedSpace = 0;
            $backupPath = BackupStorage::getPath();
            if (is_dir($backupPath)) {
                $usedSpace = $this->getDirectorySize($backupPath);
            }
            return [
                'totalBackups' => Backup::count(),
                'usedSpace' => $usedSpace,
            ];
        });

        return [
            Stat::make('Total backups', $stats['totalBackups']),
            Stat::make('Total used space', $stats['usedSpace']),
        ];
    }

    public function getDirectorySize($path)
    {
        $output = shell_exec('du -sh ' . $path);
        $output = explode("\t", $output);

        if (isset($output[0])) {
            return $output[0];
        }

        return 0;
    }
}
