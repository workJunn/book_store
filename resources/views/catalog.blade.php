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
    @include('partials.site-header')

    <main class="site-main">
        <section class="container stack-lg">
            <section class="catalog-layout">
                <aside class="stack-md catalog-sidebar" data-catalog-filters>
                    <section class="catalog-filter-section stack-sm">
                        <h2 class="catalog-filter-title">Жанры</h2>
                        <div class="catalog-category-list">
                            @foreach($genres as $genre)
                                <a
                                    href="{{ route('catalog', array_merge(request()->except(['page', 'period', 'sort', 'genre']), ['genre' => $genre->getKey()])) }}"
                                    class="catalog-category-link {{ blank($filters['period']) && (string) $filters['genre'] === (string) $genre->getKey() ? 'is-active' : '' }}"
                                    data-catalog-filter-link
                                >
                                    {{ $genre->genre_name }}
                                </a>
                            @endforeach
                        </div>
                    </section>

                    <section class="catalog-filter-section stack-sm">
                        <h2 class="catalog-filter-title">Рейтинг книг</h2>
                        <div class="catalog-ranking-list">
                            @foreach($quickRankings as $ranking)
                                @php
                                    $rankingQuery = match ($ranking['period']) {
                                        'new' => array_merge(request()->except(['page', 'period', 'sort', 'genre']), ['period' => 'new', 'sort' => 'newest']),
                                        'users' => array_merge(request()->except(['page', 'period', 'sort', 'genre']), ['period' => 'users', 'sort' => 'rating_desc']),
                                        default => array_merge(request()->except(['page', 'period', 'sort', 'genre']), ['period' => $ranking['period']]),
                                    };
                                @endphp

                                <a
                                    href="{{ route('catalog', $rankingQuery) }}"
                                    class="catalog-category-link {{ blank($filters['genre']) && $filters['period'] === $ranking['period'] ? 'is-active' : '' }}"
                                    data-catalog-filter-link
                                >
                                    {{ $ranking['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </section>

                </aside>

                <div class="stack-md" data-catalog-results>
                    <section>
                        <p class="section-text">Найдено книг: {{ $foundBooksCount }}</p>
                    </section>

                    @if($books->count())
                        <section class="catalog-grid">
                            @foreach($books as $book)
                                    @php
                                        $currentPrice = (float) $book->price;
                                        $discountPercent = $book->getDisplayDiscountPercent();
                                    @endphp

                                <article class="store-card store-card--promo">
                                    <a href="{{ route('books.show', $book->getKey()) }}" class="card__image-link">
                                        <img
                                            src="{{ $book->cover_image_url }}"
                                            class="card__image"
                                            alt="{{ $book->book_name }}"
                                        >
                                    </a>
                                    <div class="card__body">
                                        <div class="price-stack">
                                            <div class="price-meta">
                                                <span class="price">{{ number_format($currentPrice, 0, '.', ' ') }} ₽</span>
                                                @if($discountPercent > 0)
                                                    <span class="discount-badge">-{{ $discountPercent }}%</span>
                                                @endif
                                            </div>
                                        </div>

                                        <a href="{{ route('books.show', $book->getKey()) }}" class="card__title">{{ $book->book_name }}</a>
                                        <p class="muted">{{ $book->author->author_name ?? 'Автор не указан' }}</p>

                                        <div class="book-card__actions">
                                            @if($book->stock_quantity > 0)
                                                <button type="button" class="btn btn-primary btn-book-action" data-add-to-cart="{{ $book->getKey() }}">В корзину</button>
                                            @else
                                                <button class="btn btn-secondary btn-book-action" type="button" disabled>Нет в наличии</button>
                                            @endif

                                            <button
                                                type="button"
                                                class="favorite-button favorite-button--storefront"
                                                data-favorite-toggle
                                                data-book-id="{{ $book->getKey() }}"
                                                data-book-title="{{ $book->book_name }}"
                                                data-book-author="{{ $book->author->author_name ?? 'Не указан' }}"
                                                data-book-price="{{ number_format((float) $book->price, 0, '.', '') }}"
                                                data-book-rating="{{ $book->average_rating ?? 0 }}"
                                                data-book-image="{{ $book->cover_image_url }}"
                                                data-book-url="{{ route('books.show', $book->getKey()) }}"
                                                aria-label="Добавить в избранное"
                                            >
                                                <span class="bookmark-icon bookmark-icon--button" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </section>

                        @if($books->hasPages())
                            <section>
                                {{ $books->links() }}
                            </section>
                        @endif

                    @else
                        <section class="empty-state catalog-empty">
                            <h2>Ничего не найдено</h2>
                            <p>Измените фильтры или вернитесь ко всему каталогу.</p>
                        </section>
                    @endif
                </div>
            </section>
        </section>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
    @include('partials.site-footer')
</body>
</html>
