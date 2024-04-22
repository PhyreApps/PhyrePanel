<?php

namespace App\Models;

use Doctrine\DBAL\DriverManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RemoteDatabaseServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'host',
        'port',
        'database_type',
        'username',
        'password',
        'status',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->healthCheck();
        });
    }

    public function healthCheck()
    {
        if ($this->database_type == 'mysql') {

            try {
                $connectionParams = [
                    'user' => $this->username,
                    'password' => $this->password,
                    'host' => $this->host,
                    'port' => $this->port,
                    'driver' => 'pdo_mysql',
                ];

                $connection = DriverManager::getConnection($connectionParams);
                $connection->connect();

                if ($connection->isConnected()) {
                    $this->status = 'online';
                    $this->save();
                    return;
                }

            } catch (\Exception $e) {
                $this->status = 'offline';
                $this->save();
                return;
            }
        }

    }

}
