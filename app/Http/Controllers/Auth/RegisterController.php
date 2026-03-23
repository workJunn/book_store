<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'admin_code' => ['nullable', 'string', 'max:100'],
        ], [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'name.max' => 'Имя не должно превышать 50 символов.',

            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Введите корректный email.',
            'email.max' => 'Email не должен превышать 50 символов.',
            'email.unique' => 'Этот email уже зарегистрирован.',

            'phone_number.max' => 'Телефон не должен превышать 20 символов.',

            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',

            'admin_code.max' => 'Код администратора не должен превышать 100 символов.',
        ]);

        $userRole = Role::firstOrCreate(['role_name' => 'user']);
        $adminRole = Role::firstOrCreate(['role_name' => 'admin']);
        $providedAdminCode = trim((string) ($validated['admin_code'] ?? ''));
        $adminRegistrationCode = (string) env('ADMIN_REGISTRATION_CODE', 'admin-secret');
        $isAdminRegistration = $providedAdminCode !== '' && hash_equals($adminRegistrationCode, $providedAdminCode);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'balance' => 0.00,
            'id_role' => $isAdminRegistration ? $adminRole->getKey() : $userRole->getKey(),
        ]);

        Auth::login($user);

        return redirect()->intended($user->isAdmin() ? route('admin.index') : route('dashboard'));
    }
}
