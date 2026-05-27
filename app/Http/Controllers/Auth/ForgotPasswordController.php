<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function create() 
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request) 
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Введите email',
            'email.email' => 'Некорректный формат email',
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Ссылка для сброса пароля отправлена.');
    }
}
 
