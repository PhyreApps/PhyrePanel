<?php

namespace Modules\Minecraft\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Minecraft\Database\factories\MinecraftServerFactory;

class MinecraftServer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
    ];


    protected static function booted(): void
    {
        static::addGlobalScope('customer', function (Builder $query) {
            if (auth()->check() && auth()->guard()->name == 'web_customer') {
                $query->where('customer_id', auth()->user()->id);
            }
        });
    }


    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $minectaftId = 1;
            $findLastMinecraftServer = MinecraftServer::orderBy('id', 'desc')->first();
            if ($findLastMinecraftServer) {
                $minectaftId = $findLastMinecraftServer->id + 1;
                $model->name = 'Server #'.$minectaftId;
            } else {
                $model->name = 'Server #1';
            }

            $minecraftServersPath = '/home/minecraft/servers';
            $minecraftServerPath = $minecraftServersPath . '/'.$minectaftId;
            shell_exec('mkdir -p '.$minecraftServerPath);

            $minecraftServerDocker = view('minecraft::actions.docker-compose', [
                'id' => $minectaftId
            ])->render();

            file_put_contents($minecraftServerPath.'/docker-compose.yml', $minecraftServerDocker);
            $docker = shell_exec('cd '.$minecraftServerPath.' && docker-compose up -d');
            
        });

        static::created(function ($model) {


        });

        static::deleting(function ($model) {




        });

    }

}
