<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Корзина - Книжный Мир</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
        }

        .cart-page {
            max-width: 1100px;
            margin: 2rem auto;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .page-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .top-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 110px 1fr auto;
            gap: 1.5rem;
            align-items: center;
            padding: 1.2rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .cart-image {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: 10px;
        }

        .cart-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.4rem;
        }

        .cart-author {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .cart-price {
            color: #667eea;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.7rem;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-top: 0.5rem;
        }

        .qty-value {
            min-width: 35px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
        }

        .qty-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease;
        }

        .qty-btn:hover {
            transform: scale(1.1);
        }

        .qty-btn-plus {
            background: #667eea;
            color: white;
        }

        .qty-btn-minus {
            background: #ef4444;
            color: white;
        }

        .btn {
            padding: 0.75rem 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .item-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            text-align: right;
        }

        .summary {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            padding-top: 1.5rem;
            border-top: 2px solid #e5e7eb;
        }

        .summary-total {
            font-size: 1.7rem;
            font-weight: 700;
            color: #667eea;
        }

        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            color: #555;
        }

        .success-box {
            margin-bottom: 20px;
            background: #dcfce7;
            color: #166534;
            padding: 15px 20px;
            border-radius: 12px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .cart-item {
                grid-template-columns: 1fr;
            }

            .cart-image {
                width: 140px;
                height: 190px;
            }

            .item-total {
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <header>
        <a href="{{ route('home') }}" class="logo">📚 Книжный Мир</a>
        <a href="{{ route('home') }}" class="btn btn-secondary">Назад в каталог</a>
    </header>

    <main>
        <div class="cart-page">
            <h1 class="page-title">🛒 Корзина</h1>

            @if(count($cart) > 0)
                <div class="top-buttons" id="top-buttons">
                    <button onclick="clearCart()" class="btn btn-danger" type="button">
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
                    <div id="cart-items">
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
                                            onclick="updateCart({{ $item['id'] }}, 'decrease')"
                                            type="button"
                                        >
                                            -
                                        </button>

                                        <div class="qty-value" id="qty-{{ $item['id'] }}">
                                            {{ $item['quantity'] }}
                                        </div>

                                        <button
                                            class="qty-btn qty-btn-plus"
                                            onclick="updateCart({{ $item['id'] }}, 'increase')"
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

                        <a href="#" class="btn btn-primary">
                            Оформить заказ
                        </a>
                    </div>
                @else
                    <div class="empty-cart">
                        <h2>Корзина пуста</h2>
                        <p style="margin: 12px 0 20px;">Добавьте книги из каталога</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Перейти в каталог</a>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <script>
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        async function updateCart(id, action) {
            const res = await fetch(`/cart/${action}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (data.removed) {
                document.getElementById(`item-${id}`).remove();

                const totalElement = document.getElementById('cart-total');
                if (totalElement) {
                    totalElement.innerText = data.total;
                }

                if (document.querySelectorAll('.cart-item').length === 0) {
                    const topButtons = document.getElementById('top-buttons');

                    if (topButtons) {
                        topButtons.remove();
                    }

                    document.getElementById('cart-content').innerHTML = `
                        <div class="empty-cart">
                            <h2>Корзина пуста</h2>
                            <p style="margin: 12px 0 20px;">Добавьте книги из каталога</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">Перейти в каталог</a>
                        </div>
                    `;
                }

                return;
            }

            document.getElementById(`qty-${id}`).innerText = data.quantity;
            document.getElementById(`item-total-${id}`).innerText = data.item_total + ' ₽';
            document.getElementById('cart-total').innerText = data.total;
        }

        async function clearCart() {
            if (!confirm('Очистить корзину?')) {
                return;
            }

            await fetch('/cart/clear', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });

            const topButtons = document.getElementById('top-buttons');

            if (topButtons) {
                topButtons.remove();
            }

            document.getElementById('cart-content').innerHTML = `
                <div class="empty-cart">
                    <h2>Корзина пуста</h2>
                    <p style="margin: 12px 0 20px;">Добавьте книги из каталога</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">Перейти в каталог</a>
                </div>
            `;
        }
    </script>
</body>
</html>