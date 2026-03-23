<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторы - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg">
            <section class="section-head section-head--admin">
                <div>
                    <h1 class="section-title">Авторы</h1>
                </div>
                @include('partials.admin-search')
                @include('partials.admin-nav')
            </section>

            <section class="stack-md">
                @forelse($authors as $author)
                    <article class="info-box stack-sm">
                        <div class="order-line">
                            <a href="{{ route('admin.authors.show', $author) }}" class="text-link">{{ $author->author_name }}</a>
                            <span></span>
                            <span>Книг автора: {{ $author->books_count }}</span>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        Авторов пока нет.
                    </div>
                @endforelse
            </section>
        </section>
    </main>
</body>
</html>
