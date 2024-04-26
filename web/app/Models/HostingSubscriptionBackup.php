<?php

namespace App\Models;

use App\Filament\Enums\BackupStatus;
use App\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HostingSubscriptionBackup extends Model
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

    protected static function booted(): void
    {
        static::addGlobalScope('customer', function (Builder $query) {
            if (auth()->check() && auth()->guard()->name == 'web_customer') {
                $query->whereHas('hostingSubscription', function ($query) {
                    $query->where('customer_id', auth()->user()->id);
                });
            }
        });
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->status = 'pending';
            $model->checkCronJob();
        });

        static::created(function ($model) {
            $model->startBackup();
        });

        static::deleting(function ($model) {
            if (is_file($model->filepath)) {
                shell_exec('rm -rf ' . $model->filepath);
            }
        });
    }

    public function checkCronJob()
    {
        $cronJobCommand = 'phyre-php /usr/local/phyre/web/artisan phyre:run-hosting-subscriptions-backup';
        $findCronJob = CronJob::where('command', $cronJobCommand)->first();
        if (! $findCronJob) {
            $cronJob = new CronJob();
            $cronJob->schedule = '*/5 * * * *';
            $cronJob->command = $cronJobCommand;
            $cronJob->user = 'root';
            $cronJob->save();
            return false;
        }
        return true;
    }

    public function checkBackup()
    {
        $findHostingSubscription = HostingSubscription::select(['id'])
            ->where('id', $this->hosting_subscription_id)
            ->first();
        if (! $findHostingSubscription) {
            $this->delete();
            return [
                'status' => 'failed',
                'message' => 'Hosting subscription not found'
            ];
        }

        if ($this->status == BackupStatus::Processing) {

            $backupDoneFile = $this->path.'/backup-'.$this->id.'.done';
            if (file_exists($backupDoneFile)) {

                $this->size = Helpers::checkPathSize($this->path);
                $this->status = 'completed';
                $this->completed = true;
                $this->completed_at = now();
                $this->save();

                shell_exec('rm -rf ' . $backupDoneFile);

                return [
                    'status' => 'completed',
                    'message' => 'Backup completed'
                ];
            }

            $checkProcess = shell_exec('ps -p ' . $this->process_id . ' | grep ' . $this->process_id);
            if (Str::contains($checkProcess, $this->process_id)) {

                $this->size = Helpers::checkPathSize($this->path);
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
        $findHostingSubscription = HostingSubscription::where('id', $this->hosting_subscription_id)
            ->first();
        if (! $findHostingSubscription) {
            $this->delete();
            return [
                'status' => 'failed',
                'message' => 'Hosting subscription not found'
            ];
        }

        if ($this->status == BackupStatus::Processing) {
            return [
                'status' => 'processing',
                'message' => 'Backup is already processing'
            ];
        }
        $findMainDomain = Domain::where('hosting_subscription_id', $findHostingSubscription->id)
            ->where('is_main', 1)
            ->first();
        if (! $findMainDomain) {
            $this->delete();
            return [
                'status' => 'failed',
                'message' => 'Main domain not found'
            ];
        }

        $storagePath = storage_path('backups');
        $backupPath = $storagePath.'/hosting_subscriptions/'.$this->backup_type.'/'.$this->id;
        $backupTempPath = $backupPath.'/temp';
        shell_exec('mkdir -p ' . $backupTempPath);


        $backupFileName = Str::slug($findHostingSubscription->system_username .'-'. date('Ymd-His')) . '.tar.gz';
        $backupFilePath = $backupPath.'/'.$backupFileName;

        $backupLogFileName = 'backup.log';
        $backupLogFilePath = $backupPath.'/'.$backupLogFileName;

        $backupTargetPath = $findMainDomain->domain_root . '/backups';
        $backupTargetFilePath = $backupTargetPath.'/'.$backupFileName;

        $backupTempScript = '/tmp/backup-script-'.$this->id.'.sh';
        $shellFileContent = '';
        $shellFileContent .= 'mkdir -p '. $backupTargetPath.PHP_EOL;
        $shellFileContent .= 'echo "Backup up domain: '.$findHostingSubscription->domain .'"'. PHP_EOL;
        $shellFileContent .= 'echo "Backup filename: '.$backupFileName.'"' . PHP_EOL;

        if ($this->backup_type == 'full') {
            $shellFileContent .= 'cp -r /home/' . $findHostingSubscription->system_username . ' ' . $backupTempPath . PHP_EOL;
        }

        if ($this->backup_type == 'full' || $this->backup_type == 'database') {
            $getDatabases = Database::where('hosting_subscription_id', $findHostingSubscription->id)
                ->where(function ($query) {
                    $query->where('is_remote_database_server', '0')
                        ->orWhereNull('is_remote_database_server');
                })
                ->get();

            if ($getDatabases->count() > 0) {
                foreach ($getDatabases as $database) {
//                    $findDatabaseUser = DatabaseUser::where('database_id', $database->id)
//                        ->first();
//                    if (!$findDatabaseUser) {
//                        continue;
//                    }
                    $databaseName = $database->database_name_prefix . $database->database_name;
//                    $databaseUser = $findDatabaseUser->username_prefix . $findDatabaseUser->username;
//                    $databaseUserPassword = $findDatabaseUser->password;

                    $shellFileContent .= 'echo "Backup up database: ' . $databaseName .'" '. PHP_EOL;
               //     $shellFileContent .= 'echo "Backup up database user: ' . $databaseUser .'" '. PHP_EOL;
                    $databaseBackupPath = $backupTempPath . '/' . $databaseName . '.sql';
                    $shellFileContent .= 'mysqldump -u "'.env('MYSQl_ROOT_USERNAME').'" -p"'.env('MYSQL_ROOT_PASSWORD').'" "'.$databaseName.'" > '.$databaseBackupPath . PHP_EOL;

                }
            }
        }

        $shellFileContent .= 'cd '.$backupTempPath .' && tar -czvf '.$backupFilePath.' ./* '. PHP_EOL;

        $shellFileContent .= 'rm -rf '.$backupTempPath.PHP_EOL;
        $shellFileContent .= 'echo "Backup complete"' . PHP_EOL;
        $shellFileContent .= 'touch ' . $backupTargetPath. '/backup-'.$this->id.'.done' . PHP_EOL;
        $shellFileContent .= 'mv '.$backupFilePath.' '. $backupTargetFilePath.PHP_EOL;
        $shellFileContent .= 'rm -rf ' . $backupTempScript . PHP_EOL;

        file_put_contents($backupTempScript, $shellFileContent);

        $processId = shell_exec('bash '.$backupTempScript.' >> ' . $backupLogFilePath . ' & echo $!');
        $processId = intval($processId);

        if ($processId > 0 && is_numeric($processId)) {

            $this->path = $findMainDomain->domain_root . '/backups';
            $this->filepath = $backupTargetFilePath;
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

    }

    public function hostingSubscription()
    {
        return $this->belongsTo(HostingSubscription::class);
    }
}
