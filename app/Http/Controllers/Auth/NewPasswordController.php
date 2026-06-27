<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(string $token, Request $request): View
    {
        $email = $this->resolveEmailFromRequest($request);

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function store(NewPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PasswordReset) {
            return back()->withErrors([
                'email' => __($status),
            ])->onlyInput('email');
        }

        return redirect()
            ->route('login')
            ->with('status', 'Kata sandi berhasil diganti. Silakan masuk kembali.');
    }

    private function resolveEmailFromRequest(Request $request): string
    {
        $identity = (string) $request->query('identity', '');

        if ($identity !== '') {
            try {
                return Crypt::decryptString($identity);
            } catch (DecryptException) {
                return '';
            }
        }

        return (string) $request->query('email', '');
    }
}
