<?php

namespace App\Models;

use App\BackupStorage;
use App\Filament\Enums\BackupStatus;
use App\Helpers;
use App\Jobs\ProcessHostingSubscriptionBackup;
use App\PhyreConfig;
use App\ShellApi;
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
            ProcessHostingSubscriptionBackup::dispatch($model->id);
        });

        static::deleting(function ($model) {
            if (is_file($model->file_path)) {
                shell_exec('rm -rf ' . $model->file_path);
            }
        });
    }

    public function checkCronJob()
    {/*
        $cronJobCommand = 'phyre-php /usr/local/phyre/web/artisan phyre:create-daily-full-hosting-subscriptions-backup';
        $findCronJob = CronJob::where('command', $cronJobCommand)->first();
        if (! $findCronJob) {
            $cronJob = new CronJob();
            $cronJob->schedule = '0 0 * * *';
            $cronJob->command = $cronJobCommand;
            $cronJob->user = 'root';
            $cronJob->save();
        }*/

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

            $backupDoneFile = $this->path.'/backup.done';
            $backupZipFile = $this->file_path;

            if (file_exists($backupDoneFile) && file_exists($backupZipFile)) {

                $this->size = filesize($this->file_path);
                $this->status = 'completed';
                $this->completed = true;
                $this->completed_at = now();
                $this->save();

                ShellApi::safeDelete($this->path,[
                    $this->root_path
                ]);
                ShellApi::safeDelete($this->temp_path,[
                    $this->root_path
                ]);

                return [
                    'status' => 'completed',
                    'message' => 'Backup completed'
                ];
            }

            $checkProcess = shell_exec('ps -p ' . $this->process_id . ' | grep ' . $this->process_id);

            if (Str::contains($checkProcess, $this->process_id)) {

                $this->size = 0;
                $this->backup_log = "Backup is started with process id: $this->process_id";
                $this->save();

                return [
                    'status' => 'processing',
                    'message' => 'Backup is still processing'
                ];
            } else {
                $this->status = 'failed';
                $this->backup_log = "Backup failed. Process not found";
                $this->save();
                return [
                    'status' => 'failed',
                    'message' => 'Backup failed. Process not found'
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

        if ($this->status !== BackupStatus::Pending) {
            return [
                'status' => 'failed',
                'message' => 'Backup already started'
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

        $backupStorageRootPath = '/var/lib/phyre/backups/hosting_subscriptions';
        $backupPath = $backupStorageRootPath . '/' . $findHostingSubscription->customer_id;

        $backupTempPath = $backupPath.'/temp';
        shell_exec('mkdir -p ' . $backupTempPath);

        $backupFileName = Str::slug($findHostingSubscription->system_username .'-'. date('Ymd-His')) . '.zip';
        $backupFilePath = $backupStorageRootPath.'/'.$backupFileName;

        $backupLogFilePath = $backupPath.'/backup.log';

        $backupTempScript = '/tmp/backup-script-'.$this->id.'.sh';
        $shellFileContent = '';
        $shellFileContent .= 'echo "Backup up user: '.$findHostingSubscription->system_username .'"'. PHP_EOL;
        $shellFileContent .= 'echo "Backup filename: '.$backupFileName.'"' . PHP_EOL;

        if ($this->backup_type == 'full') {
            $shellFileContent .= 'cp -r /home/' . $findHostingSubscription->system_username . ' ' . $backupTempPath . PHP_EOL;
            $shellFileContent .= 'rsync -azP '. $backupTempPath . '/' . $findHostingSubscription->system_username . '/ ' . $backupTempPath . PHP_EOL;
            $shellFileContent .= 'rm -rf '. $backupTempPath . '/' . $findHostingSubscription->system_username . '/' . PHP_EOL;
        }

        if ($this->backup_type == 'full' || $this->backup_type == 'database') {

            // Export Phyre Panel database
            $mysqlAuthConf = '/root/.phyre-mysql.cnf';
            $mysqlAuthContent = '[client]' . PHP_EOL;
            $mysqlAuthContent .= 'user="' . PhyreConfig::get('MYSQL_ROOT_USERNAME') .'"'. PHP_EOL;
            $mysqlAuthContent .= 'password="' . PhyreConfig::get('MYSQL_ROOT_PASSWORD') . '"' . PHP_EOL;
            file_put_contents($mysqlAuthConf, $mysqlAuthContent);

            $getDatabases = Database::where('hosting_subscription_id', $findHostingSubscription->id)
                ->where(function ($query) {
                    $query->where('is_remote_database_server', '0')
                        ->orWhereNull('is_remote_database_server');
                })
                ->get();

            if ($getDatabases->count() > 0) {
                foreach ($getDatabases as $database) {
                    $databaseName = $database->database_name_prefix . $database->database_name;

                    $shellFileContent .= 'echo "Backup up database: ' . $databaseName .'" '. PHP_EOL;
                    $shellFileContent .= 'mkdir -p '.$backupTempPath . '/databases' . PHP_EOL;
                    $databaseBackupPath = $backupTempPath . '/databases/' . $databaseName . '.sql';
                    $shellFileContent .= 'mysqldump --defaults-extra-file='.$mysqlAuthConf.' "'.$databaseName.'" > '.$databaseBackupPath . PHP_EOL;

                }
            }
        }

        // With find, we can search for all files,directories (including hidden) in the current directory and zip them
        $shellFileContent .= 'cd '.$backupTempPath .' && find . -exec zip --symlinks -r '.$backupFilePath.' {} \;'. PHP_EOL;

        $shellFileContent .= 'rm -rf '.$backupTempPath.PHP_EOL;
        $shellFileContent .= 'echo "Backup complete"' . PHP_EOL;
        $shellFileContent .= 'touch ' . $backupPath. '/backup.done' . PHP_EOL;
        $shellFileContent .= 'rm -rf ' . $backupTempScript . PHP_EOL;

        file_put_contents($backupTempScript, $shellFileContent);

        $processId = shell_exec('bash '.$backupTempScript.' >> ' . $backupLogFilePath . ' & echo $!');
        $processId = intval($processId);

        if ($processId > 0 && is_numeric($processId)) {

            $this->path = $backupPath;
            $this->root_path = $backupStorageRootPath;
            $this->temp_path = $backupTempPath;
            $this->file_path = $backupFilePath;
            $this->file_name = $backupFileName;

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
