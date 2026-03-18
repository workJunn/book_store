<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Каталог - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-page="catalog" data-home-url="{{ route('catalog') }}">
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

                <a href="{{ route('cart.index') }}" class="cart-link">
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
        <section class="container container-wide">
            <h1 class="page-heading">Каталог книг</h1>

            <div class="catalog-layout">
                <aside class="catalog-filters page-panel">
                    <h2 class="catalog-filters__title">Фильтры</h2>

                    <form method="GET" action="{{ route('catalog') }}">
                        <div class="form-group">
                            <label for="search">Поиск по названию</label>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ $filters['search'] }}"
                                placeholder="Например, Преступление и наказание"
                            >
                        </div>

                        <div class="form-group">
                            <label for="genre">Жанр</label>
                            <select id="genre" name="genre">
                                <option value="">Все жанры</option>
                                @foreach($genres as $genre)
                                    <option
                                        value="{{ $genre->getKey() }}"
                                        @selected((string) $filters['genre'] === (string) $genre->getKey())
                                    >
                                        {{ $genre->genre_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sort">Сортировка</label>
                            <select id="sort" name="sort">
                                <option value="">По названию</option>
                                <option value="rating_desc" @selected($filters['sort'] === 'rating_desc')>
                                    По рейтингу
                                </option>
                                <option value="price_asc" @selected($filters['sort'] === 'price_asc')>
                                    Цена: сначала дешевле
                                </option>
                                <option value="price_desc" @selected($filters['sort'] === 'price_desc')>
                                    Цена: сначала дороже
                                </option>
                                <option value="newest" @selected($filters['sort'] === 'newest')>
                                    Сначала новее
                                </option>
                            </select>
                        </div>

                        <div class="form-group checkbox-group">
                            <input
                                type="checkbox"
                                id="in_stock"
                                name="in_stock"
                                value="1"
                                @checked($filters['in_stock'])
                            >
                            <label for="in_stock">Только в наличии</label>
                        </div>

                        <div class="catalog-filters__actions">
                            <button class="btn btn-primary" type="submit">Применить</button>
                            <a href="{{ route('catalog') }}" class="btn btn-secondary">Сбросить</a>
                        </div>
                    </form>
                </aside>

                <section class="catalog-results">
                    <div class="catalog-results__toolbar">
                        <div class="catalog-results__count">
                            Найдено книг: {{ $books->total() }}
                        </div>
                    </div>

                    @if($books->count())
                        <div class="catalog-grid">
                            @foreach($books as $book)
                                <article class="catalog-card">
                                    <a href="{{ route('books.show', $book->getKey()) }}" class="catalog-card__image-link">
                                        <img
                                            src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                            class="catalog-card__image"
                                            alt="{{ $book->book_name }}"
                                        >
                                    </a>

                                    <div class="catalog-card__body">
                                        <a href="{{ route('books.show', $book->getKey()) }}" class="catalog-card__title-link">
                                            <h2 class="catalog-card__title">{{ $book->book_name }}</h2>
                                        </a>

                                        <p class="catalog-card__author">
                                            Автор: {{ $book->author->author_name ?? 'Не указан' }}
                                        </p>

                                        @if($book->genres->isNotEmpty())
                                            <div class="catalog-card__genres">
                                                @foreach($book->genres as $genre)
                                                    <span class="catalog-chip">{{ $genre->genre_name }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="catalog-card__meta">
                                            Рейтинг: {{ $book->average_rating ?? 0 }}
                                        </div>

                                        <div class="catalog-card__price">
                                            {{ number_format((float) $book->price, 0, '.', ' ') }} ₽
                                        </div>

                                        <div class="catalog-card__footer">
                                            <div class="book-card__actions">
                                                @if($book->stock_quantity > 0)
                                                    <button
                                                        class="btn btn-primary btn-book-action"
                                                        data-add-to-cart="{{ $book->getKey() }}"
                                                        type="button"
                                                    >
                                                        В корзину
                                                    </button>
                                                @else
                                                    <button class="btn btn-secondary btn-book-action" type="button" disabled>
                                                        Нет в наличии
                                                    </button>
                                                @endif

                                                <button
                                                    class="favorite-button favorite-button--inline"
                                                    type="button"
                                                    data-favorite-toggle
                                                    data-book-id="{{ $book->getKey() }}"
                                                    data-book-title="{{ $book->book_name }}"
                                                    data-book-author="{{ $book->author->author_name ?? 'Не указан' }}"
                                                    data-book-price="{{ number_format((float) $book->price, 0, '.', '') }}"
                                                    data-book-rating="{{ $book->average_rating ?? 0 }}"
                                                    data-book-image="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                                    data-book-url="{{ route('books.show', $book->getKey()) }}"
                                                    aria-label="Добавить в избранное"
                                                >
                                                    ❤
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="catalog-pagination">
                            {{ $books->links() }}
                        </div>
                    @else
                        <div class="catalog-empty page-panel">
                            <h2>Ничего не найдено</h2>
                            <p>Попробуйте изменить жанр, сортировку или строку поиска.</p>
                            <a href="{{ route('catalog') }}" class="btn btn-secondary">Сбросить фильтры</a>
                        </div>
                    @endif
                </section>
            </div>
        </section>
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
