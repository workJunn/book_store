<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $formTitle }} - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container">
            <section class="auth-card stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">{{ $formTitle }}</h1>
                        <p class="section-text">Управление карточкой книги в каталоге.</p>
                    </div>
                    <a href="{{ $backUrl }}" class="btn btn-secondary">Назад</a>
                </div>

                <form method="POST" action="{{ $formAction }}" class="stack-md">
                    @csrf
                    @if($book->exists)
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label for="book_name">Название книги</label>
                        <input id="book_name" name="book_name" type="text" value="{{ old('book_name', $book->book_name) }}">
                    </div>

                    <div class="form-group">
                        <label for="price">Цена</label>
                        <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $book->price) }}">
                    </div>

                    <div class="form-group">
                        <label for="stock_quantity">Остаток</label>
                        <input id="stock_quantity" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $book->stock_quantity) }}">
                    </div>

                    <div class="form-group">
                        <label for="publication_date">Дата публикации</label>
                        <input id="publication_date" name="publication_date" type="date" value="{{ old('publication_date', optional($book->publication_date)->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group">
                        <label for="number_of_pages">Количество страниц</label>
                        <input id="number_of_pages" name="number_of_pages" type="number" min="1" value="{{ old('number_of_pages', $book->number_of_pages) }}">
                    </div>

                    <div class="form-group">
                        <label for="id_author">Автор</label>
                        <select id="id_author" name="id_author">
                            <option value="">Не выбран</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->getKey() }}" @selected((string) old('id_author', $book->id_author) === (string) $author->getKey())>{{ $author->author_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_publishers">Издатель</label>
                        <select id="id_publishers" name="id_publishers">
                            <option value="">Не выбран</option>
                            @foreach($publishers as $publisher)
                                <option value="{{ $publisher->getKey() }}" @selected((string) old('id_publishers', $book->id_publishers) === (string) $publisher->getKey())>{{ $publisher->publisher_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="genre_ids">Жанры</label>
                        <select id="genre_ids" name="genre_ids[]" multiple>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->getKey() }}" @selected(collect(old('genre_ids', $selectedGenres))->contains($genre->getKey()))>{{ $genre->genre_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание</label>
                        <textarea id="description" name="description">{{ old('description', $book->description) }}</textarea>
                    </div>

                    <label class="checkbox-row">
                        <input type="checkbox" name="is_preorder" value="1" @checked((bool) old('is_preorder', $book->is_preorder))>
                        <span>Предзаказ</span>
                    </label>

                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </section>
        </section>
    </main>
</body>
</html>
