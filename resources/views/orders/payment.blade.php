<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Оплата заказа - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-home-url="{{ route('catalog') }}">
    @include('partials.site-header', ['showCatalogButton' => true, 'showAuthButtons' => false])

    <main class="site-main">
        <section class="container stack-lg">
            <section class="stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">Оплата заказа</h1>
                        <p class="section-text">Проверьте данные покупателя и состав заказа перед оплатой.</p>
                    </div>
                    <a href="{{ route('catalog') }}" class="btn btn-secondary">Вернуться в каталог</a>
                </div>

                <div class="profile-info">
                    <div class="info-box">
                        <div class="info-label">Покупатель</div>
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
                        <div class="info-label">Дата и время заказа</div>
                        <div class="info-value">{{ $order->order_date ? $order->order_date->format('d.m.Y H:i') : 'Не указаны' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Номер заказа</div>
                        <div class="info-value">№{{ $order->getKey() }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Статус</div>
                        <div class="info-value">{{ $order->status }}</div>
                    </div>
                </div>

                @if(session('search_error'))
                    <div class="site-search-notice">{{ session('search_error') }}</div>
                @endif
            </section>

            <section class="stack-md">
                <div>
                    <h2 class="subheading">Состав заказа</h2>
                    <p class="section-text">Данные из таблиц заказов и деталей заказа.</p>
                </div>

                <div class="cart-list">
                    @foreach($orderDetails as $detail)
                        <article class="cart-item">
                            <img
                                src="{{ $detail->book?->cover_image_url ?? 'https://via.placeholder.com/500x700/667eea/ffffff?text=' . urlencode($detail->book->book_name ?? 'Книга') }}"
                                class="cart-image"
                                alt="{{ $detail->book->book_name ?? 'Книга' }}"
                            >

                            <div class="stack-sm">
                                <div class="cart-title">{{ $detail->book->book_name ?? 'Книга недоступна' }}</div>
                                <div class="muted">{{ $detail->book->author->author_name ?? 'Автор не указан' }}</div>
                                <div class="muted">Количество: {{ $detail->quantity }}</div>
                                <div class="muted">Цена за экземпляр: {{ number_format((float) $detail->price_per_item, 0, '.', ' ') }} ₽</div>
                            </div>

                            <div class="item-total">
                                {{ number_format((float) ($detail->price_per_item * $detail->quantity), 0, '.', ' ') }} ₽
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="summary">
                    <div class="summary-total">
                        К оплате: {{ number_format((float) $order->total_amount, 0, '.', ' ') }} ₽
                    </div>
                    <form method="POST" action="{{ route('orders.pay', $order) }}">
                        @csrf
                        <button class="btn btn-secondary cart-checkout-button" type="submit">Оплатить</button>
                    </form>
                </div>
            </section>
        </section>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
</body>
</html>
