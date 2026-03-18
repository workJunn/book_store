<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $book->book_name }} - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-page="book" data-home-url="{{ route('catalog') }}">
    <header class="site-header">
        <div class="site-header__inner container container-wide">
            <a href="{{ route('home') }}" class="site-logo">📚 Книжный Мир</a>

            <nav class="site-nav">
                <ul>
                    <li><a href="{{ route('home') }}">Главная</a></li>
                    <li><a href="{{ route('catalog') }}">Каталог</a></li>
                    <li><a href="{{ route('favorites') }}">Избранное</a></li>
                </ul>
            </nav>

            <div class="site-actions">
                <a href="{{ route('favorites') }}" class="favorites-link" title="Избранное">
                    ❤
                    <span class="cart-count" data-favorites-count>0</span>
                </a>

                <a href="{{ route('cart.index') }}" class="cart-link" title="Корзина">
                    🛒
                    <span class="cart-count" data-cart-count>
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
        </div>
    </header>

    <main class="site-main">
        <div class="container">
            <div class="book-page page-panel">
                <a href="{{ route('catalog') }}" class="back-link">← Назад в каталог</a>

                <div class="book-layout">
                    <div>
                        <img
                            src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                            alt="{{ $book->book_name }}"
                            class="book-image"
                        >
                    </div>

                    <div>
                        <div class="book-category">Книга</div>

                        <h1 class="book-title">{{ $book->book_name }}</h1>

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
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-favorite-toggle
                                data-book-id="{{ $book->getKey() }}"
                                data-book-title="{{ $book->book_name }}"
                                data-book-author="{{ $book->author->author_name ?? 'Не указан' }}"
                                data-book-price="{{ number_format((float) $book->price, 0, '.', '') }}"
                                data-book-rating="{{ $book->average_rating ?? 0 }}"
                                data-book-image="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                data-book-url="{{ route('books.show', $book->getKey()) }}"
                            >
                                ❤ В избранное
                            </button>

                            @if($book->stock_quantity > 0)
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    data-add-to-cart="{{ $book->getKey() }}"
                                >
                                    В корзину
                                </button>
                            @else
                                <button class="btn btn-secondary" type="button" disabled>
                                    Нет в наличии
                                </button>
                            @endif

                            <a href="{{ route('catalog') }}" class="btn btn-secondary">
                                Вернуться в каталог
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="flash-message" class="flash-message" hidden></div>

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
            <div>© Книжный Мир 2024</div>

            <div class="socials">
                <a href="#">VK</a>
                <a href="#">X</a>
                <a href="#">OK</a>
                <a href="#">TG</a>
                <a href="#">YT</a>
            </div>

            <div class="app-link">📱 Приложение для Android</div>
        </div>
    </footer>
</body>
</html>
