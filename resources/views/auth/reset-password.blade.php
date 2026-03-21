<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый пароль - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell auth-page" data-home-url="{{ route('home') }}">
    <header class="site-header">
        <div class="site-header__inner container">
            <a href="{{ route('home') }}" class="site-logo">📚 Книжный Мир</a>
        </div>
    </header>

    <main class="site-main">
        <div class="auth-card page-panel">
            <div>
                <h1 class="auth-title">Новый пароль</h1>
                <p class="auth-subtitle">Введите email и задайте новый пароль для аккаунта</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" autocomplete="off" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $email) }}"
                        placeholder="Введите ваш email"
                        required
                        autofocus
                    >
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('password') ? 'error' : '' }}">
                    <label for="password">Новый пароль</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Введите новый пароль"
                        required
                    >
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Подтверждение пароля</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Повторите новый пароль"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block">Сохранить пароль</button>
            </form>
        </div>
    </main>
</body>
</html>
