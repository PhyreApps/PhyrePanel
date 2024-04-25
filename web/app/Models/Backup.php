<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'hosting_subscription_id',
        'backup_type',
        'status',
        'path',
        'size',
        'disk',
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
        if ($this->status == 'running') {
            $backupDoneFile = $this->path.'/backup.done';
            if (file_exists($backupDoneFile)) {
                $this->size = filesize($this->filepath);
                $this->status = 'completed';
                $this->completed = true;
                $this->completed_at = now();
                $this->save();
            }
        }
    }

    public function startBackup()
    {

        if ($this->status == 'running') {
            return [
                'status' => 'running',
                'message' => 'Backup already running'
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

                $backupFileName = Str::slug($findHostingSubscription->domain .'-'. date('Y-m-d-H-i-s')) . '.tar.gz';
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

                $this->path = $backupPath;
                $this->filepath = $backupFilePath;
                $this->status = 'running';
                $this->queued = true;
                $this->queued_at = now();
                $this->save();

                file_put_contents($backupTempScript, $shellFileContent);
                shell_exec('bash '.$backupTempScript.' >> ' . $backupLogFilePath . ' &');

                return [
                    'status' => 'running',
                    'message' => 'Backup started'
                ];

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
}
