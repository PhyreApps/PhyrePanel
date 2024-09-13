<?php

namespace App\Models\Scopes;

use App\Models\Domain;
use App\Models\HostingSubscription;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CustomerDomainScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $guard = Filament::auth();
        if ($guard->check() && $guard->name == 'web_customer') {

            $findHostingSubscriptionIds = HostingSubscription::where('customer_id', $guard->user()->id)->pluck('id');
            $findDomainIds = Domain::whereIn('hosting_subscription_id', $findHostingSubscriptionIds)->pluck('id');

            $builder->whereIn('domain_id', $findDomainIds);
        }
    }

}
