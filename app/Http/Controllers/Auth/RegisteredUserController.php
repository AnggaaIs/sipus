<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user() instanceof User) {
            return redirect()->route('login');
        }

        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $request->createUser();

        return redirect()
            ->route('login')
            ->with('status', 'Pendaftaran berhasil. Akun Anda sedang menunggu persetujuan petugas perpustakaan.');
    }
}
