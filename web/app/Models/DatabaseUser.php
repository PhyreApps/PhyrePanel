<?php

namespace App\Models;

use App\UniversalDatabaseExecutor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'database_id',
        'username',
        'username_prefix',
        'password',
    ];


    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $findDatabase = Database::where('id', $model->database_id)->first();
            if (!$findDatabase) {
                return false;
            }
            $findHostingSubscription = HostingSubscription::where('id', $findDatabase->hosting_subscription_id)->first();
            if (!$findHostingSubscription) {
                return false;
            }

            $model->username_prefix = $findHostingSubscription->system_username . '_';
            $databaseUsername = $model->username_prefix . $model->username;

            if ($findDatabase->is_remote_database_server) {
                $findRemoteDatabaseServer = RemoteDatabaseServer::where('id', $findDatabase->remote_database_server_id)->first();
                if (!$findRemoteDatabaseServer) {
                    return false;
                }

                $databaseManager = new UniversalDatabaseExecutor(
                    $findRemoteDatabaseServer->host,
                    $findRemoteDatabaseServer->port,
                    $findRemoteDatabaseServer->username,
                    $findRemoteDatabaseServer->password,
                    $findDatabase->database_name_prefix . $findDatabase->database_name
                );

                $createDatabaseUser = $databaseManager->createUser($databaseUsername, $model->password);
                if (isset($createDatabaseUser['error'])) {
                    throw new \Exception($createDatabaseUser['message']);
                }
            } else {
                $universalDatabaseExecutor = new UniversalDatabaseExecutor(
                    env('MYSQL_HOST'),
                    env('MYSQL_PORT'),
                    env('MYSQl_ROOT_USERNAME'),
                    env('MYSQL_ROOT_PASSWORD'),
                    $findDatabase->database_name_prefix . $findDatabase->database_name
                );
                $createDatabase = $universalDatabaseExecutor->createUser($databaseUsername, $model->password);
                if (isset($createDatabase['error'])) {
                    throw new \Exception($createDatabase['message']);
                }
            }

        });
    }

    public function database()
    {
        return $this->belongsTo(Database::class);
    }

}
