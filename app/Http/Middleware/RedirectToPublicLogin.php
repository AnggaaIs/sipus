<?php

namespace App\Http\Middleware;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;

class RedirectToPublicLogin extends Authenticate
{
    protected function authenticate($request, array $guards): void
    {
        parent::authenticate($request, $guards);

        /** @var User $user */
        $user = auth()->user();
        $panel = Filament::getCurrentPanel();

        if ($user && ! $user->canAccessPanel($panel)) {
            abort(404);
        }
    }

    protected function redirectTo($request): ?string
    {
        return route('login');
    }
}
