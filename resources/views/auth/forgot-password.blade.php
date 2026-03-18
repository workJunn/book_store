<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля - Книжный Мир</title>
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
                <h1 class="auth-title">Сброс пароля</h1>
                <p class="auth-subtitle">Введите email, и мы отправим инструкцию по восстановлению доступа</p>
            </div>

            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            <form method="post" action="{{ route('password.email') }}" autocomplete="off" novalidate>
                @csrf

                <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Введите ваш email"
                        autofocus
                    >
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">Отправить ссылку</button>
            </form>
        </div>
    </main>
</body>
</html>
