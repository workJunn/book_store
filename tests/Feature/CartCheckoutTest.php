<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function createBookForCart(array $overrides = []): Book
{
    $author = Author::create([
        'author_name' => 'Антон Чехов',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => fake()->unique()->company(),
    ]);

    return Book::create(array_merge([
        'book_name' => 'Палата №6',
        'price' => 450.00,
        'stock_quantity' => 5,
        'publication_date' => '1892-01-01',
        'number_of_pages' => 220,
        'average_rating' => 4.70,
        'description' => 'Повесть.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ], $overrides));
}

it('adds a book to the cart', function () {
    $book = createBookForCart();

    $response = $this->postJson("/cart/add/{$book->getKey()}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'cart_count' => 1,
        ]);

    expect(session('cart'))->toHaveKey((string) $book->getKey());
});

it('requires authentication to checkout', function () {
    $book = createBookForCart();

    $this->withSession([
        'cart' => [
            $book->getKey() => $book->toCartItem(),
        ],
    ]);

    $this->postJson('/cart/checkout')
        ->assertStatus(401)
        ->assertJson([
            'error' => true,
            'requires_auth' => true,
        ]);
});

it('creates an order from the cart and decrements stock', function () {
    $user = User::factory()->create([
        'password' => Hash::make('secret123'),
    ]);

    $book = createBookForCart([
        'stock_quantity' => 4,
        'price' => 600.00,
    ]);

    $item = $book->toCartItem();
    $item['quantity'] = 2;

    $this->actingAs($user)->withSession([
        'cart' => [
            $book->getKey() => $item,
        ],
    ]);

    $response = $this->postJson('/cart/checkout');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'cart_count' => 0,
        ]);

    $this->assertDatabaseHas('orders', [
        'id_users' => $user->getKey(),
        'status' => 'confirmed',
        'total_amount' => 1200,
    ]);

    $order = \App\Models\Order::query()->first();

    $this->assertDatabaseHas('orders_details', [
        'id_orders' => $order->getKey(),
        'id_books' => $book->getKey(),
        'quantity' => 2,
        'price_per_item' => 600,
    ]);

    expect($book->fresh()->stock_quantity)->toBe(2);
    expect(session('cart', []))->toBe([]);
});
