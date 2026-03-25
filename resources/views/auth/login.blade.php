<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell auth-page" data-home-url="{{ route('home') }}">
    @include('partials.site-header', ['showNav' => false, 'showSearch' => false, 'showFavorites' => false, 'showCart' => false, 'showAuthButtons' => false, 'showProfile' => false])

    <main class="site-main">
        <section class="auth-card stack-md">
            <div>
                <h1 class="section-title">Вход</h1>
                <p class="section-text">Войдите в свой аккаунт.</p>
            </div>

            @if (session('status'))
                <div class="success-message">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" autocomplete="off" novalidate>
                @csrf

                <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('password') ? 'error' : '' }}">
                    <label for="password">Пароль</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" required data-password-input>
                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle
                            aria-label="Удерживайте, чтобы показать пароль"
                            aria-controls="password"
                            aria-pressed="false"
                        >
                            <svg class="password-toggle__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <label class="checkbox-row">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Запомнить меня</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-link">Забыли пароль?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Войти</button>
            </form>

            <p class="auth-footer">Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a></p>
        </section>
    </main>
</body>
</html>
