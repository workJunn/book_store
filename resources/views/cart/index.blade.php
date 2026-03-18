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
    <header class="site-header">
        <div class="site-header__inner container">
            <a href="{{ route('home') }}" class="site-logo">📚 Книжный Мир</a>
            <div class="site-actions">
                <a href="{{ route('favorites') }}" class="favorites-link" title="Избранное">
                    ❤
                    <span class="cart-count" data-favorites-count>0</span>
                </a>
                <a href="{{ route('catalog') }}" class="btn btn-secondary">Назад в каталог</a>
            </div>
        </div>
    </header>

    <main class="site-main">
        <div class="container">
            <div class="cart-page page-panel">
                <h1 class="page-heading page-heading--dark">Корзина</h1>

                @if(count($cart) > 0)
                    <div class="cart-toolbar" id="top-buttons">
                        <button data-clear-cart class="btn btn-danger" type="button">
                            Очистить корзину
                        </button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="success-box">
                        {{ session('success') }}
                    </div>
                @endif

                <div id="cart-content">
                    @if(count($cart))
                        <div id="cart-items" class="cart-list">
                            @foreach($cart as $item)
                                <div class="cart-item" id="item-{{ $item['id'] }}">
                                    <img src="{{ $item['image'] }}" class="cart-image" alt="{{ $item['title'] }}">

                                    <div>
                                        <div class="cart-title">{{ $item['title'] }}</div>
                                        <div class="cart-author">{{ $item['author'] }}</div>
                                        <div class="cart-price">{{ $item['price'] }} ₽</div>

                                        <div class="qty-controls">
                                            <button
                                                class="qty-btn qty-btn-minus"
                                                data-cart-action="decrease"
                                                data-item-id="{{ $item['id'] }}"
                                                type="button"
                                            >
                                                -
                                            </button>

                                            <div class="qty-value" id="qty-{{ $item['id'] }}">
                                                {{ $item['quantity'] }}
                                            </div>

                                            <button
                                                class="qty-btn qty-btn-plus"
                                                data-cart-action="increase"
                                                data-item-id="{{ $item['id'] }}"
                                                type="button"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="item-total" id="item-total-{{ $item['id'] }}">
                                            {{ $item['price'] * $item['quantity'] }} ₽
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="summary">
                            <div class="summary-total">
                                Итого:
                                <span id="cart-total">{{ $total }}</span>
                                ₽
                            </div>

                            <button class="btn btn-primary" data-open-checkout type="button">
                                Оформить заказ
                            </button>
                        </div>
                    @else
                        <div class="empty-cart">
                            <h2>Корзина пуста</h2>
                            <p>Добавьте книги из каталога</p>
                            <a href="{{ route('catalog') }}" class="btn btn-primary">Перейти в каталог</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <div class="checkout-modal" id="checkout-modal">
        <div class="checkout-dialog">
            <h2 class="checkout-title">Подтвердить заказ</h2>
            <p class="checkout-text">
                Это интерфейсное подтверждение без подключения платежной системы.
                После подтверждения корзина будет очищена, а заказ будет считаться оформленным.
            </p>

            <div class="checkout-actions">
                <button class="btn btn-secondary" data-close-checkout type="button">
                    Отмена
                </button>
                <button class="btn btn-primary" data-confirm-checkout type="button">
                    Подтвердить
                </button>
            </div>
        </div>
    </div>
</body>
</html>
