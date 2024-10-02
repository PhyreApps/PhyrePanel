<?php

namespace App\Models;

use App\BackupStorage;
use App\Filament\Enums\BackupStatus;
use App\Helpers;
use App\PhyreConfig;
use App\ShellApi;
use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            if (is_file($model->file_path)) {
                shell_exec('rm -rf ' . $model->file_path);
            }
        });
    }

    public function checkCronJob()
    {
        
        //$cronJobCommand = 'phyre-php /usr/local/phyre/web/artisan phyre:run-backup-checks';
        //$findCronJob = CronJob::where('command', $cronJobCommand)->first();
        //if (! $findCronJob) {
          //  $cronJob = new CronJob();
         //   $cronJob->schedule = '*/5 * * * *';
            //$cronJob->command = $cronJobCommand;
            //$cronJob->user = 'root';
            //$cronJob->save();
        //}

        // $cronJobCommand = 'phyre-php /usr/local/phyre/web/artisan phyre:create-daily-full-backup';
        // $findCronJob = CronJob::where('command', $cronJobCommand)->first();
        // if (! $findCronJob) {
        //     $cronJob = new CronJob();
        //     $cronJob->schedule = '0 0 * * *';
        //     $cronJob->command = $cronJobCommand;
        //     $cronJob->user = 'root';
        //     $cronJob->save();
        // }

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

                shell_exec('cd '.$tempValidatePath.' && unzip -o '.$this->file_path);

                $validateDatabaseFile = $tempValidatePath.'/database.sql';
                $validatePhyreConfigFile = $tempValidatePath.'/phyre-config.ini';

                $errorsBag = [];
                if (! file_exists($validateDatabaseFile)) {
                    $errorsBag[] = 'Database file not found';
                }
                if (! file_exists($validatePhyreConfigFile)) {
                    $errorsBag[] = 'Phyre config file not found';
                }
                if (count($errorsBag) > 0) {
                    $this->status = 'failed';
                    $this->backup_log = 'Backup failed. Database or phyre config file missing.';
                    $this->save();
                    return [
                        'status' => 'failed',
                        'message' => 'Backup failed. Database or phyre config file missing.',
                        'errors' => $errorsBag
                    ];
                }
                $originalPhyreConfigContent = file_get_contents(base_path().'/phyre-config.ini');
                $backupPhyreConfigContent = file_get_contents($validatePhyreConfigFile);
                if ($originalPhyreConfigContent != $backupPhyreConfigContent) {
                    $errorsBag[] = 'Phyre config content mismatch';
                }

                if (count($errorsBag) > 0) {
                    $this->status = 'failed';
                    $this->backup_log = 'Backup failed. Database or phyre config file content mismatch.';
                    $this->save();
                    return [
                        'status' => 'failed',
                        'message' => 'Backup failed',
                        'errors' => $errorsBag
                    ];
                }

                ShellApi::safeDelete($this->path,[
                    $this->root_path
                ]);
                ShellApi::safeDelete($this->temp_path,[
                    $this->root_path
                ]);

                $this->size = filesize($this->file_path);
                $this->status = 'completed';
                $this->completed = true;
                $this->completed_at = now();
                $this->backup_log = 'Backup completed';
                $this->save();

                return [
                    'status' => 'completed',
                    'message' => 'Backup completed'
                ];
            }

            $checkProcess = shell_exec('ps -p ' . $this->process_id . ' | grep ' . $this->process_id);
            if (Str::contains($checkProcess, $this->process_id)) {

//                $backupLog = file_get_contents($this->path.'/backup.log');
//                $backupLog = substr($backupLog, -3000, 3000);

//                $this->size = 0;
                //$this->backup_log = $backupLog;
//                $this->save();

                return [
                    'status' => 'processing',
                    'message' => 'Backup is still processing'
                ];
            } else {
                $this->backup_log = 'Backup failed. Process not found';
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

        $storagePath = BackupStorage::getPath();
        $backupPath = $storagePath.'/'.$this->id;
        $backupTempPath = $backupPath.'/temp';
        if (! is_dir($backupTempPath)) {
            shell_exec('mkdir -p '.$backupTempPath);
        }

        $backupFilename = 'phyre-backup-'.date('Ymd-His').'.zip';
        $backupFilePath = $storagePath.'/' . $backupFilename;

        // Export Phyre Panel database
        $databaseBackupPath = $backupTempPath.'/database.sql';

        $backupLogFileName = 'backup.log';
        $backupLogFilePath = $backupPath.'/'.$backupLogFileName;

        $backupTempScript = '/tmp/backup-script-'.$this->id.'.sh';
        $shellFileContent = '';
        $shellFileContent .= 'echo "Backup Phyre Panel files"'. PHP_EOL;

        // Export Phyre Panel database
        $mysqlAuthConf = '/root/.phyre-mysql.cnf';
        $mysqlAuthContent = '[client]' . PHP_EOL;
        $mysqlAuthContent .= 'user="' . PhyreConfig::get('MYSQL_ROOT_USERNAME') .'"'. PHP_EOL;
        $mysqlAuthContent .= 'password="' . PhyreConfig::get('MYSQL_ROOT_PASSWORD') . '"' . PHP_EOL;
        file_put_contents($mysqlAuthConf, $mysqlAuthContent);

        $shellFileContent .= 'mysqldump --defaults-extra-file='.$mysqlAuthConf.' "'.PhyreConfig::get('DB_DATABASE').'" > '.$databaseBackupPath . PHP_EOL;

        // Export Phyre Panel Database
        $database = [];
        $tables = Schema::getTables();
        if (count($tables) > 0) {
            foreach ($tables as $table) {
                $tableData = [];
                $tableData['table'] = $table;
                $tableData['columns'] = Schema::getColumnListing($table['name']);
                $tableData['data'] = DB::table($table['name'])->get()->toArray();
                $database[$table['name']] = $tableData;
            }
        }

        $backupStructure = [
            'database'=>$database,
            'config'=>PhyreConfig::getAll()
        ];
        file_put_contents($backupTempPath.'/backup.json', json_encode($backupStructure, JSON_PRETTY_PRINT));
        $shellFileContent .= 'echo "Backup Phyre Panel Config"'. PHP_EOL;
        $shellFileContent .= 'cp '.base_path().'/phyre-config.ini '.$backupTempPath.'/phyre-config.ini'. PHP_EOL;

        // Make exclude.lst file
        $backupManagerConfig = app()->backupManager->getConfigs([
            'microweber'
        ]);
        $excludeListContent = '';
        if (isset($backupManagerConfig['excludePaths'])) {
            foreach ($backupManagerConfig['excludePaths'] as $exclude) {
                $excludeListContent .= $exclude . PHP_EOL;
            }
        }
        $excludeListPath = $backupTempPath.'/exclude.lst';
        file_put_contents($excludeListPath, $excludeListContent);

        if ($this->backup_type == 'full') {
            // Export Phyre Panel Hosting Subscription
            $findHostingSubscription = HostingSubscription::all();
            if ($findHostingSubscription->count() > 0) {
                foreach ($findHostingSubscription as $hostingSubscription) {
                    $hostingSubscriptionsMainPath = $backupTempPath . '/hosting_subscriptions';
                    $hostingSubscriptionPath = $hostingSubscriptionsMainPath . '/' . $hostingSubscription->system_username;
                    $shellFileContent .= PHP_EOL;
                    $shellFileContent .= 'echo "Backup up hosting subscription: ' . $hostingSubscription->system_username . '" ' . PHP_EOL;
                    $shellFileContent .= 'mkdir -p ' . $hostingSubscriptionPath . PHP_EOL;

                    // cp -r (copy recursively, also copy hidden files)
                  //  $shellFileContent .= 'cp -r /home/' . $hostingSubscription->system_username . '/ ' . $hostingSubscriptionsMainPath . PHP_EOL;
                    $shellFileContent .= "rsync -av --exclude-from='$excludeListPath' /home/$hostingSubscription->system_username/ " . $hostingSubscriptionPath .'/'. PHP_EOL;

                    $shellFileContent .= 'mkdir -p ' . $hostingSubscriptionPath . '/databases' . PHP_EOL;

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
                            $shellFileContent .= 'mysqldump --defaults-extra-file=' . $mysqlAuthConf . ' "' . $databaseName . '" > ' . $databaseBackupPath . PHP_EOL;
                        }
                    }

                    $shellFileContent .= PHP_EOL;

                }
            }

        }

        // With find, we can search for all files,directories (including hidden) in the current directory and zip them
        $shellFileContent .= 'cd '.$backupTempPath .' && find . -exec zip -1 --symlinks -r '.$backupFilePath.' {} \;'. PHP_EOL;

        $shellFileContent .= 'rm -rf '.$backupTempPath.PHP_EOL;
        $shellFileContent .= 'echo "Backup complete"' . PHP_EOL;
        $shellFileContent .= 'touch ' . $backupPath. '/backup.done' . PHP_EOL;
        $shellFileContent .= 'phyre-php /usr/local/phyre/web/artisan phyre:run-backup-checks'. PHP_EOL;
        $shellFileContent .= 'rm -rf ' . $backupTempScript;

        file_put_contents($backupTempScript, $shellFileContent);

        // chmod read and delete by owner only
        chmod($backupTempScript, 0600);

        $processId = shell_exec('bash '.$backupTempScript.' >> ' . $backupLogFilePath . ' & echo $!');
        $processId = intval($processId);

        if ($processId > 0 && is_numeric($processId)) {

            $this->path = $backupPath;
            $this->root_path = $storagePath;
            $this->temp_path = $backupTempPath;
            $this->file_path = $backupFilePath;
            $this->file_name = $backupFilename;

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
