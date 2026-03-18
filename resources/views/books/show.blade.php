<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $book->book_name }} - Книжный Мир</title>

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
            display: inline-block;
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
            background: #f3f4f6;
        }

        .book-category {
            font-size: 0.85rem;
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

        .book-meta {
            margin-bottom: 1.5rem;
            color: #555;
            line-height: 1.8;
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
            gap: 15px;
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

            .site-footer {
                width: calc(100% - 2rem);
                padding: 1.5rem;
            }

            .footer-bottom {
                flex-direction: column;
                align-items: flex-start;
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
            </ul>
        </nav>

        <div class="auth-buttons">
            <a href="{{ route('cart.index') }}" class="cart-link" title="Корзина">
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
            <div class="book-page">
                <a href="{{ route('home') }}" class="back-link">← Назад в каталог</a>

                <div class="book-layout">
                    <div>
                        <img
                            src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                            alt="{{ $book->book_name }}"
                            class="book-image"
                        >
                    </div>

                    <div>
                        <div class="book-category">
                            Книга
                        </div>

                        <h1 class="book-title">
                            {{ $book->book_name }}
                        </h1>

                        <p class="book-author">
                            Автор: {{ $book->author->author_name ?? 'Не указан' }}
                        </p>

                        <div class="book-rating">
                            <span class="stars">
                                @php
                                    $rating = round((float) ($book->average_rating ?? 0));
                                @endphp

                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $rating ? '★' : '☆' }}
                                @endfor
                            </span>

                            <span class="rating-count">
                                Рейтинг: {{ $book->average_rating ?? 0 }}
                            </span>
                        </div>

                        <div class="book-price-block">
                            <span class="book-price">
                                {{ number_format((float) $book->price, 2, '.', ' ') }} ₽
                            </span>
                        </div>

                        <div class="book-meta">
                            <div>
                                <strong>Издатель:</strong>
                                {{ $book->publisher->publisher_name ?? 'Не указан' }}
                            </div>

                            <div>
                                <strong>Дата публикации:</strong>
                                {{ $book->publication_date ? $book->publication_date->format('d.m.Y') : 'Не указана' }}
                            </div>

                            <div>
                                <strong>Количество страниц:</strong>
                                {{ $book->number_of_pages }}
                            </div>

                            <div>
                                <strong>Наличие:</strong>
                                @if($book->stock_quantity > 0)
                                    <span class="in-stock">В наличии ({{ $book->stock_quantity }})</span>
                                @else
                                    <span class="out-of-stock">Нет в наличии</span>
                                @endif
                            </div>
                        </div>

                        <div class="book-description">
                            <p>{{ $book->description ?? 'Описание отсутствует.' }}</p>
                        </div>

                        <div class="actions">
                            @if($book->stock_quantity > 0)
                                <button
                                    type="button"
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

                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                Вернуться в каталог
                            </a>
                        </div>
                    </div>
                </div>
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