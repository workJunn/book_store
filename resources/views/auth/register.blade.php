<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell auth-page" data-home-url="{{ route('home') }}">
    @include('partials.site-header', ['showNav' => false, 'showSearch' => false, 'showFavorites' => false, 'showCart' => false, 'showAuthButtons' => false, 'showProfile' => false])

    <main class="site-main">
        <section class="auth-card stack-md">
            <div class="auth-card__head">
                <h1 class="section-title">Регистрация</h1>
            </div>

            <form method="POST" action="{{ route('register') }}" autocomplete="off" class="stack-md">
                @csrf

                <div class="form-group {{ $errors->has('name') ? 'error' : '' }}">
                    <label for="name">Имя</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Иван Иванов">
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="ivan@example.com">
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('phone_number') ? 'error' : '' }}">
                    <label for="phone_number">Телефон</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="+7 999 000-00-00">
                    @error('phone_number')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('password') ? 'error' : '' }}">
                    <label for="password">Пароль</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" placeholder="Не менее 8 символов" data-password-input>
                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle
                            aria-label="Показать или скрыть пароль"
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
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('password_confirmation') ? 'error' : '' }}">
                    <label for="password_confirmation">Подтвердите пароль</label>
                    <div class="password-field">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Повторите пароль" data-password-input>
                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle
                            aria-label="Показать или скрыть пароль"
                            aria-controls="password_confirmation"
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
                    @error('password_confirmation')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
            </form>

            <p class="auth-footer">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
        </section>
    </main>
</body>
</html>
