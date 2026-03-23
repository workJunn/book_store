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
                <aside class="stack-md catalog-sidebar">
                    @if($filters['period'] !== '')
                        <div class="catalog-filter-section stack-sm">
                            <div>
                                <h1 class="section-title">{{ $periodMeta['title'] }}</h1>
                                <p class="section-text">{{ $periodMeta['description'] }}</p>
                            </div>
                        </div>
                    @endif

                    <section class="catalog-filter-section stack-sm">
                        <h2 class="catalog-filter-title">Категории</h2>
                        <div class="catalog-category-list">
                            <a
                                href="{{ route('catalog', array_merge(request()->except(['genre', 'page']), [])) }}"
                                class="catalog-category-link {{ $filters['genre'] === '' ? 'is-active' : '' }}"
                            >
                                Все категории
                            </a>

                            @foreach($genres as $genre)
                                <a
                                    href="{{ route('catalog', array_merge(request()->except(['page']), ['genre' => $genre->getKey()])) }}"
                                    class="catalog-category-link {{ (string) $filters['genre'] === (string) $genre->getKey() ? 'is-active' : '' }}"
                                >
                                    {{ $genre->genre_name }}
                                </a>
                            @endforeach
                        </div>
                    </section>

                </aside>

                <div class="stack-md">
                    <section>
                        <p class="section-text">Найдено книг: {{ $books->count() }}</p>
                    </section>

                    @if($books->count())
                        <section class="catalog-grid">
                            @foreach($books as $book)
                                @php
                                    $currentPrice = (float) $book->price;
                                    $oldPrice = ceil(($currentPrice * 1.2) / 10) * 10;
                                    $discountPercent = max(10, (int) round((1 - ($currentPrice / $oldPrice)) * 100));
                                @endphp

                                <article class="store-card store-card--promo">
                                    <a href="{{ route('books.show', $book->getKey()) }}" class="card__image-link">
                                        <img
                                            src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                            class="card__image"
                                            alt="{{ $book->book_name }}"
                                        >
                                    </a>
                                    <div class="card__body">
                                        <div class="price-stack">
                                            <div class="price">{{ number_format($currentPrice, 0, '.', ' ') }} ₽</div>
                                            <div class="price-meta">
                                                <span class="price-old">{{ number_format($oldPrice, 0, '.', ' ') }} ₽</span>
                                                <span class="discount-badge">-{{ $discountPercent }}%</span>
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
                        </section>

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
