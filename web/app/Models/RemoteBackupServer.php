<?php

namespace App\Models;

use Doctrine\DBAL\DriverManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemoteBackupServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'hostname',
        'port',
        'username',
        'password',
        'path',
        'ssh_private_key',
        'ssh_private_key_password',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->healthCheck();
        });

        static::updated(function ($model) {
            $model->healthCheck();
        });
    }

    public function healthCheck()
    {
        if ($this->type == 'ftp') {

            try {

                $username = trim($this->username);
                $password = trim($this->password);
                $hostname = trim($this->hostname);
                $port = trim($this->port);
                $path = trim($this->path);

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'ftp://'.$hostname.':'.$port.'/'.$path.'/');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                curl_setopt($curl, CURLOPT_USERPWD, $username.':'.$password);
                $curlResponse = curl_exec($curl);
                curl_close($curl);

                if ($curlResponse) {
                    $this->status = 'online';
                    $this->save();
                    return;
                } else {
                    $this->status = 'offline';
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
