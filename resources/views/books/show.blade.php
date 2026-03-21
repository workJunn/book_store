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
                    <span class="bookmark-icon" aria-hidden="true"></span>
                    <span class="cart-count" data-favorites-count>0</span>
                </a>

                <a href="{{ route('cart.index') }}" class="cart-link" title="Корзина">
                    🛒
                    <span class="cart-count" data-cart-count>
                        {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                    </span>
                </a>

                @guest
                    <a href="{{ route('User_login') }}" class="btn btn-secondary">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                @endguest

                @auth
                    <a href="{{ route('dashboard') }}" class="profile-link">👤</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="site-main">
        <div class="container container-wide">
            <div class="book-page-detail">
                <section class="book-hero page-panel">
                    <a href="{{ route('catalog') }}" class="back-link">← Назад в каталог</a>

                    @php
                        $rating = round((float) ($book->average_rating ?? 0));
                        $reviewWord = $book->reviews_count === 1 ? 'отзыв' : (($book->reviews_count >= 2 && $book->reviews_count <= 4) ? 'отзыва' : 'отзывов');
                    @endphp

                    <div class="book-layout">
                        <div class="book-cover-card">
                            <img
                                src="https://via.placeholder.com/500x700/667eea/ffffff?text={{ urlencode($book->book_name) }}"
                                alt="{{ $book->book_name }}"
                                class="book-image"
                            >

                            <div class="book-badges">
                                <span class="book-badge">Книга</span>
                                @foreach($book->genres as $genre)
                                    <span class="book-badge book-badge--soft">{{ $genre->genre_name }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="book-main">
                            <div class="book-main__top">
                                <div>
                                    <div class="book-category">Подробная страница книги</div>
                                    <h1 class="book-title">{{ $book->book_name }}</h1>
                                    <p class="book-author">Автор: {{ $book->author->author_name ?? 'Не указан' }}</p>
                                </div>

                                <div class="book-price-block">
                                    <span class="book-price">{{ number_format((float) $book->price, 2, '.', ' ') }} ₽</span>
                                    <span class="book-price-note">Цена за один экземпляр</span>
                                </div>
                            </div>

                            <div class="book-rating-panel">
                                <div class="book-rating">
                                    <span class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $rating ? '★' : '☆' }}
                                        @endfor
                                    </span>
                                    <span class="rating-count">{{ number_format((float) $book->average_rating, 2, '.', ' ') }} / 5</span>
                                </div>
                                <div class="book-rating-summary">Основано на {{ $book->reviews_count }} {{ $reviewWord }}</div>
                            </div>

                            <div class="book-stats-grid">
                                <div class="book-stat-card">
                                    <span class="book-stat-card__label">Издатель</span>
                                    <span class="book-stat-card__value">{{ $book->publisher->publisher_name ?? 'Не указан' }}</span>
                                </div>
                                <div class="book-stat-card book-stat-card--primary">
                                    <span class="book-stat-card__label">Дата публикации</span>
                                    <span class="book-stat-card__value">{{ $book->publication_date ? $book->publication_date->format('d.m.Y') : 'Не указана' }}</span>
                                </div>
                                <div class="book-stat-card">
                                    <span class="book-stat-card__label">Количество страниц</span>
                                    <span class="book-stat-card__value">{{ $book->number_of_pages }}</span>
                                </div>
                                <div class="book-stat-card">
                                    <span class="book-stat-card__label">Жанры</span>
                                    <span class="book-stat-card__value">
                                        {{ $book->genres->isNotEmpty() ? $book->genres->pluck('genre_name')->join(', ') : 'Не указаны' }}
                                    </span>
                                </div>
                            </div>

                            <div class="book-description-card">
                                <h2>О книге</h2>
                                <p>{{ $book->description ?? 'Описание отсутствует.' }}</p>
                            </div>

                            @if($book->author?->biography)
                                <div class="book-description-card">
                                    <h2>Об авторе</h2>
                                    <p>{{ $book->author->biography }}</p>
                                </div>
                            @endif

                            <div class="actions">
                                <button
                                    type="button"
                                    class="favorite-button favorite-button--inline favorite-button--storefront"
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

                                @if($book->stock_quantity > 0)
                                    <button type="button" class="btn btn-cart-outline" data-add-to-cart="{{ $book->getKey() }}">
                                        В корзину
                                    </button>
                                @else
                                    <button class="btn btn-cart-outline" type="button" disabled>Нет в наличии</button>
                                @endif

                                <a href="{{ route('catalog') }}" class="btn btn-secondary">Вернуться в каталог</a>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="book-reviews-layout page-panel">
                    <div class="reviews-hub" data-reviews-hub>
                        <div class="reviews-tabs" role="tablist" aria-label="Отзывы и рецензии">
                            <button
                                class="reviews-tab is-active"
                                type="button"
                                role="tab"
                                aria-selected="true"
                                data-reviews-tab-trigger="users"
                            >
                                Отзывы читателей
                                <span class="reviews-tab__count">{{ $reviews->count() }}</span>
                            </button>

                            <button
                                class="reviews-tab"
                                type="button"
                                role="tab"
                                aria-selected="false"
                                data-reviews-tab-trigger="external"
                            >
                                Рецензии с платформы
                                <span class="reviews-tab__count">{{ $externalReviews->count() }}</span>
                            </button>
                        </div>

                        <div class="reviews-toolbar">
                            <button type="button" class="btn btn-primary" data-open-review-form>
                                Добавить отзыв
                            </button>

                            <div class="reviews-reward">
                                <div class="reviews-reward__title">Бонус за отзыв</div>
                                <div class="reviews-reward__text">
                                    Поделитесь впечатлением о книге и получите персональные рекомендации для следующих покупок.
                                </div>
                            </div>

                            <form method="GET" action="{{ route('books.show', $book) }}" class="reviews-sort">
                                <label for="review_sort">Сортировка</label>
                                <select id="review_sort" name="review_sort" onchange="this.form.submit()">
                                    <option value="newest" @selected($reviewSort === 'newest')>Сначала новые</option>
                                    <option value="oldest" @selected($reviewSort === 'oldest')>Сначала старые</option>
                                    <option value="rating_desc" @selected($reviewSort === 'rating_desc')>Высокая оценка</option>
                                    <option value="rating_asc" @selected($reviewSort === 'rating_asc')>Низкая оценка</option>
                                </select>
                            </form>
                        </div>

                        <div class="reviews-panel is-active" data-reviews-tab-panel="users">
                            <div class="book-reviews-list">
                                @forelse($reviews as $review)
                                    @php
                                        $helpfulCount = 4 + (($review->id_reviews ?? 1) % 7);
                                        $notHelpfulCount = 1 + (($review->id_reviews ?? 1) % 3);
                                    @endphp

                                    <article class="review-card review-card--detailed">
                                        <div class="review-card__top">
                                            <div>
                                                <div class="review-card__author">{{ $review->user->name ?? 'Пользователь' }}</div>
                                                <div class="review-card__meta-row">
                                                    <span class="review-card__date">{{ $review->review_date ? $review->review_date->format('d.m.Y H:i') : '' }}</span>
                                                    @if($verifiedBuyerIds->contains($review->id_users))
                                                        <span class="review-card__badge">Подтвержденная покупка</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="review-card__rating">
                                                @for($star = 1; $star <= 5; $star++)
                                                    <span class="review-card__star {{ $star <= (int) $review->rating ? 'is-filled' : '' }}">★</span>
                                                @endfor
                                            </div>
                                        </div>

                                        <div class="review-card__body">
                                            {{ $review->review_text ?: 'Пользователь оставил только оценку без текста.' }}
                                        </div>

                                        <div class="review-feedback">
                                            <span class="review-feedback__label">Отзыв был полезен?</span>
                                            <button type="button" class="review-feedback__button">Да · {{ $helpfulCount }}</button>
                                            <button type="button" class="review-feedback__button">Нет · {{ $notHelpfulCount }}</button>
                                        </div>
                                    </article>
                                @empty
                                    <div class="empty-state">
                                        Пока нет комментариев. Станьте первым читателем, который поделится впечатлением об этой книге.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="reviews-panel" data-reviews-tab-panel="external" hidden>
                            <div class="book-reviews-list">
                                @foreach($externalReviews as $review)
                                    <article class="review-card review-card--detailed">
                                        <div class="review-card__top">
                                            <div>
                                                <div class="review-card__author">{{ $review['author'] }}</div>
                                                <div class="review-card__meta-row">
                                                    <span class="review-card__date">{{ $review['date'] }}</span>
                                                    <span class="review-card__badge review-card__badge--soft">{{ $review['source'] }}</span>
                                                </div>
                                            </div>

                                            <div class="review-card__rating">
                                                @for($star = 1; $star <= 5; $star++)
                                                    <span class="review-card__star {{ $star <= (int) $review['rating'] ? 'is-filled' : '' }}">★</span>
                                                @endfor
                                            </div>
                                        </div>

                                        <div class="review-card__body">
                                            {{ $review['text'] }}
                                        </div>

                                        <div class="review-feedback">
                                            <span class="review-feedback__label">Рецензия собрана из внешнего источника.</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="book-review-form-card">
                        <div class="section-heading">
                            <h2>Оставить комментарий</h2>
                            <p>Ваша оценка в звездах участвует в расчете среднего рейтинга книги. Форма откроется только после нажатия на кнопку ниже.</p>
                        </div>

                        @if (session('status'))
                            <div class="success-box">
                                {{ session('status') }}
                            </div>
                        @endif

                        @auth
                            @php
                                $selectedRating = (int) old('rating', $userReview?->rating);
                                $shouldOpenReviewForm = $errors->has('rating') || $errors->has('review_text') || session('status');
                            @endphp

                            <details class="review-form-toggle" id="review-form-panel" @if($shouldOpenReviewForm) open @endif>
                                <summary class="btn btn-primary review-form-toggle__button">
                                    <span class="review-form-toggle__text">
                                    {{ $userReview ? 'Изменить комментарий' : 'Написать комментарий' }}
                                    </span>
                                    <span class="review-form-toggle__icon" aria-hidden="true">+</span>
                                </summary>

                                <form method="POST" action="{{ route('books.reviews.store', $book) }}" class="review-form">
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
                                        >{{ old('review_text', $userReview?->review_text) }}</textarea>
                                        @error('review_text')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        {{ $userReview ? 'Обновить отзыв' : 'Опубликовать отзыв' }}
                                    </button>
                                </form>
                            </details>
                        @else
                            <div class="book-review-login">
                                <p>Чтобы оставить комментарий, нужно войти в аккаунт.</p>
                                <a href="{{ route('User_login') }}" class="btn btn-primary">Войти</a>
                            </div>
                        @endauth
                    </div>
                </section>
            </div>
        </div>
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
