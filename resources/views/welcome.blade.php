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
    @include('partials.site-header')

    <main class="site-main">
        <section class="container stack-lg">
            <section class="hero-simple panel">
                <div class="stack-md">
                    <p class="eyebrow">Онлайн-магазин книг</p>
                    <h1 class="hero-simple__title">Книги без лишнего шума</h1>
                    <p class="hero-simple__text">
                        На главной собраны рейтинги, в каталоге доступны фильтры, а карточка книги
                        позволяет быстро добавить товар в корзину или избранное.
                    </p>
                    <div class="actions">
                        <a href="{{ route('catalog') }}" class="btn btn-primary">Открыть каталог</a>
                    </div>
                </div>

                <div class="simple-grid simple-grid--2">
                    @foreach($featuredBooks as $book)
                        <article class="card">
                            <a href="{{ route('books.show', $book->getKey()) }}" class="card__image-link">
                                <img
                                    src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                    class="card__image"
                                    alt="{{ $book->book_name }}"
                                >
                            </a>
                            <div class="card__body">
                                <div class="muted">Рейтинг {{ number_format((float) $book->average_rating, 2, '.', ' ') }}</div>
                                <a href="{{ route('books.show', $book->getKey()) }}" class="card__title">{{ $book->book_name }}</a>
                                <div class="muted">{{ $book->author->author_name ?? 'Не указан' }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="stack-md ratings-section">
                <h2 class="section-title">Рейтинги книг</h2>
                <div class="simple-grid simple-grid--5">
                    @foreach($quickRankings as $ranking)
                        <a href="{{ route('catalog', ['period' => $ranking['period'], 'sort' => 'rating_desc']) }}" class="link-card link-card--ranking">
                            <div class="stack-sm">
                                <strong>{{ $ranking['title'] }}</strong>
                                <span>{{ $ranking['description'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="stack-md">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Книги</h2>
                        <p class="section-text">Карточки книг в полках с горизонтальной каруселью.</p>
                    </div>
                    <a href="{{ route('catalog') }}" class="text-link">Все книги</a>
                </div>

                <section class="stack-md" id="book-shelves">
                    @foreach($shelves as $shelf)
                        <section class="panel stack-md">
                            <div class="section-head">
                                <div>
                                    <h3 class="section-title">{{ $shelf['title'] }}</h3>
                                    <p class="section-text">{{ $shelf['description'] }}</p>
                                </div>
                                <a href="{{ route('catalog') }}" class="text-link">Все книги</a>
                            </div>

                            <section class="book-shelf" data-book-shelf>
                                <div class="book-shelf__stage">
                                    <button class="book-shelf__arrow book-shelf__arrow--prev" data-shelf-direction="prev" type="button" aria-label="Предыдущие книги">&lt;</button>
                                    <div class="book-shelf__viewport" data-shelf-viewport>
                                        <div class="book-shelf__track" data-shelf-track>
                                            @foreach($shelf['books'] as $book)
                                                <article class="store-card">
                                                    <a href="{{ route('books.show', $book->getKey()) }}" class="card__image-link">
                                                        <img
                                                            src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                                            class="card__image"
                                                            alt="{{ $book->book_name }}"
                                                        >
                                                    </a>
                                                    <div class="card__body">
                                                        <div class="price">{{ number_format((float) $book->price, 0, '.', ' ') }} ₽</div>
                                                        <a href="{{ route('books.show', $book->getKey()) }}" class="card__title">{{ $book->book_name }}</a>
                                                        <p class="muted">{{ $book->author->author_name ?? 'Автор не указан' }}</p>
                                                        <div class="book-card__actions">
                                                            @if($book->stock_quantity > 0)
                                                                <button class="btn btn-primary btn-book-action" data-add-to-cart="{{ $book->getKey() }}" type="button">В корзину</button>
                                                            @else
                                                                <button class="btn btn-secondary btn-book-action" type="button" disabled>Нет в наличии</button>
                                                            @endif
                                                            <button
                                                                class="favorite-button favorite-button--storefront"
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
                                    <button class="book-shelf__arrow book-shelf__arrow--next" data-shelf-direction="next" type="button" aria-label="Следующие книги">&gt;</button>
                                </div>
                            </section>
                        </section>
                    @endforeach
                </section>
            </section>

        </section>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
    @include('partials.site-footer')
</body>
</html>
