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
    @include('partials.site-header')

    <main class="site-main">
        <section class="container stack-lg">
            <section class="stack-md">
                <a href="{{ route('catalog') }}" class="text-link">← Назад в каталог</a>

                @php
                    $rating = round((float) ($book->average_rating ?? 0));
                    $reviewWord = $book->reviews_count === 1 ? 'отзыв' : (($book->reviews_count >= 2 && $book->reviews_count <= 4) ? 'отзыва' : 'отзывов');
                @endphp

                <div class="book-layout">
                    <div class="stack-md">
                        <img
                            src="{{ $book->cover_image_url }}"
                            alt="{{ $book->book_name }}"
                            class="book-image"
                        >

                        <div class="chips">
                            @foreach($book->genres as $genre)
                                <span class="chip">{{ $genre->genre_name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="stack-md">
                            <div class="section-head">
                                <div>
                                    <h1 class="section-title">{{ $book->book_name }}</h1>
                                    <p class="section-text">Автор: {{ $book->author->author_name ?? 'Не указан' }}</p>
                                </div>
                            <div class="price-box">
                                <div class="price price--large">{{ number_format((float) $book->price, 2, '.', ' ') }} ₽</div>
                                <div class="muted">Цена за экземпляр</div>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="book-rating">
                                <span class="stars" data-book-rating-stars>
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $rating ? '★' : '☆' }}
                                    @endfor
                                </span>
                                <span data-book-average-rating>{{ number_format((float) $book->average_rating, 2, '.', ' ') }} / 5</span>
                            </div>
                            <div class="muted" data-book-reviews-summary>Основано на {{ $book->reviews_count }} {{ $reviewWord }}</div>
                        </div>

                        <div class="details-grid">
                            <div class="info-box">
                                <div class="info-label">Издатель</div>
                                <div class="info-value">{{ $book->publisher->publisher_name ?? 'Не указан' }}</div>
                            </div>
                            <div class="info-box">
                                <div class="info-label">Дата публикации</div>
                                <div class="info-value">{{ $book->publication_date ? $book->publication_date->format('d.m.Y') : 'Не указана' }}</div>
                            </div>
                            <div class="info-box">
                                <div class="info-label">Страниц</div>
                                <div class="info-value">{{ $book->number_of_pages }}</div>
                            </div>
                            <div class="info-box">
                                <div class="info-label">Жанры</div>
                                <div class="info-value">{{ $book->genres->isNotEmpty() ? $book->genres->pluck('genre_name')->join(', ') : 'Не указаны' }}</div>
                            </div>
                        </div>

                        <div class="actions">
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

                            @if($book->stock_quantity > 0)
                                <button type="button" class="btn btn-primary btn-book-action" data-add-to-cart="{{ $book->getKey() }}">В корзину</button>
                            @else
                                <button class="btn btn-secondary btn-book-action" type="button" disabled>Нет в наличии</button>
                            @endif
                        </div>

                        <div class="info-box stack-sm">
                            <h2 class="subheading">О книге</h2>
                            <p>{{ $book->description ?? 'Описание отсутствует.' }}</p>
                        </div>

                        @if($book->author?->biography)
                            <div class="info-box stack-sm">
                                <h2 class="subheading">Об авторе</h2>
                                <p>{{ $book->author->biography }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

                <div class="stack-md">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Оставить комментарий</h2>
                            <p class="section-text">Оценка влияет на средний рейтинг книги.</p>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="success-box">{{ session('status') }}</div>
                    @endif

                    @auth
                        @php
                            $selectedRating = (int) old('rating');
                        @endphp

                        <form method="POST" action="{{ route('books.reviews.store', $book) }}" class="review-form" data-review-form novalidate>
                            @csrf

                            <div class="form-group {{ $errors->has('rating') ? 'error' : '' }}">
                                <span class="review-stars__label">Ваша оценка</span>
                                <div class="review-stars" role="radiogroup" aria-label="Оценка книги">
                                    @for($value = 5; $value >= 1; $value--)
                                        <input
                                            class="review-stars__input"
                                            type="radio"
                                            id="rating-{{ $value }}"
                                            name="rating"
                                            value="{{ $value }}"
                                            @checked($selectedRating === $value)
                                            required
                                        >
                                        <label class="review-stars__star" for="rating-{{ $value }}" title="{{ $value }} из 5">★</label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group {{ $errors->has('review_text') ? 'error' : '' }}">
                                <label for="review_text">Комментарий</label>
                                <textarea
                                    id="review_text"
                                    name="review_text"
                                    rows="7"
                                    placeholder="Что вам понравилось в книге, кому она подойдет, стоит ли рекомендовать?"
                                >{{ old('review_text') }}</textarea>
                                @error('review_text')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Опубликовать отзыв
                            </button>
                        </form>
                    @else
                        <div class="empty-state">
                            <p>Чтобы оставить комментарий, нужно войти в аккаунт.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary">Войти</a>
                        </div>
                    @endauth
                </div>

            <section class="stack-lg">
                <div class="reviews-hub" data-reviews-hub>
                    <div class="reviews-toolbar">
                        <form method="GET" action="{{ route('books.show', $book) }}" class="reviews-sort">
                            <label for="review_sort">Сортировка</label>
                            <select id="review_sort" name="review_sort" onchange="this.form.submit()">
                                <option value="newest" @selected($reviewSort === 'newest')>Сначала новые</option>
                                <option value="oldest" @selected($reviewSort === 'oldest')>Сначала старые</option>
                                <option value="rating_desc" @selected($reviewSort === 'rating_desc')>Высокая оценка</option>
                                <option value="rating_asc" @selected($reviewSort === 'rating_asc')>Низкая оценка</option>
                            </select>
                        </form>

                        <div class="reviews-tabs" aria-label="Отзывы">
                            <div class="reviews-tab is-active">
                                Отзывы читателей <span class="reviews-tab__count" data-reviews-count>{{ $reviews->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="reviews-pane is-active" id="reviews-pane-users" role="tabpanel" aria-labelledby="reviews-tab-users" data-reviews-tab-pane="users">
                        <div class="book-reviews-list" data-reviews-list>
                            @forelse($reviews as $review)
                                @include('partials.review-card', ['review' => $review, 'verifiedBuyerIds' => $verifiedBuyerIds])
                            @empty
                                <div class="empty-state" data-reviews-empty>
                                    Пока нет комментариев. Станьте первым читателем, который поделится впечатлением об этой книге.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
    @include('partials.site-footer')
</body>
</html>
