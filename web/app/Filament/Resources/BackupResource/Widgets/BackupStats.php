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
        $findBackups = Backup::select(['id'])->where('status', 'processing')->get();
        if ($findBackups->count() > 0) {
            foreach ($findBackups as $backup) {
                $backup->checkBackup();
            }
        }
        $usedSpace = 0;
        $backupPath = BackupStorage::getPath();
        if (is_dir($backupPath)) {
            $usedSpace = $this->getDirectorySize($backupPath);
        }

        return [
            Stat::make('Total backups', Backup::count()),
            Stat::make('Total used space', $usedSpace),
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
