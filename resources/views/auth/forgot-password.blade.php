<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell auth-page" data-home-url="{{ route('home') }}">
    @include('partials.site-header', ['showNav' => false, 'showSearch' => false, 'showFavorites' => false, 'showCart' => false, 'showAuthButtons' => false, 'showProfile' => false])

    <main class="site-main">
        <section class="auth-card stack-md">
            <div class="auth-card__head">
                <h1 class="section-title">Сброс пароля</h1>
            </div>

            @if (session('status'))
                <div class="success-message">{{ session('status') }}</div>
            @endif

            <form method="post" action="{{ route('password.email') }}" autocomplete="off" novalidate class="stack-md">
                @csrf

                <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">Отправить ссылку</button>
            </form>
        </section>
    </main>
</body>
</html>
