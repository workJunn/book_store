<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column {{ $user->isAdmin() ? 'admin-page' : '' }}" data-home-url="{{ route('home') }}">
    @if(! $user->isAdmin())
        @include('partials.site-header', ['showAuthButtons' => false])
    @endif

    <main class="site-main">
        <section class="container">
            <section class="stack-md">
                <div class="section-head {{ $user->isAdmin() ? 'section-head--admin' : '' }}">
                    <div>
                        <h1 class="section-title">{{ $user->name }}</h1>
                    </div>
                    @if($user->isAdmin())
                        @include('partials.admin-search')
                        @include('partials.admin-nav')
                    @else
                        <div class="status-badge {{ $user->email_verified_at ? 'status-badge--ok' : 'status-badge--warn' }}">
                            {{ $user->email_verified_at ? 'Email подтверждён' : 'Email не подтверждён' }}
                        </div>
                    @endif
                </div>

                @if(session('status'))
                    <div class="success-box">{{ session('status') }}</div>
                @endif

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

                <section class="stack-md">
                    <div>
                        <h2 class="subheading">Мои заказы</h2>
                        <p class="section-text">Здесь отображаются оформленные и оплаченные заказы.</p>
                    </div>

                    @if($user->orders->isNotEmpty())
                        <div class="stack-md">
                            @foreach($user->orders as $order)
                                <article class="info-box stack-sm">
                                    <div class="order-line">
                                        <a href="{{ route('orders.show', $order) }}" class="text-link">Заказ №{{ $order->getKey() }}</a>
                                        <span>{{ $order->order_date ? $order->order_date->format('d.m.Y H:i') : 'Дата не указана' }}</span>
                                        <span>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} ₽</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            У вас пока нет заказов.
                        </div>
                    @endif
                </section>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-secondary cart-checkout-button">Выйти</button>
                </form>
            </section>
        </section>
    </main>
</body>
</html>
