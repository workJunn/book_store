<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\Review;
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
        ->assertDontSee('Карточка книги')
        ->assertDontSee('Подробная страница книги')
        ->assertSee('О книге')
        ->assertSee('Об авторе')
        ->assertSee('Отзывы читателей')
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

it('allows the same user to create multiple reviews for one book', function () {
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

    expect($book->reviews()->count())->toBe(2);

    $this->assertDatabaseHas('reviews', [
        'id_books' => $book->getKey(),
        'id_users' => $user->getKey(),
        'rating' => 3,
        'review_text' => 'После перечитывания мнение изменилось.',
    ]);
});

it('stores a review over ajax and returns data for updating the page without reload', function () {
    $user = User::factory()->create();
    $book = createDetailedBook();

    $response = $this->actingAs($user)->postJson(route('books.reviews.store', $book), [
        'rating' => 5,
        'review_text' => '  Отличная книга для долгого чтения.  ',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Ваш отзыв сохранен.',
            'review_text' => '',
            'rating' => null,
            'submit_label' => 'Опубликовать отзыв',
            'reviews_count' => 1,
        ])
        ->assertJsonStructure([
            'review_html',
            'average_rating',
            'reviews_summary',
        ]);

    expect($response->json('review_html'))->toContain('data-review-card');
    expect($response->json('review_html'))->toContain('Отличная книга для долгого чтения.');
});

it('reminds the user to choose a rating before storing a review', function () {
    $user = User::factory()->create();
    $book = createDetailedBook();

    $this->actingAs($user)->postJson(route('books.reviews.store', $book), [
        'review_text' => 'Комментарий без оценки.',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['rating'])
        ->assertJsonPath('errors.rating.0', 'Выберите оценку книги.');

    expect($book->reviews()->count())->toBe(0);
});

it('stores real helpful votes for reviews and updates the same users vote', function () {
    $book = createDetailedBook();
    $reviewAuthor = User::factory()->create();
    $voter = User::factory()->create();

    $review = Review::create([
        'id_books' => $book->getKey(),
        'id_users' => $reviewAuthor->getKey(),
        'rating' => 4,
        'review_text' => 'Полезный отзыв.',
        'review_date' => now(),
    ]);

    $this->actingAs($voter)->postJson(route('reviews.vote', $review), [
        'vote' => 'helpful',
    ])->assertOk()
        ->assertJson([
            'success' => true,
            'helpful_count' => 1,
            'not_helpful_count' => 0,
            'user_vote' => 'helpful',
        ]);

    $this->assertDatabaseHas('review_votes', [
        'id_reviews' => $review->getKey(),
        'id_users' => $voter->getKey(),
        'is_helpful' => true,
    ]);

    $this->actingAs($voter)->postJson(route('reviews.vote', $review), [
        'vote' => 'not_helpful',
    ])->assertOk()
        ->assertJson([
            'success' => true,
            'helpful_count' => 0,
            'not_helpful_count' => 1,
            'user_vote' => 'not_helpful',
        ]);

    expect($review->votes()->count())->toBe(1);
    $this->assertDatabaseHas('review_votes', [
        'id_reviews' => $review->getKey(),
        'id_users' => $voter->getKey(),
        'is_helpful' => false,
    ]);
});
