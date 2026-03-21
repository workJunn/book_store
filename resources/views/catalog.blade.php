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
                    <span class="bookmark-icon" aria-hidden="true"></span>
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

    <main class="site-main site-main--catalog">
        <section class="catalog-stage">
            <section class="catalog-results catalog-results--full">
                <h1 class="page-heading page-heading--catalog">Каталог книг</h1>

                <div class="catalog-results__toolbar catalog-results__toolbar--stage">
                    <div class="catalog-results__count">
                        Найдено книг: {{ $books->total() }}
                    </div>
                </div>

                <div class="catalog-stage__divider" aria-hidden="true"></div>

                @if($books->count())
                    <div class="catalog-grid catalog-grid--storefront">
                        @foreach($books as $book)
                            @php
                                $currentPrice = (float) $book->price;
                                $oldPrice = ceil(($currentPrice * 1.22) / 10) * 10;
                                $discountPercent = max(10, (int) round((1 - ($currentPrice / $oldPrice)) * 100));
                            @endphp

                            <article class="catalog-card catalog-card--storefront">
                                <a href="{{ route('books.show', $book->getKey()) }}" class="catalog-card__image-link catalog-card__image-link--storefront">
                                    <img
                                        src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                        class="catalog-card__image"
                                        alt="{{ $book->book_name }}"
                                    >
                                </a>

                                <div class="catalog-card__body catalog-card__body--storefront">
                                    <div class="catalog-price-block">
                                        <div class="catalog-price-block__current">{{ number_format($currentPrice, 0, '.', ' ') }} ₽</div>
                                        <div class="catalog-price-block__row">
                                            <span class="catalog-price-block__old">{{ number_format($oldPrice, 0, '.', ' ') }} ₽</span>
                                            <span class="catalog-price-block__discount">-{{ $discountPercent }}%</span>
                                        </div>
                                    </div>

                                    <a href="{{ route('books.show', $book->getKey()) }}" class="catalog-card__title-link">
                                        <h2 class="catalog-card__title catalog-card__title--storefront">{{ $book->book_name }}</h2>
                                    </a>

                                    <p class="catalog-card__author catalog-card__author--storefront">
                                        {{ $book->author->author_name ?? 'Автор не указан' }}
                                    </p>

                                    <div class="catalog-card__footer catalog-card__footer--storefront">
                                        <div class="book-card__actions book-card__actions--storefront">
                                            @if($book->stock_quantity > 0)
                                                <button
                                                    class="btn btn-primary btn-book-action btn-book-action--storefront"
                                                    data-add-to-cart="{{ $book->getKey() }}"
                                                    type="button"
                                                >
                                                    В корзину
                                                </button>
                                            @else
                                                <button class="btn btn-secondary btn-book-action btn-book-action--storefront" type="button" disabled>
                                                    Нет в наличии
                                                </button>
                                            @endif

                                            <button
                                                class="favorite-button favorite-button--inline favorite-button--storefront"
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
                                                <span class="bookmark-icon bookmark-icon--button" aria-hidden="true"></span>
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
                        <p>Сейчас в каталоге нет книг по текущему набору данных.</p>
                    </div>
                @endif
            </section>
        </section>
    </main>

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

            <div class="app-link">📱 Приложение для Android</div>
        </div>
    </footer>
</body>
</html>
