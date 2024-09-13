<?php

namespace App\Models\Scopes;

use Filament\Facades\Filament;
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

        $guard = Filament::auth();

        if ($guard->check() && $guard->name == 'web_customer') {
            $builder->whereHas('hostingSubscription', function ($query) use($guard) {
                $query->where('customer_id', $guard->user()->id);
            });
        }
    }

}
