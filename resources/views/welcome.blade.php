<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-page="welcome" data-home-url="{{ route('catalog') }}">
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

    <main class="site-main">
        <section class="container container-wide">
            <div class="welcome-hero page-panel">
                <div class="welcome-hero__content">
                    <span class="welcome-hero__eyebrow">Онлайн-книжный магазин</span>
                    <h1 class="welcome-hero__title">Выбирайте книги по настроению, семье и школьной программе</h1>
                    <p class="welcome-hero__text">
                        На главной собраны тематические полки, а в каталоге доступны фильтры,
                        сортировка и быстрый переход к карточкам книг.
                    </p>

                    <div class="welcome-hero__actions">
                        <a href="{{ route('catalog') }}" class="btn btn-primary">Открыть каталог</a>
                        <a href="#book-shelves" class="btn btn-secondary">Смотреть подборки</a>
                    </div>
                </div>

                <div class="welcome-hero__featured">
                    @foreach($featuredBooks as $book)
                        <article class="hero-book-card">
                            <a href="{{ route('books.show', $book->getKey()) }}" class="hero-book-card__image-link">
                                <img
                                    src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                    class="hero-book-card__image"
                                    alt="{{ $book->book_name }}"
                                >
                            </a>

                            <div class="hero-book-card__body">
                                <div class="hero-book-card__rating">Рейтинг {{ $book->average_rating ?? 0 }}</div>
                                <a href="{{ route('books.show', $book->getKey()) }}" class="hero-book-card__title-link">
                                    {{ $book->book_name }}
                                </a>
                                <div class="hero-book-card__author">{{ $book->author->author_name ?? 'Не указан' }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="container container-wide" id="book-shelves">
            <div class="welcome-section-heading">
                <div>
                    <h2 class="welcome-section-heading__title">Подборки книг</h2>
                    <p class="welcome-section-heading__text">Горизонтальные витрины с быстрым просмотром, ценой и переходом ко всему каталогу.</p>
                </div>
            </div>

            <div class="book-shelves">
                @foreach($shelves as $shelf)
                    <section class="book-shelf" data-book-shelf>
                        <div class="book-shelf__header">
                            <div>
                                <h3 class="book-shelf__title">{{ $shelf['title'] }}</h3>
                                <p class="book-shelf__description">{{ $shelf['description'] }}</p>
                            </div>

                            <a href="{{ route('catalog') }}" class="book-shelf__link">Смотреть все</a>
                        </div>

                        <div class="book-shelf__stage">
                            <button class="book-shelf__arrow book-shelf__arrow--prev" data-shelf-direction="prev" type="button" aria-label="Назад">
                                &lt;
                            </button>

                            <div class="book-shelf__viewport" data-shelf-viewport>
                                <div class="book-shelf__track" data-shelf-track>
                                    @foreach($shelf['books'] as $book)
                                        @php
                                            $currentPrice = (float) $book->price;
                                            $oldPrice = ceil(($currentPrice * 1.2) / 10) * 10;
                                            $discountPercent = max(10, (int) round((1 - ($currentPrice / $oldPrice)) * 100));
                                        @endphp

                                        <article class="book-shelf__card">
                                            <a href="{{ route('books.show', $book->getKey()) }}" class="book-shelf__image-link">
                                                <img
                                                    src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                                    class="book-shelf__image"
                                                    alt="{{ $book->book_name }}"
                                                >
                                            </a>

                                            <div class="book-shelf__card-body">
                                                <div class="book-shelf__price-block">
                                                    <div class="book-shelf__price-current">{{ number_format($currentPrice, 0, '.', ' ') }} ₽</div>
                                                    <div class="book-shelf__price-row">
                                                        <span class="book-shelf__price-old">{{ number_format($oldPrice, 0, '.', ' ') }} ₽</span>
                                                        <span class="book-shelf__discount">-{{ $discountPercent }}%</span>
                                                    </div>
                                                </div>

                                                <a href="{{ route('books.show', $book->getKey()) }}" class="book-shelf__card-title">
                                                    {{ $book->book_name }}
                                                </a>
                                                <p class="book-shelf__card-author">{{ $book->author->author_name ?? 'Автор не указан' }}</p>

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
                                        </article>
                                    @endforeach
                                </div>
                            </div>

                            <button class="book-shelf__arrow book-shelf__arrow--next" data-shelf-direction="next" type="button" aria-label="Вперед">
                                &gt;
                            </button>
                        </div>
                    </section>
                @endforeach
            </div>
        </section>
    </main>

    <section class="container container-wide">
        <h3 class="ratings-title">Рейтинги</h3>
        <div class="ratings-cards">
            <div class="rating-card">
                <div class="rating-card__title">Книга месяца</div>
                <div class="rating-card__subtitle">Март 2026</div>
            </div>
            <div class="rating-card">
                <div class="rating-card__title">Книга года</div>
                <div class="rating-card__subtitle">2025</div>
            </div>
            <div class="rating-card">
                <div class="rating-card__title">Топ продаж</div>
                <div class="rating-card__subtitle">В этом месяце</div>
            </div>
            <div class="rating-card">
                <div class="rating-card__title">Бестселлер</div>
                <div class="rating-card__subtitle">За все время</div>
            </div>
            <div class="rating-card">
                <div class="rating-card__title">Новинка</div>
                <div class="rating-card__subtitle">2026</div>
            </div>
        </div>
    </section>

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
