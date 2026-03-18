<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .profile-link {
            text-decoration: none;
            color: #667eea;
            font-size: 1.7rem;
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

        .book-meta {
            font-size: 0.9rem;
            color: #555;
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

        .in-stock {
            color: #16a34a;
            font-weight: 600;
        }

        .out-of-stock {
            color: #dc2626;
            font-weight: 600;
        }

        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 14px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .flash-success {
            background: #16a34a;
        }

        .flash-error {
            background: #dc2626;
        }

        .site-footer {
            margin: 2rem auto 0;
            width: calc(100% - 4rem);
            max-width: 1400px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 20px 20px 0 0;
            padding: 2rem;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 1rem;
        }

        .footer-links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .footer-links a:hover {
            color: #764ba2;
        }

        .footer-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 1rem;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .socials {
            display: flex;
            gap: 10px;
        }

        .socials a {
            text-decoration: none;
            color: white;
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
        }

        .app-link {
            color: #667eea;
            font-weight: 700;
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
                <span class="cart-count" id="cart-count">
                    {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                </span>
            </a>

            @guest
                <a href="{{ route('User_login') }}" class="btn btn-secondary">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endguest

            @auth
                <a href="{{ route('dashboard') }}" class="profile-link">👤</a>
            @endauth
        </div>
    </header>

    <main>
        <div class="container">
            <h1 class="page-title">📖 Каталог книг</h1>

            <div class="books-grid">
                @foreach($books as $book)
                    <div class="book-card">
                        <a href="{{ route('books.show', $book->getKey()) }}">
                            <img
                                src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                class="book-image"
                                alt="{{ $book->book_name }}"
                            >
                        </a>

                        <div class="book-content">
                            <h3 class="book-title">
                                {{ $book->book_name }}
                            </h3>

                            <p class="book-author">
                                Автор: {{ $book->author->author_name ?? 'Не указан' }}
                            </p>

                            <p class="book-description">
                                {{ $book->description ?? 'Описание отсутствует.' }}
                            </p>

                            <div class="book-meta">
                                <div>
                                    Страниц: {{ $book->number_of_pages }}
                                </div>

                                <div>
                                    Рейтинг: {{ $book->average_rating ?? 0 }}
                                </div>

                                <div>
                                    Наличие:

                                    @if($book->stock_quantity > 0)
                                        <span class="in-stock">
                                            В наличии ({{ $book->stock_quantity }})
                                        </span>
                                    @else
                                        <span class="out-of-stock">
                                            Нет в наличии
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="book-footer">
                                <div class="book-price">
                                    {{ number_format((float) $book->price, 2, '.', ' ') }} ₽
                                </div>

                                @if($book->stock_quantity > 0)
                                    <button
                                        class="btn btn-primary"
                                        onclick="addToCart({{ $book->getKey() }})"
                                    >
                                        В корзину
                                    </button>
                                @else
                                    <button class="btn btn-secondary" disabled>
                                        Нет в наличии
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <div id="flash-message" class="flash-message"></div>

    <footer class="site-footer">
        <div class="footer-links">
            <a href="#">Правовая информация</a>
            <a href="#">Контакты</a>
            <a href="#">Реклама</a>
            <a href="#">Политика конфиденциальности</a>
            <a href="#">Условия использования</a>
            <a href="#">Пресс-релизы</a>
        </div>

        <div class="footer-info">
            На информационном ресурсе применяются рекомендательные технологии
            в соответствии с правилами сервиса.
        </div>

        <div class="footer-bottom">
            <div>
                © Книжный Мир 2024
            </div>

            <div class="socials">
                <a href="#">VK</a>
                <a href="#">X</a>
                <a href="#">OK</a>
                <a href="#">TG</a>
                <a href="#">YT</a>
            </div>

            <div class="app-link">
                📱 Приложение для Android
            </div>
        </div>
    </footer>

    <script>
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        async function addToCart(id) {
            let res;
            let data;

            try {
                res = await fetch(`/cart/add/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                data = await res.json();
            } catch (error) {
                showMessage('Ошибка сервера', 'error');
                return;
            }

            const cartCount = document.getElementById('cart-count');

            if (cartCount && data.cart_count !== undefined) {
                cartCount.innerText = data.cart_count;
            }

            showMessage(data.message || 'Книга добавлена', 'success');
        }

        function showMessage(text, type = 'success') {
            const message = document.getElementById('flash-message');

            message.className = 'flash-message';
            message.classList.add(type === 'success' ? 'flash-success' : 'flash-error');

            message.textContent = text;
            message.style.display = 'block';

            setTimeout(() => {
                message.style.display = 'none';
            }, 2000);
        }
    </script>

</body>

</html>