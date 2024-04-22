<?php

namespace App\Models;

use App\ShellApi;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class CronJob extends Model
{
    use Sushi;

    protected $fillable = [
        'schedule',
        'command',
        'user',
    ];

    protected $schema = [
        'schedule' => 'string',
        'command' => 'string',
        'user' => 'string',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $addCron = ShellApi::callBin('cron-job-add', [
                $model->user,
                $model->schedule,
                $model->command,
            ]);
            if (empty($addCron)) {
                return false;
            }

        });

        static::deleting(function ($model) {

            $deleteCron = ShellApi::callBin('cron-job-delete', [
                $model->user,
                $model->schedule,
                $model->command,
            ]);
            if (empty($deleteCron)) {
                return false;
            }

        });
    }

    protected function sushiShouldCache()
    {
        return true;
    }

    public function getRows()
    {
        $user = ShellApi::exec('whoami');

        $cronList = ShellApi::callBin('cron-jobs-list', [
            $user,
        ]);

        $rows = [];
        if (! empty($cronList)) {
            $cronList = json_decode($cronList, true);
            if (! empty($cronList)) {
                foreach ($cronList as $cron) {
                    if (isset($cron['schedule'])) {
                        $rows[] = [
                            'schedule' => $cron['schedule'],
                            'command' => $cron['command'],
                            'user' => $user,
                            'time' => time(),
                        ];
                    }
                }
            }
        }

        return $rows;
    }
}
