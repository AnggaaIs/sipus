<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return redirect()->route('login')->with('status', 'Reset password belum tersedia.');
    }

    public function store(Request $request)
    {
        return redirect()->route('login')->with('status', 'Reset password belum tersedia.');
    }
}
