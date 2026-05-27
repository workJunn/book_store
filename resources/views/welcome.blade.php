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
            <section class="genre-showcase" data-genre-showcase>
                <div class="genre-showcase__viewport" data-genre-showcase-viewport>
                    <div class="genre-showcase__track" data-genre-showcase-track>
                        @foreach($genreSlides as $slide)
                            <article class="hero-simple genre-showcase__slide" data-genre-showcase-slide>
                                <div class="stack-md">
                                    <h1 class="hero-simple__title hero-simple__title--compact">{{ $slide['title'] }}</h1>
                                    <p class="hero-simple__text">{{ $slide['description'] }}</p>
                                </div>

                                <div class="simple-grid simple-grid--2">
                                    @foreach($slide['books'] as $book)
                                        <article class="card">
                                            <a href="{{ route('books.show', $book->getKey()) }}" class="card__image-link">
                                                <img
                                                    src="{{ $book->cover_image_url }}"
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
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="stack-md ratings-section">
                <h2 class="section-title">Рейтинги книг</h2>
                <div class="simple-grid simple-grid--5">
                    @foreach($quickRankings as $ranking)
                        @php
                            $rankingQuery = match ($ranking['period']) {
                                'new' => ['period' => 'new', 'sort' => 'newest'],
                                'users' => ['period' => 'users', 'sort' => 'rating_desc'],
                                default => ['period' => $ranking['period']],
                            };
                        @endphp

                        <a href="{{ route('catalog', $rankingQuery) }}" class="link-card link-card--ranking">
                            <div class="stack-sm">
                                <strong>{{ $ranking['title'] }}</strong>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="stack-md carousel-section">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Новинки</h2>
                    </div>
                    <a href="{{ route('catalog', ['period' => 'new', 'sort' => 'newest']) }}" class="text-link">Все новинки →</a>
                </div>

                <section class="book-shelf book-shelf--promo" data-book-shelf>
                    <div class="book-shelf__stage">
                        <button class="book-shelf__arrow book-shelf__arrow--prev" data-shelf-direction="prev" type="button" aria-label="Предыдущие новинки">&lt;</button>
                        <div class="book-shelf__viewport" data-shelf-viewport>
                            <div class="book-shelf__track" data-shelf-track>
                                @foreach($newArrivals as $book)
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
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                        <button class="book-shelf__arrow book-shelf__arrow--next" data-shelf-direction="next" type="button" aria-label="Следующие новинки">&gt;</button>
                    </div>
                </section>
            </section>

            <section class="stack-md">

                <section class="stack-md" id="book-shelves">
                    @foreach($shelves as $shelf)
                        <section class="stack-md carousel-section">
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
