<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell auth-page" data-home-url="{{ route('home') }}">
    <header class="site-header">
        <div class="site-header__inner container">
            <a href="{{ route('home') }}" class="site-logo">📚 Книжный Мир</a>
            <a href="{{ route('User_login') }}" class="btn btn-secondary">Log in</a>
        </div>
    </header>

    <main class="site-main">
        <div class="auth-card page-panel">
            <div>
                <h1 class="auth-title">Книжный Мир</h1>
                <p class="auth-subtitle">Создайте новый аккаунт</p>
            </div>

            <form method="POST" action="{{ route('register') }}" autocomplete="off">
                @csrf

                <div class="form-group {{ $errors->has('name') ? 'error' : '' }}">
                    <label for="name">Имя</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Введите ваше имя"
                    >
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Введите ваш email"
                    >
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('phone_number') ? 'error' : '' }}">
                    <label for="phone_number">Телефон (необязательно)</label>
                    <input
                        type="text"
                        id="phone_number"
                        name="phone_number"
                        value="{{ old('phone_number') }}"
                        placeholder="Введите номер телефона"
                    >
                    @error('phone_number')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('password') ? 'error' : '' }}">
                    <label for="password">Пароль</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Создайте пароль"
                    >
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('password_confirmation') ? 'error' : '' }}">
                    <label for="password_confirmation">Подтвердите пароль</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Повторите пароль"
                    >
                    @error('password_confirmation')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
            </form>

            <div class="auth-footer">
                Уже есть аккаунт? <a href="{{ route('User_login') }}">Войти</a>
            </div>
        </div>
    </main>
</body>
</html>
