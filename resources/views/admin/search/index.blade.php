<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg admin-layout">
            @include('partials.admin-sidebar')

            <div class="admin-layout__content stack-lg">
                @include('partials.admin-page-head', ['title' => 'Поиск'])

                @php
                    $hasResults = $users->isNotEmpty() || $authors->isNotEmpty() || $books->isNotEmpty() || ($showOrders && $orders->isNotEmpty());
                @endphp

                @if($query === '')
                    <div class="empty-state search-empty-state">
                        Введите ключевые слова для поиска по пользователям, авторам и книгам. Для заказов начните запрос со слова `заказ`.
                    </div>
                @elseif(! $hasResults)
                    <div class="empty-state search-empty-state">
                        По вашему запросу ничего не найдено.
                    </div>
                @else
                    @if($users->isNotEmpty())
                        <section class="stack-md">
                            <h2 class="subheading">Пользователи</h2>
                            <div class="stack-md">
                                @foreach($users as $user)
                                    <article class="info-box info-box--plain stack-sm">
                                        <div class="order-line">
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-link">{{ $user->name }}</a>
                                            <span>{{ $user->email }}</span>
                                            <span>{{ $user->role?->role_name ?? 'user' }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($authors->isNotEmpty())
                        <section class="stack-md">
                            <h2 class="subheading">Авторы</h2>
                            <div class="stack-md">
                                @foreach($authors as $author)
                                    <article class="info-box info-box--plain stack-sm">
                                        <div class="order-line">
                                            <a href="{{ route('admin.authors.show', $author) }}" class="text-link">{{ $author->author_name }}</a>
                                            <span></span>
                                            <span>Книг автора: {{ $author->books_count }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($books->isNotEmpty())
                        <section class="stack-md">
                            <h2 class="subheading">Книги</h2>
                            <div class="stack-md">
                                @foreach($books as $book)
                                    <article class="info-box info-box--plain stack-sm">
                                        <div class="order-line">
                                            <a href="{{ route('admin.books.edit', $book) }}" class="text-link">{{ $book->book_name }}</a>
                                            <span>{{ $book->author->author_name ?? 'Автор не указан' }}</span>
                                            <span>{{ number_format((float) $book->price, 0, '.', ' ') }} ₽</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($showOrders && $orders->isNotEmpty())
                        <section class="stack-md">
                            <h2 class="subheading">Заказы</h2>
                            <div class="stack-md">
                                @foreach($orders as $order)
                                    <article class="info-box info-box--plain stack-sm">
                                        <div class="order-line">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-link">Заказ №{{ $order->getKey() }}</a>
                                            <span>{{ $order->user->name ?? 'Пользователь' }}</span>
                                            <span>{{ $order->order_date ? $order->order_date->format('d.m.Y H:i') : 'Дата не указана' }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endif
            </div>
        </section>
    </main>
</body>
</html>
