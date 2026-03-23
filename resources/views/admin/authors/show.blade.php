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

            <section class="stack-md">
                <div class="profile-info">
                    <div class="info-box">
                        <div class="info-label">Имя автора</div>
                        <div class="info-value">{{ $author->author_name }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Количество книг</div>
                        <div class="info-value">{{ $books->count() }}</div>
                    </div>
                </div>

                <div class="info-box stack-sm">
                    <div class="info-label">Биография</div>
                    <div class="info-value">{{ $author->biography ?: 'Биография не указана.' }}</div>
                </div>
            </section>

            <section class="stack-md">
                <div>
                    <h2 class="subheading">Книги автора</h2>
                </div>

                @if($books->isNotEmpty())
                    <div class="stack-md">
                        @foreach($books as $book)
                            <article class="info-box stack-sm">
                                <a href="{{ route('admin.books.edit', $book) }}" class="text-link">{{ $book->book_name }}</a>
                                <div class="muted">
                                    {{ number_format((float) $book->price, 0, '.', ' ') }} ₽ ·
                                    {{ $book->publisher->publisher_name ?? 'Издатель не указан' }}
                                </div>
                                <div class="muted">
                                    {{ $book->genres->pluck('genre_name')->join(', ') ?: 'Жанры не указаны' }}
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
        </section>
    </main>
</body>
</html>
