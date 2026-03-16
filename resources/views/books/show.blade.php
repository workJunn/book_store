<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book['title'] }} - Книжный Мир</title>
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
            display: flex;
            flex-direction: column;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo:hover {
            color: #5568d3;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #667eea;
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cart-link {
            position: relative;
            text-decoration: none;
            color: #667eea;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -12px;
            background: #ef4444;
            color: white;
            font-size: 0.75rem;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            padding: 0 4px;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        main {
            flex: 1;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .book-page {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 1.5rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            color: #5568d3;
        }

        .book-layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 2rem;
            align-items: start;
        }

        .book-image {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            object-fit: cover;
        }

        .book-category {
            font-size: 0.8rem;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 0.7rem;
        }

        .book-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 0.8rem;
            line-height: 1.2;
        }

        .book-author {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .book-rating {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 1.2rem;
        }

        .stars {
            color: #fbbf24;
            font-size: 1.2rem;
        }

        .rating-count {
            color: #999;
            font-size: 0.95rem;
        }

        .book-price-block {
            background: #f8f9ff;
            border: 2px solid #e5e7ff;
            border-radius: 15px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .book-price {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }

        .old-price {
            font-size: 1.2rem;
            color: #999;
            text-decoration: line-through;
            margin-left: 0.7rem;
            font-weight: 400;
        }

        .book-description {
            color: #555;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .actions form {
            margin: 0;
        }

        .badge {
            display: inline-block;
            margin-bottom: 1rem;
            padding: 0.45rem 0.9rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-new {
            background: #10b981;
            color: white;
        }

        .badge-sale {
            background: #ef4444;
            color: white;
        }

        .success-message {
            max-width: 700px;
            margin: 0 auto 20px;
            background: #dcfce7;
            color: #166534;
            padding: 15px 20px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .book-layout {
                grid-template-columns: 1fr;
            }

            .book-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            nav ul {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            main {
                padding: 1rem;
            }

            .book-page {
                padding: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('home') }}" class="logo">📚 Книжный Мир</a>

        <nav>
            <ul>
                <li><a href="{{ route('home') }}">Главная</a></li>
                <li><a href="{{ route('home') }}">Каталог</a></li>
                <li><a href="#">Новинки</a></li>
                <li><a href="#">Акции</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <a href="{{ route('cart.index') }}" class="cart-link" title="Корзина">
                🛒
                <span class="cart-count">
                    {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                </span>
            </a>

            @guest
                <a href="{{ route('User_login') }}" class="btn btn-secondary">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endguest

            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
            @endauth
        </div>
    </header>

    <main>
        <div class="container">
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <div class="book-page">
                <a href="{{ route('home') }}" class="back-link">← Назад в каталог</a>

                <div class="book-layout">
                    <div>
                        <img src="{{ $book['image'] }}" alt="{{ $book['title'] }}" class="book-image">
                    </div>

                    <div>
                        @if($book['badge'])
                            <span class="badge {{ $book['badge_type'] === 'new' ? 'badge-new' : 'badge-sale' }}">
                                {{ $book['badge'] }}
                            </span>
                        @endif

                        <div class="book-category">{{ $book['category'] }}</div>
                        <h1 class="book-title">{{ $book['title'] }}</h1>
                        <p class="book-author">Автор: {{ $book['author'] }}</p>

                        <div class="book-rating">
                            <span class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $book['rating'] ? '★' : '☆' }}
                                @endfor
                            </span>
                            <span class="rating-count">{{ $book['reviews'] }} отзывов</span>
                        </div>

                        <div class="book-price-block">
                            <span class="book-price">{{ $book['price'] }} ₽</span>
                            @if($book['old_price'])
                                <span class="old-price">{{ $book['old_price'] }} ₽</span>
                            @endif
                        </div>

                        <div class="book-description">
                            <p>{{ $book['full_description'] ?? $book['description'] }}</p>
                        </div>

                        <div class="actions">
                            <form action="{{ route('cart.add', $book['id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">В корзину</button>
                            </form>

                            <a href="{{ route('home') }}" class="btn btn-secondary">Вернуться в каталог</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>