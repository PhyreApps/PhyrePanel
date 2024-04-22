<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_key',
        'api_secret',
        'whitelisted_ips',
        'is_active',
        'enable_whitelisted_ips',
    ];

    protected $casts = [
        'whitelisted_ips' => 'array',
        'is_active' => 'boolean',
        'enable_whitelisted_ips' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->api_key = bin2hex(random_bytes(32));
            $model->api_secret = bin2hex(random_bytes(32));
        });
    }
}
