<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg">
            @include('partials.admin-page-head', ['title' => 'Заказы'])

            @if(session('status'))
                <div class="success-box">{{ session('status') }}</div>
            @endif

            <section class="stack-md">
                @foreach($orders as $order)
                    <article class="info-box stack-sm">
                        <div class="order-line">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-link">Заказ №{{ $order->getKey() }}</a>
                            <span>{{ $order->user->name ?? 'Пользователь' }}</span>
                            <span>{{ $order->order_date ? $order->order_date->format('d.m.Y H:i') : 'Дата не указана' }}</span>
                        </div>
                    </article>
                @endforeach
            </section>
        </section>
    </main>
</body>
</html>
