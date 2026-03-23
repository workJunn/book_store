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
                <aside class="panel stack-md catalog-sidebar">
                    <div class="section-head">
                        <div>
                            <h1 class="section-title">{{ $periodMeta['title'] }}</h1>
                            <p class="section-text">{{ $periodMeta['description'] }}</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('catalog') }}" class="catalog-filters">
                        <div class="form-group">
                            <label for="search">Поиск</label>
                            <input id="search" type="text" name="search" value="{{ $filters['search'] }}" placeholder="Название книги">
                        </div>

                        <div class="form-group">
                            <label for="genre">Жанр</label>
                            <select id="genre" name="genre">
                                <option value="">Все жанры</option>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->getKey() }}" @selected((string) $filters['genre'] === (string) $genre->getKey())>
                                        {{ $genre->genre_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sort">Сортировка</label>
                            <select id="sort" name="sort">
                                <option value="">По названию</option>
                                <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Цена по возрастанию</option>
                                <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Цена по убыванию</option>
                                <option value="rating_desc" @selected($filters['sort'] === 'rating_desc')>По рейтингу</option>
                                <option value="newest" @selected($filters['sort'] === 'newest')>Сначала новые</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="period">Подборка</label>
                            <select id="period" name="period">
                                <option value="">Без периода</option>
                                <option value="year" @selected($filters['period'] === 'year')>Книги года</option>
                                <option value="month" @selected($filters['period'] === 'month')>Книги месяца</option>
                                <option value="week" @selected($filters['period'] === 'week')>Книги недели</option>
                            <option value="new" @selected($filters['period'] === 'new')>Новинки</option>
                            <option value="preorder" @selected($filters['period'] === 'preorder')>Предзаказы</option>
                            <option value="users" @selected($filters['period'] === 'users')>Рейтинг</option>
                        </select>
                    </div>

                        <label class="checkbox-row">
                            <input type="checkbox" name="in_stock" value="1" @checked($filters['in_stock'])>
                            <span>Только в наличии</span>
                        </label>

                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Применить</button>
                            <a href="{{ route('catalog') }}" class="btn btn-secondary">Сбросить</a>
                        </div>
                    </form>
                </aside>

                <div class="stack-md">
                    <section class="panel">
                        <p class="section-text">Найдено книг: {{ $books->total() }}</p>
                    </section>

                    @if($books->count())
                        <section class="catalog-grid">
                            @foreach($books as $book)
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
                                        <p class="muted">Рейтинг {{ number_format((float) $book->average_rating, 2, '.', ' ') }}</p>

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
                        </section>

                        <div class="catalog-pagination">
                            {{ $books->links() }}
                        </div>
                    @else
                        <section class="panel empty-state">
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
