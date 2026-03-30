<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\PartnerApplication;
use App\Models\Publisher;
use App\Models\Role;
use App\Models\User;

it('allows a user to submit a partner application', function () {
    $userRole = Role::create([
        'role_name' => 'user',
    ]);

    $user = User::factory()->create([
        'id_role' => $userRole->getKey(),
    ]);

    $this->actingAs($user)->post(route('partner.program.apply'), [
        'pen_name' => 'Марина Соколова',
        'biography' => 'Пишу современные романы и короткую прозу.',
        'experience_summary' => '3 изданные книги.',
        'portfolio_url' => 'https://example.com/marina',
        'payment_method' => 'sbp',
    ])->assertRedirect(route('partner.program'));

    $this->assertDatabaseHas('partner_applications', [
        'id_users' => $user->getKey(),
        'pen_name' => 'Марина Соколова',
        'status' => 'pending',
    ]);
});

it('allows admin to approve a partner application and opens the author panel for the user', function () {
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
        'name' => 'Партнерский автор',
        'id_role' => $userRole->getKey(),
    ]);

    $application = PartnerApplication::create([
        'id_users' => $user->getKey(),
        'pen_name' => 'Партнерский автор',
        'biography' => 'Автор современной прозы.',
        'experience_summary' => 'Публикуюсь с 2020 года.',
        'payment_method' => 'card',
        'status' => 'pending',
    ]);

    $this->actingAs($admin)->post(route('admin.partner-applications.approve', $application))
        ->assertRedirect(route('admin.partner-applications.index'));

    $authorRole = Role::query()->where('role_name', 'author')->firstOrFail();
    $user->refresh();

    expect((int) $user->id_role)->toBe((int) $authorRole->getKey());

    $this->assertDatabaseHas('authors', [
        'id_users' => $user->getKey(),
        'author_name' => 'Партнерский автор',
    ]);

    $this->assertDatabaseHas('partner_applications', [
        'id_partner_application' => $application->getKey(),
        'status' => 'approved',
    ]);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Панель автора');

    $this->actingAs($user)->get(route('author.index'))
        ->assertOk()
        ->assertSee('Панель автора')
        ->assertSee('Партнерский автор');
});

it('allows an approved author to manage own books with discount editing', function () {
    $authorRole = Role::create([
        'role_name' => 'author',
    ]);

    $user = User::factory()->create([
        'id_role' => $authorRole->getKey(),
    ]);

    $author = Author::create([
        'id_users' => $user->getKey(),
        'author_name' => 'Анна Автор',
        'biography' => 'Биография автора.',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Новая Волна',
    ]);

    $this->actingAs($user)->post(route('author.books.store'), [
        'book_name' => 'Новая книга',
        'price' => 900,
        'discount_percent' => 15,
        'stock_quantity' => 12,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Описание книги.',
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('author.index'));

    $book = Book::query()->where('book_name', 'Новая книга')->firstOrFail();

    expect((int) $book->id_author)->toBe((int) $author->getKey());

    $this->actingAs($user)->put(route('author.books.update', $book), [
        'book_name' => 'Новая книга',
        'price' => 990,
        'discount_percent' => 25,
        'stock_quantity' => 9,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Обновленное описание книги.',
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('author.index'));

    $this->assertDatabaseHas('books', [
        'id_books' => $book->getKey(),
        'id_author' => $author->getKey(),
        'price' => 990,
        'discount_percent' => 25,
    ]);
});
