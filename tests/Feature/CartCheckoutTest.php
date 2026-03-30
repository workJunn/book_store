<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        'balance' => 1500.00,
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
        'status' => 'Оформлен',
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
    expect((float) $user->fresh()->balance)->toBe(300.0);
    expect(session('cart', []))->toBe([]);
});

it('does not create an order when user balance is insufficient', function () {
    $user = User::factory()->create([
        'balance' => 300.00,
    ]);

    $book = createBookForCart([
        'price' => 600.00,
        'stock_quantity' => 2,
    ]);

    $item = $book->toCartItem();
    $item['quantity'] = 1;

    $response = $this->actingAs($user)->withSession([
        'cart' => [
            $book->getKey() => $item,
        ],
    ])->postJson('/cart/checkout');

    $response->assertStatus(422)
        ->assertJson([
            'error' => true,
            'message' => 'Недостаточно средств на балансе для оформления заказа.',
        ]);

    $this->assertDatabaseCount('orders', 0);
    expect((float) $user->fresh()->balance)->toBe(300.0);
    expect($book->fresh()->stock_quantity)->toBe(2);
});

it('redirects browser checkout to the payment page with order data', function () {
    $user = User::factory()->create([
        'name' => 'Иван Петров',
        'email' => 'ivan@example.com',
        'phone_number' => '+79991234567',
        'balance' => 2000.00,
    ]);

    $book = createBookForCart([
        'stock_quantity' => 3,
        'price' => 500.00,
    ]);

    $item = $book->toCartItem();
    $item['quantity'] = 2;

    $response = $this->actingAs($user)->withSession([
        'cart' => [
            $book->getKey() => $item,
        ],
    ])->post('/cart/checkout');

    $order = \App\Models\Order::query()->firstOrFail();

    $response->assertRedirect(route('orders.payment', $order));

    $this->get(route('orders.payment', $order))
        ->assertOk()
        ->assertSee('Оплата заказа')
        ->assertSee('Иван Петров')
        ->assertSee('ivan@example.com')
        ->assertSee('+79991234567')
        ->assertSee('Оформлен')
        ->assertSee('Палата №6')
        ->assertSee('Количество: 2');

    expect((float) $user->fresh()->balance)->toBe(1000.0);
});

it('marks order as paid and shows it in the user profile', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'name' => 'Мария Соколова',
        'email' => 'maria@example.com',
        'balance' => 1000.00,
    ]);

    $book = createBookForCart([
        'digital_file_path' => UploadedFile::fake()->create('book.pdf', 32, 'application/pdf')->store('books/files', 'local'),
        'digital_file_original_name' => 'book.pdf',
        'stock_quantity' => 2,
        'price' => 700.00,
    ]);

    $item = $book->toCartItem();
    $item['quantity'] = 1;

    $this->actingAs($user)->withSession([
        'cart' => [
            $book->getKey() => $item,
        ],
    ])->post('/cart/checkout');

    $order = \App\Models\Order::query()->firstOrFail();

    $response = $this->actingAs($user)->post(route('orders.pay', $order));

    $response->assertRedirect(route('orders.show', $order));
    $response->assertSessionHas('status', 'Оплата прошла успешно.');
    $response->assertSessionHas('auto_download_book_ids', [$book->getKey()]);

    $this->assertDatabaseHas('orders', [
        'id_orders' => $order->getKey(),
        'status' => 'Оплачен',
    ]);

    expect((float) $user->fresh()->balance)->toBe(300.0);

    $this->actingAs($user)->withSession([
        'auto_download_book_ids' => [$book->getKey()],
        'status' => 'Оплата прошла успешно.',
    ])->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee('Заказ №' . $order->getKey())
        ->assertSee('Оплачен')
        ->assertSee('Палата №6')
        ->assertSee('Количество: 1')
        ->assertSee('Скачать файл книги')
        ->assertSee('data-auto-download', false);
});

it('allows a buyer to download a paid digital book and blocks unpaid access', function () {
    Storage::fake('local');

    $buyer = User::factory()->create([
        'balance' => 1500.00,
    ]);

    $otherUser = User::factory()->create();

    $book = createBookForCart([
        'digital_file_path' => UploadedFile::fake()->create('story.pdf', 64, 'application/pdf')->store('books/files', 'local'),
        'digital_file_original_name' => 'story.pdf',
        'price' => 700.00,
        'stock_quantity' => 2,
    ]);

    $item = $book->toCartItem();
    $item['quantity'] = 1;

    $this->actingAs($buyer)->withSession([
        'cart' => [
            $book->getKey() => $item,
        ],
    ])->post('/cart/checkout');

    $order = \App\Models\Order::query()->firstOrFail();

    $this->actingAs($buyer)->get(route('orders.books.download', ['order' => $order, 'book' => $book]))
        ->assertForbidden();

    $this->actingAs($buyer)->post(route('orders.pay', $order));

    $this->actingAs($buyer)->get(route('orders.books.download', ['order' => $order, 'book' => $book]))
        ->assertOk()
        ->assertHeader('content-disposition');

    $this->actingAs($otherUser)->get(route('orders.books.download', ['order' => $order, 'book' => $book]))
        ->assertForbidden();
});
