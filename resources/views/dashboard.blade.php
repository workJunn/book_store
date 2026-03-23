<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-home-url="{{ route('home') }}">
    @include('partials.site-header', ['showAuthButtons' => false])

    <main class="site-main">
        <section class="container">
            <section class="panel stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">{{ $user->name }}</h1>
                        <p class="section-text">Личный кабинет пользователя.</p>
                    </div>
                    <div class="status-badge {{ $user->email_verified_at ? 'status-badge--ok' : 'status-badge--warn' }}">
                        {{ $user->email_verified_at ? 'Email подтверждён' : 'Email не подтверждён' }}
                    </div>
                </div>

                <div class="profile-info">
                    <div class="info-box">
                        <div class="info-label">Имя</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="info-box">
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
                        <div class="info-value">{{ $user->registration_date ? $user->registration_date->format('d.m.Y H:i') : 'Не указана' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Последнее обновление</div>
                        <div class="info-value">{{ $user->updated_at ? $user->updated_at->format('d.m.Y H:i') : 'Не обновлялся' }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-primary">Выйти</button>
                </form>
            </section>
        </section>
    </main>
</body>
</html>
