<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Order;
use App\Models\Publisher;
use App\Models\Role;
use App\Models\User;

it('allows only admins to open the admin panel', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $userRole = Role::create([
        'role_name' => 'user',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    $user = User::factory()->create([
        'id_role' => $userRole->getKey(),
    ]);

    $this->actingAs($admin)->get(route('admin.index'))
        ->assertOk()
        ->assertSee('Админ панель');

    $this->actingAs($user)->get(route('admin.index'))
        ->assertForbidden();
});

it('shows all authors on the admin authors page', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    Author::create([
        'author_name' => 'Александр Пушкин',
        'biography' => 'Русский поэт.',
    ]);

    Author::create([
        'author_name' => 'Иван Тургенев',
    ]);

    $this->actingAs($admin)->get(route('admin.authors.index'))
        ->assertOk()
        ->assertSee('Авторы')
        ->assertSee('Александр Пушкин')
        ->assertSee('Иван Тургенев');
});

it('shows all users and allows admin to delete a user', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'name' => 'Главный админ',
        'id_role' => $adminRole->getKey(),
    ]);

    $user = User::factory()->create([
        'name' => 'Удаляемый пользователь',
        'email' => 'remove@example.com',
    ]);

    $this->actingAs($admin)->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Пользователи')
        ->assertSee('Удаляемый пользователь');

    $this->actingAs($admin)->get(route('admin.users.show', $user))
        ->assertOk()
        ->assertSee('Удаляемый пользователь')
        ->assertSee('remove@example.com')
        ->assertSee('Удалить пользователя');

    $this->actingAs($admin)->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseMissing('users', [
        'id_users' => $user->getKey(),
    ]);
});

it('shows admins first in the admin users list and searches across entities', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $userRole = Role::create([
        'role_name' => 'user',
    ]);

    $admin = User::factory()->create([
        'name' => 'Админ Первый',
        'email' => 'admin1@example.com',
        'id_role' => $adminRole->getKey(),
    ]);

    User::factory()->create([
        'name' => 'Пользователь Второй',
        'email' => 'user2@example.com',
        'id_role' => $userRole->getKey(),
    ]);

    $author = Author::create([
        'author_name' => 'Антон Чехов',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'АСТ',
    ]);

    Book::create([
        'book_name' => 'Чайка',
        'price' => 500,
        'stock_quantity' => 2,
        'publication_date' => '1896-01-01',
        'number_of_pages' => 220,
        'average_rating' => 4.4,
        'description' => 'Пьеса.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $orderUser = User::factory()->create([
        'name' => 'Читатель Заказов',
        'email' => 'orders@example.com',
        'id_role' => $userRole->getKey(),
    ]);

    $order = Order::create([
        'id_users' => $orderUser->getKey(),
        'status' => 'Оформлен',
        'total_amount' => 1200,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk();
    expect(strpos($response->getContent(), 'Админ Первый'))->toBeLessThan(strpos($response->getContent(), 'Пользователь Второй'));

    $this->actingAs($admin)->get(route('admin.search', ['q' => 'Ч']))
        ->assertOk()
        ->assertSee('Антон Чехов')
        ->assertSee('Чайка')
        ->assertDontSee('Заказ №' . $order->getKey());

    $this->actingAs($admin)->get(route('admin.search', ['q' => 'заказ ' . $order->getKey()]))
        ->assertOk()
        ->assertSee('Заказы')
        ->assertSee('Заказ №' . $order->getKey());
});

it('opens a separate admin author page with full details', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    $author = Author::create([
        'author_name' => 'Лев Толстой',
        'biography' => 'Русский писатель и мыслитель.',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Эксмо',
    ]);

    Book::create([
        'book_name' => 'Анна Каренина',
        'price' => 850,
        'stock_quantity' => 4,
        'publication_date' => '1877-01-01',
        'number_of_pages' => 640,
        'average_rating' => 4.8,
        'description' => 'Роман.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->actingAs($admin)->get(route('admin.authors.show', $author))
        ->assertOk()
        ->assertSee('Лев Толстой')
        ->assertSee('Русский писатель и мыслитель.')
        ->assertSee('Анна Каренина');
});

it('allows admin to create and update a book', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    $author = Author::create([
        'author_name' => 'Николай Гоголь',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Просвещение',
    ]);

    $this->actingAs($admin)->post(route('admin.books.store'), [
        'book_name' => 'Мертвые души',
        'price' => 650,
        'stock_quantity' => 7,
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Поэма в прозе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('admin.books.index'));

    $book = Book::query()->where('book_name', 'Мертвые души')->firstOrFail();

    $this->actingAs($admin)->put(route('admin.books.update', $book), [
        'book_name' => 'Мертвые души',
        'price' => 700,
        'stock_quantity' => 10,
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Обновленное описание.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('admin.books.index'));

    $this->assertDatabaseHas('books', [
        'id_books' => $book->getKey(),
        'price' => 700,
        'stock_quantity' => 10,
    ]);
});

it('shows a compact orders list and opens order details for admin', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $userRole = Role::create([
        'role_name' => 'user',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    $user = User::factory()->create([
        'id_role' => $userRole->getKey(),
    ]);

    $order = Order::create([
        'id_users' => $user->getKey(),
        'status' => 'Оформлен',
        'total_amount' => 900,
    ]);

    $this->actingAs($admin)->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSee('Заказ №' . $order->getKey())
        ->assertSee($user->name)
        ->assertSee($order->order_date?->format('d.m.Y H:i') ?? '');

    $this->actingAs($admin)->get(route('admin.orders.show', $order))
        ->assertOk()
        ->assertSee('Заказ №' . $order->getKey())
        ->assertSee($user->name)
        ->assertSee('Оформлен');
});
