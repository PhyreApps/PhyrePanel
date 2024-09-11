<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitSshKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'hosting_subscription_id',
        'name',
        'private_key',
        'public_key',
    ];

    public function hostingSubscription()
    {
        return $this->belongsTo(HostingSubscription::class);
    }
}
