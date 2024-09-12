<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CustomerHostingSubscriptionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && auth()->guard()->name == 'web_customer') {
            $builder->whereHas('hostingSubscription', function ($query) {
                $query->where('customer_id', auth()->user()->id);
            });
        }
    }

}
