<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-home-url="{{ route('home') }}">
    <header class="site-header">
        <div class="site-header__inner container">
            <a href="{{ route('home') }}" class="site-logo">📚 Книжный Мир</a>

            <div class="site-actions">
                <a href="{{ route('cart.index') }}" class="cart-link" title="Корзина">
                    🛒
                    <span class="cart-count" data-cart-count>
                        {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                    </span>
                </a>

                <a href="{{ route('dashboard') }}" class="profile-link" title="Профиль">
                    👤
                </a>
            </div>
        </div>
    </header>

    <main class="site-main">
        <div class="container profile-wrap">
            <div class="profile-card page-panel">
                <div class="profile-top">
                    <div class="profile-avatar">👤</div>
                    <h1 class="profile-title">{{ $user->name }}</h1>
                    <p class="profile-subtitle">Личная информация пользователя</p>

                    <div class="profile-status {{ $user->email_verified_at ? 'verified' : 'not-verified' }}">
                        {{ $user->email_verified_at ? 'Email подтверждён' : 'Email не подтверждён' }}
                    </div>
                </div>

                <div class="profile-info">
                    <div class="info-box">
                        <div class="info-label">Имя</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>

                    <div class="info-box full-width">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">Телефон</div>
                        <div class="info-value">{{ $user->phone_number ?: 'Не указан' }}</div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">Баланс</div>
                        <div class="info-value">{{ number_format((float) $user->balance, 2, '.', ' ') }} ₽</div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">Дата регистрации</div>
                        <div class="info-value">
                            {{ $user->registration_date ? $user->registration_date->format('d.m.Y H:i') : 'Не указана' }}
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">Дата создания записи</div>
                        <div class="info-value">
                            {{ $user->created_at ? $user->created_at->format('d.m.Y H:i') : 'Не указана' }}
                        </div>
                    </div>

                    <div class="info-box full-width">
                        <div class="info-label">Последнее обновление профиля</div>
                        <div class="info-value">
                            {{ $user->updated_at ? $user->updated_at->format('d.m.Y H:i') : 'Не обновлялся' }}
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Выход из аккаунта
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
