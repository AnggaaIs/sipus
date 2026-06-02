<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user() instanceof User) {
            return redirect()->to($this->redirectPathFor($request->user()));
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        return redirect()->to($this->redirectPathFor($user));
    }

    protected function redirectPathFor(User $user): string
    {
        if ($user->isAdmin()) {
            return route('filament.admin.pages.dashboard');
        }

        return route('filament.user.pages.dashboard');
    }
}
