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
        // Валидация
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Введите email',
            'email.email' => 'Некорректный формат email',
            'email.exists' => 'Пользователь с таким email не найден',
        ]);

        // Отправка ссылки
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Ссылка для сброса пароля отправлена!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Не удалось отправить ссылку. Проверьте email.']);
    }
}
 