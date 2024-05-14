<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingSubscriptionFtpAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'path',
        'quota',
        'unlimited_quota',
    ];
}
