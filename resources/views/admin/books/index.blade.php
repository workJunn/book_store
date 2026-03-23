<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Книги - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg">
            <section class="section-head">
                <div>
                    <h1 class="section-title">Управление книгами</h1>
                    <p class="section-text">Добавление, редактирование и удаление книг.</p>
                </div>
                <div class="actions">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Назад</a>
                    @include('partials.admin-nav')
                    <a href="{{ route('admin.books.create') }}" class="btn btn-primary">Добавить книгу</a>
                </div>
            </section>

            @if(session('status'))
                <div class="success-box">{{ session('status') }}</div>
            @endif

            <section class="stack-md">
                @foreach($books as $book)
                    <article class="info-box stack-sm">
                        <div class="section-head">
                            <div class="stack-sm">
                                <div class="info-value">{{ $book->book_name }}</div>
                                <div class="muted">
                                    {{ $book->author->author_name ?? 'Автор не указан' }} · {{ number_format((float) $book->price, 0, '.', ' ') }} ₽ · Остаток: {{ $book->stock_quantity }}
                                </div>
                            </div>
                            <div class="actions">
                                <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-secondary">Редактировать</a>
                                <form method="POST" action="{{ route('admin.books.destroy', $book) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Удалить</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        </section>
    </main>
</body>
</html>
