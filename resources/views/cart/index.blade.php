<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background: rgba(255,255,255,0.96);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .page-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1.5rem;
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

        .qty-form {
            margin: 0;
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

            @if(session('success'))
                <div style="margin-bottom: 20px; background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 12px; font-weight: 600;">
                    {{ session('success') }}
                </div>
            @endif

            @if(count($cart))
                @foreach($cart as $item)
                    <div class="cart-item">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="cart-image">

                        <div>
                            <div class="cart-title">{{ $item['title'] }}</div>
                            <div class="cart-author">{{ $item['author'] }}</div>
                            <div class="cart-price">{{ $item['price'] }} ₽</div>

                            <div class="qty-controls">
                                <form action="{{ route('cart.decrease', $item['id']) }}" method="POST" class="qty-form">
                                    @csrf
                                    <button type="submit" class="qty-btn qty-btn-minus">-</button>
                                </form>

                                <div class="qty-value">{{ $item['quantity'] }}</div>

                                <form action="{{ route('cart.increase', $item['id']) }}" method="POST" class="qty-form">
                                    @csrf
                                    <button type="submit" class="qty-btn qty-btn-plus">+</button>
                                </form>
                            </div>
                        </div>

                        <div>
                            <div class="item-total">{{ $item['price'] * $item['quantity'] }} ₽</div>

                            <form action="{{ route('cart.remove', $item['id']) }}" method="POST" style="margin-top: 10px;">
                                @csrf
                                <button type="submit" class="btn btn-danger">Удалить</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <div class="summary">
                    <div class="summary-total">Итого: {{ $total }} ₽</div>
                    <a href="#" class="btn btn-primary">Оформить заказ</a>
                </div>
            @else
                <div class="empty-cart">
                    <h2>Корзина пуста</h2>
                    <p style="margin: 12px 0 20px;">Добавьте книги из каталога</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">Перейти в каталог</a>
                </div>
            @endif
        </div>
    </main>
</body>
</html>