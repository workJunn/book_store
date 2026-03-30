<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель автора - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-home-url="{{ route('catalog') }}">
    @include('partials.site-header', ['showAuthButtons' => false])

    <main class="site-main">
        <section class="container stack-lg">
            <section class="section-head">
                <div>
                    <h1 class="section-title">Панель автора</h1>
                    <p class="section-text">{{ $author->author_name }}. Управляйте книгами, ценой и скидками в своем каталоге.</p>
                </div>
                <div class="actions">
                    <a href="{{ route('author.books.create') }}" class="btn btn-primary">Добавить книгу</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Профиль</a>
                </div>
            </section>

            @if(session('status'))
                <div class="success-box">{{ session('status') }}</div>
            @endif

            <section class="profile-info">
                <article class="info-box">
                    <div class="info-label">Автор</div>
                    <div class="info-value">{{ $author->author_name }}</div>
                </article>
                <article class="info-box">
                    <div class="info-label">Книг в каталоге</div>
                    <div class="info-value">{{ $books->count() }}</div>
                </article>
                <article class="info-box">
                    <div class="info-label">Средняя цена</div>
                    <div class="info-value">{{ number_format((float) $books->avg('price'), 0, '.', ' ') }} ₽</div>
                </article>
            </section>

            <section class="stack-md">
                <div>
                    <h2 class="subheading">Мои книги</h2>
                </div>

                @forelse($books as $book)
                    <article class="info-box stack-sm">
                        <div class="order-line">
                            <a href="{{ route('books.show', $book) }}" class="text-link">{{ $book->book_name }}</a>
                            <span>{{ number_format((float) $book->price, 0, '.', ' ') }} ₽</span>
                            <span>Скидка {{ $book->discount_percent }}%</span>
                        </div>
                        <div class="actions">
                            <a href="{{ route('author.books.edit', $book) }}" class="btn btn-secondary">Редактировать</a>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        Пока нет книг. Добавьте первую книгу в каталог автора.
                    </div>
                @endforelse
            </section>
        </section>
    </main>

    @include('partials.site-footer')
</body>
</html>
