<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Order;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Support\Carbon;

it('filters catalog by genre', function () {
    $author = Author::create([
        'author_name' => 'Федор Достоевский',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Эксмо',
    ]);

    $novel = Genre::create([
        'genre_name' => 'Роман',
    ]);

    $detective = Genre::create([
        'genre_name' => 'Детектив',
    ]);

    $bookInGenre = Book::create([
        'book_name' => 'Преступление и наказание',
        'price' => 799.00,
        'stock_quantity' => 4,
        'publication_date' => '1866-01-01',
        'number_of_pages' => 640,
        'average_rating' => 4.8,
        'description' => 'Классический роман.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $bookOtherGenre = Book::create([
        'book_name' => 'Шерлок Холмс',
        'price' => 650.00,
        'stock_quantity' => 2,
        'publication_date' => '1892-01-01',
        'number_of_pages' => 320,
        'average_rating' => 4.5,
        'description' => 'Детективная история.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $bookInGenre->genres()->attach($novel->getKey());
    $bookOtherGenre->genres()->attach($detective->getKey());

    $response = $this->get('/catalog?genre=' . $novel->getKey());

    $response->assertOk();
    $response->assertSee('Преступление и наказание');
    $response->assertDontSee('Шерлок Холмс');
});

it('filters catalog by search and stock', function () {
    $author = Author::create([
        'author_name' => 'Лев Толстой',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'АСТ',
    ]);

    Book::create([
        'book_name' => 'Война и мир',
        'price' => 1100.00,
        'stock_quantity' => 0,
        'publication_date' => '1869-01-01',
        'number_of_pages' => 1225,
        'average_rating' => 4.9,
        'description' => 'Исторический роман.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    Book::create([
        'book_name' => 'Мир глазами читателя',
        'price' => 500.00,
        'stock_quantity' => 5,
        'publication_date' => '2020-01-01',
        'number_of_pages' => 210,
        'average_rating' => 4.1,
        'description' => 'Современная проза.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $response = $this->get('/catalog?search=Мир&in_stock=1');

    $response->assertOk();
    $response->assertSee('Мир глазами читателя');
    $response->assertDontSee('Война и мир');
});

it('shows quick rankings on the welcome page and links them to catalog periods', function () {
    Carbon::setTestNow('2026-03-23 12:00:00');

    $author = Author::create([
        'author_name' => 'Джордж Оруэлл',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Penguin Books',
    ]);

    Book::create([
        'book_name' => '1984',
        'price' => 700.00,
        'stock_quantity' => 5,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 328,
        'average_rating' => 4.9,
        'description' => 'Антиутопия.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    Book::create([
        'book_name' => 'Скотный двор',
        'price' => 550.00,
        'stock_quantity' => 3,
        'publication_date' => '2025-05-10',
        'number_of_pages' => 180,
        'average_rating' => 4.8,
        'description' => 'Политическая сатира.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Топ года');
    $response->assertSee('Топ месяца');
    $response->assertSee('Топ недели');
    $response->assertSee('Новинки');
    $response->assertSee('Рейтинг');
    $response->assertSee('1984');
    $response->assertSee('Скотный двор');

    Carbon::setTestNow();
});

it('shows exactly the latest ten books on the welcome page new arrivals shelf', function () {
    Carbon::setTestNow('2026-03-23 12:00:00');

    $author = Author::create([
        'author_name' => 'Фрэнк Герберт',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Orbit',
    ]);

    foreach (range(1, 11) as $index) {
        Book::create([
            'book_name' => sprintf('Книга ленты %02d', $index),
            'price' => 500 + $index,
            'stock_quantity' => 5,
            'publication_date' => now()->subDays(12 - $index)->toDateString(),
            'number_of_pages' => 200 + $index,
            'average_rating' => 4.0 + ($index / 100),
            'description' => 'Тестовая книга для ленты новинок.',
            'id_author' => $author->getKey(),
            'id_publishers' => $publisher->getKey(),
        ]);
    }

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSeeInOrder([
        'Книга ленты 11',
        'Книга ленты 10',
        'Книга ленты 09',
        'Книга ленты 08',
        'Книга ленты 07',
        'Книга ленты 06',
        'Книга ленты 05',
        'Книга ленты 04',
        'Книга ленты 03',
        'Книга ленты 02',
    ]);
    $response->assertDontSee('Книга ленты 01');

    Carbon::setTestNow();
});

it('builds weekly top books in catalog based on paid purchases and hides the old period text block', function () {
    Carbon::setTestNow('2026-03-23 12:00:00');

    $author = Author::create([
        'author_name' => 'Рэй Брэдбери',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Эксмо',
    ]);

    $firstBook = Book::create([
        'book_name' => '451 градус по Фаренгейту',
        'price' => 700.00,
        'stock_quantity' => 10,
        'publication_date' => '1953-01-01',
        'number_of_pages' => 256,
        'average_rating' => 4.7,
        'description' => 'Антиутопия.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $secondBook = Book::create([
        'book_name' => 'Марсианские хроники',
        'price' => 680.00,
        'stock_quantity' => 10,
        'publication_date' => '1950-01-01',
        'number_of_pages' => 320,
        'average_rating' => 4.6,
        'description' => 'Фантастика.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $thirdBook = Book::create([
        'book_name' => 'Вино из одуванчиков',
        'price' => 650.00,
        'stock_quantity' => 10,
        'publication_date' => '1957-01-01',
        'number_of_pages' => 280,
        'average_rating' => 4.8,
        'description' => 'Роман.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $user = User::factory()->create();

    $weeklyTopOrder = Order::create([
        'id_users' => $user->getKey(),
        'order_date' => now()->subDays(2),
        'status' => 'Оплачен',
        'total_amount' => 2780,
    ]);

    $weeklyTopOrder->details()->create([
        'id_books' => $secondBook->getKey(),
        'quantity' => 3,
        'price_per_item' => 680,
    ]);

    $weeklyTopOrder->details()->create([
        'id_books' => $firstBook->getKey(),
        'quantity' => 1,
        'price_per_item' => 700,
    ]);

    $oldOrder = Order::create([
        'id_users' => $user->getKey(),
        'order_date' => now()->subDays(20),
        'status' => 'Оплачен',
        'total_amount' => 2600,
    ]);

    $oldOrder->details()->create([
        'id_books' => $thirdBook->getKey(),
        'quantity' => 4,
        'price_per_item' => 650,
    ]);

    $response = $this->get('/catalog?period=week');

    $response->assertOk();
    $response->assertSeeInOrder([
        'Марсианские хроники',
        '451 градус по Фаренгейту',
    ]);
    $response->assertDontSee('Вино из одуванчиков');
    $response->assertDontSee('Лучшие книги за год');
    $response->assertDontSee('Подборка книг за прошлый календарный год.');

    Carbon::setTestNow();
});

it('shows new books in the rating section sorted by newest first', function () {
    Carbon::setTestNow('2026-03-23 12:00:00');

    $author = Author::create([
        'author_name' => 'Аркадий Стругацкий',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'АСТ',
    ]);

    Book::create([
        'book_name' => 'Книга старше',
        'price' => 500.00,
        'stock_quantity' => 5,
        'publication_date' => '2026-03-18',
        'number_of_pages' => 240,
        'average_rating' => 4.2,
        'description' => 'Первая книга.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    Book::create([
        'book_name' => 'Книга новее',
        'price' => 600.00,
        'stock_quantity' => 5,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'average_rating' => 4.1,
        'description' => 'Вторая книга.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $response = $this->get('/catalog?period=new');

    $response->assertOk();
    $response->assertSeeInOrder([
        'Книга новее',
        'Книга старше',
    ]);

    Carbon::setTestNow();
});

it('shows all books released during the last year in the new section', function () {
    Carbon::setTestNow('2026-03-23 12:00:00');

    $author = Author::create([
        'author_name' => 'Айзек Азимов',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Азбука',
    ]);

    Book::create([
        'book_name' => 'Книга старше года',
        'price' => 500.00,
        'stock_quantity' => 5,
        'publication_date' => '2025-03-20',
        'number_of_pages' => 240,
        'average_rating' => 4.2,
        'description' => 'Первая книга.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    Book::create([
        'book_name' => 'Книга за последний год',
        'price' => 600.00,
        'stock_quantity' => 5,
        'publication_date' => '2025-09-19',
        'number_of_pages' => 280,
        'average_rating' => 4.1,
        'description' => 'Вторая книга.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $response = $this->get('/catalog?period=new');

    $response->assertOk();
    $response->assertSee('Книга за последний год');
    $response->assertDontSee('Книга старше года');

    Carbon::setTestNow();
});

it('shows rating books sorted from higher rating to lower', function () {
    $author = Author::create([
        'author_name' => 'Стивен Кинг',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Эксмо',
    ]);

    Book::create([
        'book_name' => 'Книга с низким рейтингом',
        'price' => 500.00,
        'stock_quantity' => 5,
        'publication_date' => '2020-01-01',
        'number_of_pages' => 240,
        'average_rating' => 3.8,
        'description' => 'Первая книга.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    Book::create([
        'book_name' => 'Книга с высоким рейтингом',
        'price' => 600.00,
        'stock_quantity' => 5,
        'publication_date' => '2020-02-01',
        'number_of_pages' => 280,
        'average_rating' => 4.9,
        'description' => 'Вторая книга.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $response = $this->get('/catalog?period=users');

    $response->assertOk();
    $response->assertSeeInOrder([
        'Книга с высоким рейтингом',
        'Книга с низким рейтингом',
    ]);
});

it('redirects header search to the matching book page', function () {
    $author = Author::create([
        'author_name' => 'Эрих Мария Ремарк',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'АСТ',
    ]);

    $book = Book::create([
        'book_name' => 'Три товарища',
        'price' => 780.00,
        'stock_quantity' => 3,
        'publication_date' => '1936-01-01',
        'number_of_pages' => 480,
        'average_rating' => 4.9,
        'description' => 'Роман о дружбе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->get('/search?search=Три товарища')
        ->assertRedirect(route('books.show', $book));
});

it('shows a small notice when header search does not find a book', function () {
    $response = $this->from('/catalog')->get('/search?search=Несуществующая книга');

    $response->assertRedirect('/catalog');

    $this->followRedirects($response)
        ->assertSee('Такой книги нет.');
});
