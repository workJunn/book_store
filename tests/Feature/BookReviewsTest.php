<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;

function createDetailedBook(): Book
{
    $author = Author::create([
        'author_name' => 'Михаил Булгаков',
        'biography' => 'Русский писатель, драматург и автор романов XX века.',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Азбука',
    ]);

    $genre = Genre::create([
        'genre_name' => 'Роман',
    ]);

    $book = Book::create([
        'book_name' => 'Мастер и Маргарита',
        'price' => 890.00,
        'stock_quantity' => 8,
        'publication_date' => '1967-01-01',
        'number_of_pages' => 480,
        'average_rating' => 0,
        'description' => 'Роман о добре, зле и свободе выбора.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $book->genres()->attach($genre->getKey());

    return $book;
}

it('shows detailed book page with extended information', function () {
    $book = createDetailedBook();

    $this->get(route('books.show', $book))
        ->assertOk()
        ->assertSee('Подробная страница книги')
        ->assertSee('О книге')
        ->assertSee('Об авторе')
        ->assertSee('Отзывы читателей')
        ->assertSee('Рецензии с платформы')
        ->assertDontSee('Статус')
        ->assertDontSee('Ориентир по чтению');
});

it('stores a review for a book and recalculates its average rating', function () {
    $user = User::factory()->create();
    $book = createDetailedBook();

    $response = $this->actingAs($user)->post(route('books.reviews.store', $book), [
        'rating' => 5,
        'review_text' => 'Сильный роман с очень плотной атмосферой.',
    ]);

    $response->assertRedirect(route('books.show', $book));
    $response->assertSessionHas('status');

    $this->assertDatabaseHas('reviews', [
        'id_books' => $book->getKey(),
        'id_users' => $user->getKey(),
        'rating' => 5,
        'review_text' => 'Сильный роман с очень плотной атмосферой.',
    ]);

    expect((float) $book->fresh()->average_rating)->toBe(5.0);
});

it('updates the same users review instead of creating duplicates', function () {
    $user = User::factory()->create();
    $book = createDetailedBook();

    $this->actingAs($user)->post(route('books.reviews.store', $book), [
        'rating' => 4,
        'review_text' => 'Первое впечатление.',
    ]);

    $this->actingAs($user)->post(route('books.reviews.store', $book), [
        'rating' => 3,
        'review_text' => 'После перечитывания мнение изменилось.',
    ]);

    expect($book->reviews()->count())->toBe(1);

    $this->assertDatabaseHas('reviews', [
        'id_books' => $book->getKey(),
        'id_users' => $user->getKey(),
        'rating' => 3,
        'review_text' => 'После перечитывания мнение изменилось.',
    ]);
});
