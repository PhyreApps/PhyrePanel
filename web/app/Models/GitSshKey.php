<?php

namespace App\Models;

use App\Models\Scopes\CustomerScope;
use App\Models\Scopes\CustomerHostingSubscriptionScope;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new CustomerHostingSubscriptionScope());
    }

    public function hostingSubscription()
    {
        return $this->belongsTo(HostingSubscription::class);
    }
}
