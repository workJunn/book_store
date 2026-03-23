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
                    <input type="password" id="password" name="password" required>
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
