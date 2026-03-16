<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог - Книжный Мир</title>

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

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* корзина */

        .cart-link {
            position: relative;
            text-decoration: none;
            font-size: 1.6rem;
            color: #667eea;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #ef4444;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 50%;
        }

        /* иконка профиля */

        .profile-link {
            text-decoration: none;
            color: #667eea;
            font-size: 1.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-link:hover {
            color: #5568d3;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        main {
            flex: 1;
            padding: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-title {
            color: white;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .book-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .book-image {
            width: 100%;
            height: 350px;
            object-fit: cover;
        }

        .book-content {
            padding: 1.5rem;
        }

        .book-title {
            font-size: 1.25rem;
            margin-bottom: 0.4rem;
            font-weight: 700;
        }

        .book-author {
            color: #666;
            margin-bottom: 1rem;
        }

        .book-description {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 1rem;
        }

        .book-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .book-price {
            font-size: 1.4rem;
            font-weight: bold;
            color: #667eea;
        }

        .old-price {
            font-size: 1rem;
            color: #999;
            text-decoration: line-through;
            margin-left: 5px;
        }
    </style>
</head>

<body>

<header>

    <a href="{{ route('home') }}" class="logo">
        📚 Книжный Мир
    </a>

    <nav>
        <ul>
            <li><a href="{{ route('home') }}">Главная</a></li>
            <li><a href="{{ route('home') }}">Каталог</a></li>
        </ul>
    </nav>

    <div class="auth-buttons">

        <a href="{{ route('cart.index') }}" class="cart-link">
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
            <a href="{{ route('dashboard') }}" class="profile-link" title="Профиль">
                👤
            </a>
        @endauth

    </div>

</header>

<main>

<div class="container">

<h1 class="page-title">📖 Каталог книг</h1>

@if(session('success'))
    <div style="background:#dcfce7;color:#166534;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center;">
        {{ session('success') }}
    </div>
@endif

<div class="books-grid">

@foreach($books as $book)

<div class="book-card">

    <a href="{{ route('books.show', $book['id']) }}">
        <img src="{{ $book['image'] }}" class="book-image">
    </a>

    <div class="book-content">

        <h3 class="book-title">{{ $book['title'] }}</h3>
        <p class="book-author">{{ $book['author'] }}</p>

        <p class="book-description">
            {{ $book['description'] }}
        </p>

        <div class="book-footer">

            <div class="book-price">
                {{ $book['price'] }} ₽

                @if($book['old_price'])
                    <span class="old-price">
                        {{ $book['old_price'] }} ₽
                    </span>
                @endif
            </div>

            <form action="{{ route('cart.add', $book['id']) }}" method="POST">
                @csrf
                <button class="btn btn-primary">
                    В корзину
                </button>
            </form>

        </div>

    </div>

</div>

@endforeach

</div>
</div>

</main>

</body>
</html>