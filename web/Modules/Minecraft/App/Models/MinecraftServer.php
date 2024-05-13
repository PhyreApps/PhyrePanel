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
    protected $fillable = [];


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


        });

        static::created(function ($model) {


        });

        static::deleting(function ($model) {




        });

    }

}
