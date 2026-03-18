<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;

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
