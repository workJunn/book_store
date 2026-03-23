<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Корзина - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-page="cart" data-home-url="{{ route('catalog') }}">
    @include('partials.site-header', ['showCatalogButton' => true, 'showAuthButtons' => false, 'showProfile' => false])

    <main class="site-main">
        <section class="container">
            <section class="stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">Корзина</h1>
                        <p class="section-text">Проверьте состав заказа перед оформлением.</p>
                    </div>

                    @if(count($cart) > 0)
                        <div class="cart-toolbar" id="top-buttons">
                            <button data-clear-cart class="btn btn-danger" type="button">Очистить корзину</button>
                        </div>
                    @endif
                </div>

                @if(session('success'))
                    <div class="success-box">{{ session('success') }}</div>
                @endif

                <div id="cart-content">
                    @if(count($cart))
                        <div id="cart-items" class="cart-list">
                            @foreach($cart as $item)
                                <article class="cart-item" id="item-{{ $item['id'] }}">
                                    <img src="{{ $item['image'] }}" class="cart-image" alt="{{ $item['title'] }}">

                                    <div class="stack-sm">
                                        <div class="cart-title">{{ $item['title'] }}</div>
                                        <div class="muted">{{ $item['author'] }}</div>
                                        <div class="price">{{ number_format((float) $item['price'], 0, '.', ' ') }} ₽</div>

                                        <div class="qty-controls">
                                            <button class="qty-btn qty-btn-minus" data-cart-action="decrease" data-item-id="{{ $item['id'] }}" type="button" aria-label="Уменьшить количество {{ $item['title'] }}">-</button>
                                            <div class="qty-value" id="qty-{{ $item['id'] }}">{{ $item['quantity'] }}</div>
                                            <button class="qty-btn qty-btn-plus" data-cart-action="increase" data-item-id="{{ $item['id'] }}" type="button" aria-label="Увеличить количество {{ $item['title'] }}">+</button>
                                        </div>
                                    </div>

                                    <div class="item-total" id="item-total-{{ $item['id'] }}">
                                        {{ number_format((float) ($item['price'] * $item['quantity']), 0, '.', ' ') }} ₽
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="summary">
                            <div class="summary-total">
                                Итого: <span id="cart-total">{{ number_format((float) $total, 0, '.', ' ') }}</span> ₽
                            </div>
                            <form method="POST" action="{{ route('cart.checkout') }}">
                                @csrf
                                <button class="btn btn-secondary cart-checkout-button" type="submit">Оформить заказ</button>
                            </form>
                        </div>
                    @else
                        <div class="empty-cart">
                            <h2>Корзина пуста</h2>
                            <p>Добавьте книги из каталога.</p>
                            <a href="{{ route('catalog') }}" class="btn btn-primary">Перейти в каталог</a>
                        </div>
                    @endif
                </div>
            </section>
        </section>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
</body>
</html>
