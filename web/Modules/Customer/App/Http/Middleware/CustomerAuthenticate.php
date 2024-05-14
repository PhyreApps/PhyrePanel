<?php

namespace Modules\Customer\App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Model;

class CustomerAuthenticate extends Authenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

//        /** @var Model $user */
//        $user = $guard->user();
//
//        $panel = Filament::getCurrentPanel();

    }

    protected function redirectTo($request): ?string
    {
        return Filament::getLoginUrl();
    }
}
