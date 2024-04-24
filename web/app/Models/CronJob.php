<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    protected $fillable = [
        'schedule',
        'command',
        'user',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

//            $addCron = ShellApi::callBin('cron-job-add', [
//                $model->user,
//                $model->schedule,
//                $model->command,
//            ]);
//            if (empty($addCron)) {
//                return false;
//            }

        });

        static::deleting(function ($model) {

//            $deleteCron = ShellApi::callBin('cron-job-delete', [
//                $model->user,
//                $model->schedule,
//                $model->command,
//            ]);
//            if (empty($deleteCron)) {
//                return false;
//            }

        });
    }

}
