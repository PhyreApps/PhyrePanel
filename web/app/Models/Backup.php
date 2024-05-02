<?php

namespace App\Models;

use App\Filament\Enums\BackupStatus;
use App\Helpers;
use App\ShellApi;
use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class Backup extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
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

        static::created(function ($model) {
            $model->startBackup();
        });

        static::deleting(function ($model) {
            if (is_file($model->filepath)) {
//           ShellApi::safeDelete($model->path, [
//              Storage::path('backups')
//           ]);
                if (Storage::disk('backups')->exists($model->filepath)) {
                    Storage::disk('backups')->delete($model->filepath);
                }
            }
        });
    }

    public function checkCronJob()
    {
        $cronJobCommand = 'phyre-php /usr/local/phyre/web/artisan phyre:run-backup-checks';
        $findCronJob = CronJob::where('command', $cronJobCommand)->first();
        if (! $findCronJob) {
            $cronJob = new CronJob();
            $cronJob->schedule = '*/5 * * * *';
            $cronJob->command = $cronJobCommand;
            $cronJob->user = 'root';
            $cronJob->save();
        }

        $cronJobCommand = 'phyre-php /usr/local/phyre/web/artisan phyre:create-daily-full-backup';
        $findCronJob = CronJob::where('command', $cronJobCommand)->first();
        if (! $findCronJob) {
            $cronJob = new CronJob();
            $cronJob->schedule = '0 0 * * *';
            $cronJob->command = $cronJobCommand;
            $cronJob->user = 'root';
            $cronJob->save();
        }

        return true;
    }

    public function checkBackup()
    {
        if ($this->status == BackupStatus::Processing) {

            $backupDoneFile = $this->path.'/backup.done';
            if (file_exists($backupDoneFile)) {

                $tempValidatePath = $this->path.'/temp-validate';
                if (! is_dir($tempValidatePath)) {
                    mkdir($tempValidatePath);
                }

                shell_exec('cd '.$tempValidatePath.' && unzip -o '.Storage::disk('backups')->path($this->filepath));

                $validateDatabaseFile = $tempValidatePath.'/database.sql';
                $validateEnvFile = $tempValidatePath.'/.env';

                $errorsBag = [];
                if (! file_exists($validateDatabaseFile)) {
                    $errorsBag[] = 'Database file not found';
                }
                if (! file_exists($validateEnvFile)) {
                    $errorsBag[] = 'Env file not found';
                }
                if (count($errorsBag) > 0) {
                    $this->status = 'failed';
                    $this->save();
                    return [
                        'status' => 'failed',
                        'message' => 'Backup failed. Database or env file missing.',
                        'errors' => $errorsBag
                    ];
                }
                $originalEnvContent = file_get_contents(base_path().'/.env');
                $backupEnvContent = file_get_contents($validateEnvFile);
                if ($originalEnvContent != $backupEnvContent) {
                    $errorsBag[] = 'Env file content mismatch';
                }

                if (count($errorsBag) > 0) {
                    $this->status = 'failed';
                    $this->save();
                    return [
                        'status' => 'failed',
                        'message' => 'Backup failed',
                        'errors' => $errorsBag
                    ];
                }

                ShellApi::safeDelete($this->path,[
                    Storage::path('backups')
                ]);

                $this->size = filesize(Storage::disk('backups')->path($this->filepath));
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
        if ($this->status == BackupStatus::Processing) {
            return [
                'status' => 'processing',
                'message' => 'Backup is already processing'
            ];
        }

        $storagePath = Storage::path('backups');
        if (! is_dir($storagePath)) {
            mkdir($storagePath);
        }
        $backupPath = $storagePath.'/'.$this->id;
        if (!is_dir(dirname($backupPath))) {
            mkdir(dirname($backupPath));
        }
        if (! is_dir($backupPath)) {
            mkdir($backupPath);
        }
        $backupTempPath = $backupPath.'/temp';
        if (! is_dir($backupTempPath)) {
            mkdir($backupTempPath);
        }

        $backupFilename = 'phyre-backup-'.date('Ymd-His').'.zip';
        $backupFilePath = $storagePath.'/' . $backupFilename;

        if ($this->backup_type == 'full') {

            // Export Phyre Panel database
            $databaseBackupPath = $backupTempPath.'/database.sql';

            $backupLogFileName = 'backup.log';
            $backupLogFilePath = $backupPath.'/'.$backupLogFileName;

            $backupTempScript = '/tmp/backup-script-'.$this->id.'.sh';
            $shellFileContent = '';
            $shellFileContent .= 'echo "Backup Phyre Panel files"'. PHP_EOL;

            // Export Phyre Panel database
            $shellFileContent .= 'mysqldump -u "'.env('MYSQl_ROOT_USERNAME').'" -p"'.env('MYSQL_ROOT_PASSWORD').'" "'.env('DB_DATABASE').'" > '.$databaseBackupPath . PHP_EOL;

            // Export Phyre Panel ENV
            $getEnv = Dotenv::createArrayBacked(base_path())->load();
            $backupStructure = [
                'env'=>$getEnv,
            ];
            file_put_contents($backupTempPath.'/backup.json', json_encode($backupStructure, JSON_PRETTY_PRINT));
            $shellFileContent .= 'echo "Backup Phyre Panel ENV"'. PHP_EOL;
            $shellFileContent .= 'cp '.base_path().'/.env '.$backupTempPath.'/.env'. PHP_EOL;

            // Export Phyre Panel Hosting Subscription
            $findHostingSubscription = HostingSubscription::all();
            if ($findHostingSubscription->count() > 0) {
                foreach ($findHostingSubscription as $hostingSubscription) {
                    $hostingSubscriptionPath = $backupTempPath .'/hosting_subscriptions/'.$hostingSubscription->system_username;
                    $shellFileContent .= PHP_EOL;
                    $shellFileContent .= 'echo "Backup up hosting subscription: ' . $hostingSubscription->system_username .'" '. PHP_EOL;
                    $shellFileContent .= 'mkdir -p '.$hostingSubscriptionPath.PHP_EOL;

                    // cp -r (copy recursively, also copy hidden files)
                    $shellFileContent .= 'cp -r /home/'.$hostingSubscription->system_username.'/ ' . $hostingSubscriptionPath .PHP_EOL;

                    $shellFileContent .= 'mkdir -p '.$hostingSubscriptionPath.'/databases'.PHP_EOL;

                    $getDatabases = Database::where('hosting_subscription_id', $hostingSubscription->id)
                        ->where(function ($query) {
                            $query->where('is_remote_database_server', '0')
                                ->orWhereNull('is_remote_database_server');
                        })
                        ->get();

                    if ($getDatabases->count() > 0) {
                        foreach ($getDatabases as $database) {
                            $databaseName = $database->database_name_prefix . $database->database_name;
                            $shellFileContent .= 'echo "Backup up database: ' . $databaseName . '" ' . PHP_EOL;
                            $databaseBackupPath = $hostingSubscriptionPath . '/databases/' . $databaseName . '.sql';
                            $shellFileContent .= 'mysqldump -u "' . env('MYSQl_ROOT_USERNAME') . '" -p"' . env('MYSQL_ROOT_PASSWORD') . '" "' . $databaseName . '" > ' . $databaseBackupPath . PHP_EOL;
                        }
                    }

                    $shellFileContent .= PHP_EOL;
                }
            }

            $shellFileContent .= 'cd '.$backupTempPath .' && find . -exec zip -r '.$backupFilePath.' {} \;'. PHP_EOL;

            $shellFileContent .= 'rm -rf '.$backupTempPath.PHP_EOL;
            $shellFileContent .= 'echo "Backup complete"' . PHP_EOL;
            $shellFileContent .= 'touch ' . $backupPath. '/backup.done' . PHP_EOL;
            $shellFileContent .= 'rm -rf ' . $backupTempScript;

            file_put_contents($backupTempScript, $shellFileContent);

            $processId = shell_exec('bash '.$backupTempScript.' >> ' . $backupLogFilePath . ' & echo $!');
            $processId = intval($processId);

            if ($processId > 0 && is_numeric($processId)) {

                $this->path = $backupPath;
                $this->filepath = $backupFilename;
                $this->status = 'processing';
                $this->queued = true;
                $this->queued_at = now();
                $this->process_id = $processId;
                $this->save();

                return [
                    'status' => 'processing',
                    'message' => 'System backup started'
                ];
            } else {
                $this->status = 'failed';
                $this->save();
                return [
                    'status' => 'failed',
                    'message' => 'System backup failed to start'
                ];
            }

        }
    }
}
