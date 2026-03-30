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
        <section class="container stack-lg">
            <section class="stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">Админ панель</h1>
                        <p class="section-text">Доступ только для администратора системы.</p>
                    </div>
                    <div class="actions">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Назад</a>
                        @include('partials.admin-nav')
                    </div>
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

            <section class="stack-md">
                <div>
                    <h2 class="subheading">Последние заказы</h2>
                    <p class="section-text">Быстрый обзор последних заказов в системе.</p>
                </div>

                @if($latestOrders->isNotEmpty())
                    <div class="stack-md">
                        @foreach($latestOrders as $order)
                            <article class="info-box stack-sm">
                                <div class="order-line">
                                    <span>Заказ №{{ $order->getKey() }}</span>
                                    <span>{{ $order->user->name ?? 'Пользователь' }}</span>
                                    <span>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} ₽</span>
                                </div>
                                <div class="muted">
                                    {{ $order->order_date ? $order->order_date->format('d.m.Y H:i') : 'Дата не указана' }} · {{ $order->status }}
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        Заказов пока нет.
                    </div>
                @endif
            </section>
        </section>
    </main>
</body>
</html>
