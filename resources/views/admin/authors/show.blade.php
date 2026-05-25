<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Автор - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg">
            <section class="section-head">
                <div>
                    <h1 class="section-title">{{ $author->author_name }}</h1>
                    <p class="section-text">Подробные данные автора.</p>
                </div>
                <a href="{{ route('admin.authors.index') }}" class="btn btn-secondary">Назад</a>
            </section>

            @if($partnerApplication)
                <section class="stack-md">
                    <div class="info-box stack-md">
                        <div class="simple-grid simple-grid--2">
                            <div class="stack-sm">
                                <div><strong>Email:</strong> {{ $partnerApplication->user?->email ?? $author->user?->email ?? 'Email не указан' }}</div>
                                <div><strong>Статус:</strong> {{ $partnerApplication->status === 'approved' ? 'Подтверждена' : 'Ожидает подтверждения' }}</div>
                                <div><strong>Выплаты:</strong> {{ match($partnerApplication->payment_method) {
                                    'card' => 'Карта',
                                    'sbp' => 'СБП',
                                    default => 'QR-код',
                                } }}</div>
                                <div><strong>Дата заявки:</strong> {{ $partnerApplication->created_at?->format('d.m.Y H:i') }}</div>
                                @if($partnerApplication->processed_at)
                                    <div><strong>Подтверждена:</strong> {{ $partnerApplication->processed_at->format('d.m.Y H:i') }}</div>
                                @endif
                            </div>
                            <div class="stack-sm">
                                @if($partnerApplication->experience_summary)
                                    <div><strong>Опыт:</strong> {{ $partnerApplication->experience_summary }}</div>
                                @endif
                                @if($partnerApplication->portfolio_url)
                                    <div><strong>Портфолио:</strong> <a href="{{ $partnerApplication->portfolio_url }}" class="text-link">{{ $partnerApplication->portfolio_url }}</a></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <section class="stack-md">
                <div class="section-head">
                    <h2 class="subheading">Книги автора</h2>
                    <span class="muted">Количество книг: {{ $books->count() }}</span>
                </div>

                @if($books->isNotEmpty())
                    <div class="stack-md">
                        @foreach($books as $book)
                            <article class="info-box stack-sm">
                                <div class="section-head">
                                    <div class="stack-sm">
                                        <a href="{{ route('admin.books.edit', $book) }}" class="text-link">{{ $book->book_name }}</a>
                                        <div class="muted">
                                            {{ number_format((float) $book->price, 0, '.', ' ') }} ₽ ·
                                            {{ $book->publisher->publisher_name ?? 'Издатель не указан' }}
                                        </div>
                                        <div class="muted">
                                            {{ $book->genres->pluck('genre_name')->join(', ') ?: 'Жанры не указаны' }}
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-secondary">Редактировать</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        У этого автора пока нет книг.
                    </div>
                @endif
            </section>

            <section class="stack-md">
                <button type="button" class="btn btn-danger" data-open-author-delete>Удалить автора</button>
            </section>
        </section>

        <div class="checkout-modal" id="delete-author-modal" role="dialog" aria-modal="true" aria-labelledby="delete-author-title" aria-hidden="true">
            <div class="checkout-dialog info-box stack-md" tabindex="-1">
                <h2 class="section-title" id="delete-author-title">Удалить автора?</h2>
                <p class="section-text">Вы точно хотите удалить автора {{ $author->author_name }} из системы?</p>
                <div class="actions">
                    <button type="button" class="btn btn-secondary" data-close-author-delete>Нет</button>
                    <form method="POST" action="{{ route('admin.authors.destroy', $author) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Да</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
</body>
</html>
