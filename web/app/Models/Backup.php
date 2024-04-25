<?php

namespace App\Models;

use App\Filament\Enums\BackupStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class Backup extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'hosting_subscription_id',
        'backup_type',
        'status',
        'path',
        'size',
        'disk',
    ];

    protected $casts = [
        'status' => BackupStatus::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->status = 'pending';
            $model->checkCronJob();
        });

        static::deleting(function ($model) {
           if (is_dir($model->path)) {
               shell_exec('rm -rf ' . $model->path);
           }
        });
    }

    private function checkCronJob()
    {
        $cronJobCommand = 'phyre-php /usr/local/phyre/web/artisan phyre:run-backup';
        $findCronJob = CronJob::where('command', $cronJobCommand)->first();
        if (! $findCronJob) {
            $cronJob = new CronJob();
            $cronJob->schedule = '*/5 * * * *';
            $cronJob->command = $cronJobCommand;
            $cronJob->user = 'root';
            $cronJob->save();
        }
    }

    protected function backupRelated() : Attribute
    {
        $relatedWith = $this->backup_type;
        if ($this->backup_type === 'hosting_subscription') {
            $findHostingSubscription = HostingSubscription::where('id', $this->hosting_subscription_id)->first();
            if ($findHostingSubscription) {
                $relatedWith = $findHostingSubscription->domain;
            }
        }

        return Attribute::make(
            get: fn () => $relatedWith
        );
    }

    public function checkBackup()
    {
        if ($this->status == BackupStatus::Processing) {

            $backupDoneFile = $this->path.'/backup.done';
            if (file_exists($backupDoneFile)) {
                $this->size = $this->checkPathSize($this->path);
                $this->status = 'completed';
                $this->completed = true;
                $this->completed_at = now();
                $this->save();
                return [
                    'status' => 'completed',
                    'message' => 'Backup completed'
                ];
            }

            $checkProcess = shell_exec('ps -p ' . $this->process_id . ' | grep ' . $this->process_id);
            if (Str::contains($checkProcess, $this->process_id)) {

                $this->size = $this->checkPathSize($this->path);
                $this->save();

                return [
                    'status' => 'processing',
                    'message' => 'Backup is still processing'
                ];
            } else {
                $this->status = 'failed';
                $this->save();
                return [
                    'status' => 'failed',
                    'message' => 'Backup failed'
                ];
            }
        }
    }

    public function startBackup()
    {
        if ($this->status == BackupStatus::Processing) {
            return [
                'status' => 'processing',
                'message' => 'Backup is already processing'
            ];
        }

        $storagePath = storage_path('backups');
        if (! is_dir($storagePath)) {
            mkdir($storagePath);
        }
        $backupPath = $storagePath.'/'.$this->id;
        if (! is_dir($backupPath)) {
            mkdir($backupPath);
        }
        $backupTempPath = $backupPath.'/temp';
        if (! is_dir($backupTempPath)) {
            mkdir($backupTempPath);
        }
        if ($this->backup_type == 'hosting_subscription') {
            $findHostingSubscription = HostingSubscription::where('id', $this->hosting_subscription_id)->first();
            if ($findHostingSubscription) {

                $backupFileName = Str::slug($findHostingSubscription->domain .'-'. date('Ymd-His')) . '.tar.gz';
                $backupFilePath = $backupPath.'/'.$backupFileName;

                $backupLogFileName = 'backup.log';
                $backupLogFilePath = $backupPath.'/'.$backupLogFileName;

                $backupTempScript = '/tmp/backup-script-'.$this->id.'.sh';
                $shellFileContent = '';
                $shellFileContent .= 'echo "Backup up domain: '.$findHostingSubscription->domain . PHP_EOL;
                $shellFileContent .= 'echo "Backup filename: '.$backupFileName. PHP_EOL;
                $shellFileContent .= 'cp -r /home/'.$findHostingSubscription->system_username.' '.$backupTempPath.PHP_EOL;

                $shellFileContent .= 'cd '.$backupTempPath .' && tar -czvf '.$backupFilePath.' ./* '. PHP_EOL;

                $shellFileContent .= 'rm -rf '.$backupTempPath.PHP_EOL;
                $shellFileContent .= 'echo "Backup complete"' . PHP_EOL;
                $shellFileContent .= 'touch ' . $backupPath. '/backup.done' . PHP_EOL;
                $shellFileContent .= 'rm -rf ' . $backupTempScript;

                file_put_contents($backupTempScript, $shellFileContent);

                $processId = shell_exec('bash '.$backupTempScript.' >> ' . $backupLogFilePath . ' & echo $!');
                $processId = intval($processId);

                if ($processId > 0 && is_numeric($processId)) {

                    $this->path = $backupPath;
                    $this->filepath = $backupFilePath;
                    $this->status = 'processing';
                    $this->queued = true;
                    $this->queued_at = now();
                    $this->process_id = $processId;
                    $this->save();

                    return [
                        'status' => 'processing',
                        'message' => 'Backup started'
                    ];
                } else {
                    $this->status = 'failed';
                    $this->save();
                    return [
                        'status' => 'failed',
                        'message' => 'Backup failed to start'
                    ];
                }

            } else {
                $this->status = 'failed';
                $this->save();
                return [
                    'status' => 'failed',
                    'message' => 'Hosting subscription not found'
                ];
            }
        }

    }

    private function checkPathSize($path)
    {
        // Check path size
        $pathSize = shell_exec('du -sh ' . $this->path);
        $pathSize = trim($pathSize);
        $pathSize = explode("\t", $pathSize);

        if (isset($pathSize[0])) {
            $pathSize = $pathSize[0];
            return $pathSize;
        }

        return 0;
    }
}
