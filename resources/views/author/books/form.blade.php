<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $formTitle }} - Панель автора</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-home-url="{{ route('catalog') }}">
    @include('partials.site-header', ['showAuthButtons' => false])

    <main class="site-main">
        <section class="container">
            <section class="auth-card stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">{{ $formTitle }}</h1>
                        <p class="section-text">Авторская карточка книги. Здесь можно менять цену, скидку и остальные основные данные.</p>
                    </div>
                    <a href="{{ route('author.index') }}" class="btn btn-secondary">Назад</a>
                </div>

                <form method="POST" action="{{ $formAction }}" class="stack-md" enctype="multipart/form-data">
                    @csrf
                    @if($book->exists)
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label for="book_name">Название книги</label>
                        <input id="book_name" name="book_name" type="text" value="{{ old('book_name', $book->book_name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="cover">Обложка книги</label>
                        <input id="cover" name="cover" type="file" accept="image/png,image/jpeg,image/webp,image/jpg">
                        @error('cover')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        @error('remove_cover_image')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        @if($book->cover_image)
                            <div class="stack-sm">
                                <img src="{{ $book->cover_image_url }}" alt="{{ $book->book_name }}" class="book-image">
                                <label class="checkbox-row">
                                    <input type="checkbox" name="remove_cover_image" value="1" @checked(old('remove_cover_image'))>
                                    <span>Удалить текущую обложку</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="book_file">Файл книги</label>
                        <input id="book_file" name="book_file" type="file" accept=".pdf,.epub,.fb2,.txt">
                        @error('book_file')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        @error('remove_book_file')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        @if($book->digital_file_path)
                            <div class="stack-sm">
                                <div class="muted">Загружен файл: {{ $book->digital_file_original_name }}</div>
                                <label class="checkbox-row">
                                    <input type="checkbox" name="remove_book_file" value="1" @checked(old('remove_book_file'))>
                                    <span>Удалить текущий файл книги</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="price">Цена</label>
                        <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $book->price) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="discount_percent">Скидка, %</label>
                        <input id="discount_percent" name="discount_percent" type="number" min="0" max="95" value="{{ old('discount_percent', $book->discount_percent ?? 0) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="stock_quantity">Остаток</label>
                        <input id="stock_quantity" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $book->stock_quantity) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="publication_date">Дата публикации</label>
                        <input id="publication_date" name="publication_date" type="date" value="{{ old('publication_date', optional($book->publication_date)->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group">
                        <label for="number_of_pages">Количество страниц</label>
                        <input id="number_of_pages" name="number_of_pages" type="number" min="1" value="{{ old('number_of_pages', $book->number_of_pages) }}" required>
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

    @include('partials.site-footer')
</body>
</html>
