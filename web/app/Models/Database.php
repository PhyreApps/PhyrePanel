<?php

namespace App\Models;

use App\Models\Scopes\CustomerHostingSubscriptionScope;
use App\PhyreConfig;
use App\Services\RemoteDatabaseService;
use App\UniversalDatabaseExecutor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class Database extends Model
{
    use HasFactory;

    protected $fillable = [
        'hosting_subscription_id',
        'remote_database_server_id',
        'is_remote_database_server',
        'database_name',
        'database_name_prefix',
        'description',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CustomerHostingSubscriptionScope());
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $findHostingSubscription = HostingSubscription::where('id', $model->hosting_subscription_id)->first();
            if (!$findHostingSubscription) {
                return false;
            }

            $model->database_name_prefix = $findHostingSubscription->system_username . '_';

            $databaseName = Str::slug($model->database_name, '_');
            $databaseName = $model->database_name_prefix . $databaseName;
            $databaseName = strtolower($databaseName);

            if ($model->is_remote_database_server == 1) {

                $remoteDatabaseService = new RemoteDatabaseService($model->remote_database_server_id);
                $createDatabase = $remoteDatabaseService->createDatabase($databaseName);
                if (isset($createDatabase['error'])) {
                    throw new \Exception($createDatabase['message']);
                }

            } else {
                $universalDatabaseExecutor = new UniversalDatabaseExecutor(
                    PhyreConfig::get('MYSQL_HOST', '127.0.0.1'),
                    PhyreConfig::get('MYSQL_PORT', 3306),
                    PhyreConfig::get('MYSQL_ROOT_USERNAME'),
                    PhyreConfig::get('MYSQL_ROOT_PASSWORD'),
                );

                $universalDatabaseExecutor->fixPasswordPolicy();

                // Check main database user exists
                $mainDatabaseUser = $universalDatabaseExecutor->getUserByUsername($findHostingSubscription->system_username);
                if (!$mainDatabaseUser) {
                    $createMainDatabaseUser = $universalDatabaseExecutor->createUser($findHostingSubscription->system_username, $findHostingSubscription->system_password);
                    if (!isset($createMainDatabaseUser['success'])) {
                        throw new \Exception($createMainDatabaseUser['message']);
                    }
                }

                $createDatabase = $universalDatabaseExecutor->createDatabase($databaseName);
                if (isset($createDatabase['error'])) {
                    throw new \Exception($createDatabase['message']);
                }

                $universalDatabaseExecutor->userGrantPrivilegesToDatabase($findHostingSubscription->system_username, [$databaseName]);
            }

            return $model;

        });

        static::deleting(function($model) {

            if ($model->is_remote_database_server == 1) {

                $remoteDatabaseService = new RemoteDatabaseService($model->remote_database_server_id);
                $deleteDatabase = $remoteDatabaseService->deleteDatabase($model->database_name_prefix . $model->database_name);
                if (!$deleteDatabase) {
                    return false;
                }

            }
        });
    }

    public function hostingSubscription()
    {
        return $this->belongsTo(HostingSubscription::class);
    }

    public function databaseUsers()
    {
        return $this->hasMany(DatabaseUser::class);
    }
}
