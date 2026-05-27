<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg admin-layout">
            @include('partials.admin-sidebar')

            <section class="stack-md">
                <div class="section-head section-head--admin">
                    <div>
                        <h1 class="section-title">Админ панель</h1>
                        <p class="section-text">Доступ только для администратора системы.</p>
                    </div>
                    @include('partials.admin-search')
                </div>

                <div class="profile-info">
                    <div class="info-box">
                        <div class="info-label">Пользователи</div>
                        <div class="info-value">{{ $usersCount }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Авторы</div>
                        <div class="info-value">{{ \App\Models\Author::query()->count() }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Все заказы</div>
                        <div class="info-value">{{ $ordersCount }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Оплаченные заказы</div>
                        <div class="info-value">{{ $paidOrdersCount }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Партнерские заявки</div>
                        <div class="info-value">{{ $partnerApplicationsCount }}</div>
                    </div>
                </div>
            </section>

            <div class="actions admin-home-actions">
                <a href="{{ route('home') }}" class="btn btn-secondary">На главную</a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-secondary cart-checkout-button">Выйти</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
