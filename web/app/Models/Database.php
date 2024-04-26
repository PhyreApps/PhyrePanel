<?php

namespace App\Models;

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
                    env('MYSQL_HOST'),
                    env('MYSQL_PORT'),
                    env('MYSQl_ROOT_USERNAME'),
                    env('MYSQL_ROOT_PASSWORD'),
                );
                $createDatabase = $universalDatabaseExecutor->createDatabase($databaseName);
                if (isset($createDatabase['error'])) {
                    throw new \Exception($createDatabase['message']);
                }
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
