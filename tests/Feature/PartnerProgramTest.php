<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\PartnerApplication;
use App\Models\Publisher;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makePartnerCoverUpload(string $filename = 'cover.png'): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'cover_');

    file_put_contents(
        $path,
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9sX6lzQAAAAASUVORK5CYII=')
    );

    return new UploadedFile($path, $filename, 'image/png', null, true);
}

it('shows partner application form on a separate page', function () {
    $user = User::factory()->create();

    $this->get(route('partner.program'))
        ->assertOk()
        ->assertSee(route('partner.program.apply.form'), false)
        ->assertDontSee('name="pen_name"', false)
        ->assertDontSee('name="biography"', false);

    $this->actingAs($user)->get(route('partner.program.apply.form'))
        ->assertOk()
        ->assertSee('Имя автора или псевдоним')
        ->assertSee('Краткая биография')
        ->assertDontSee('Предпочтительный способ выплат')
        ->assertDontSee('QR-кодом')
        ->assertSee('Отправить заявку');
});

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
    ])->assertRedirect(route('partner.program'));

    $this->assertDatabaseHas('partner_applications', [
        'id_users' => $user->getKey(),
        'pen_name' => 'Марина Соколова',
        'payment_method' => 'card',
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

    $this->actingAs($admin)->get(route('admin.authors.index'))
        ->assertOk()
        ->assertSee('Партнерские заявки')
        ->assertSee(route('admin.partner-applications.index'), false)
        ->assertDontSee('Партнерский автор')
        ->assertDontSee('Автор современной прозы.')
        ->assertDontSee('Публикуюсь с 2020 года.')
        ->assertDontSee('Принять');

    $this->actingAs($admin)->get(route('admin.partner-applications.index'))
        ->assertOk()
        ->assertSee('Партнерский автор')
        ->assertSee($user->email)
        ->assertSee('Ожидает подтверждения')
        ->assertSee(route('admin.partner-applications.show', $application), false)
        ->assertDontSee('Автор современной прозы.')
        ->assertDontSee('Публикуюсь с 2020 года.')
        ->assertDontSee('Принять');

    $this->actingAs($admin)->get(route('admin.partner-applications.show', $application))
        ->assertOk()
        ->assertSee('Партнерский автор')
        ->assertSee($user->email)
        ->assertSee('Автор современной прозы.')
        ->assertSee('Публикуюсь с 2020 года.')
        ->assertSee('Карта')
        ->assertSee('Принять');

    $this->actingAs($admin)->postJson(route('admin.partner-applications.approve', $application))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 'approved',
            'status_label' => 'Подтверждена',
        ]);

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

    $this->actingAs($admin)->get(route('admin.partner-applications.index'))
        ->assertOk()
        ->assertDontSee('Партнерский автор')
        ->assertDontSee($user->email)
        ->assertDontSee('Подтверждена');

    $author = Author::query()->where('id_users', $user->getKey())->firstOrFail();

    $this->actingAs($admin)->get(route('admin.authors.index'))
        ->assertOk()
        ->assertSee('Партнерский автор')
        ->assertDontSee('Партнёрская заявка')
        ->assertDontSee('выплаты: Карта')
        ->assertSee('Партнерские заявки')
        ->assertSee(route('admin.partner-applications.index'), false)
        ->assertDontSee('Автор современной прозы.')
        ->assertDontSee('Публикуюсь с 2020 года.');

    $authorPageResponse = $this->actingAs($admin)->get(route('admin.authors.show', $author));

    $authorPageResponse
        ->assertOk()
        ->assertSee('Партнерский автор')
        ->assertDontSee('Данные партнёра')
        ->assertDontSee('Имя автора')
        ->assertDontSee('Биография')
        ->assertDontSee('Автор современной прозы.')
        ->assertSee('Публикуюсь с 2020 года.')
        ->assertSee('Карта');

    expect(strpos($authorPageResponse->getContent(), 'Карта'))
        ->toBeLessThan(strpos($authorPageResponse->getContent(), 'Книги автора'));

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Панель автора');

    $this->actingAs($user)->get(route('author.index'))
        ->assertOk()
        ->assertSee('Панель автора')
        ->assertSee('Партнерский автор');
});

it('does not remove admin role when approving an admins partner application', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    $application = PartnerApplication::create([
        'id_users' => $admin->getKey(),
        'pen_name' => 'Администратор',
        'biography' => 'Администратор магазина.',
        'experience_summary' => 'Управляет каталогом.',
        'payment_method' => 'card',
        'status' => 'pending',
    ]);

    $this->actingAs($admin)->postJson(route('admin.partner-applications.approve', $application))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 'approved',
        ]);

    $admin->refresh()->load('role');

    expect($admin->role?->role_name)->toBe('admin');

    $this->actingAs($admin)->get(route('admin.index'))
        ->assertOk()
        ->assertSee('Админ панель');

    $this->assertDatabaseHas('authors', [
        'id_users' => $admin->getKey(),
        'author_name' => 'Администратор',
    ]);
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

it('allows an author to delete their own book', function () {
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

    $book = Book::create([
        'book_name' => 'Новая книга',
        'price' => 900,
        'discount_percent' => 15,
        'stock_quantity' => 12,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Описание книги.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->actingAs($user)->get(route('author.index'))
        ->assertOk()
        ->assertSee('Удалить');

    $this->actingAs($user)->delete(route('author.books.destroy', $book))
        ->assertRedirect(route('author.index'))
        ->assertSessionHas('status', 'Книга удалена.');

    $this->assertDatabaseMissing('books', [
        'id_books' => $book->getKey(),
    ]);
});

it('prevents an author from deleting another author book', function () {
    $authorRole = Role::create([
        'role_name' => 'author',
    ]);

    $user = User::factory()->create([
        'id_role' => $authorRole->getKey(),
    ]);

    $owner = User::factory()->create([
        'id_role' => $authorRole->getKey(),
    ]);

    Author::create([
        'id_users' => $user->getKey(),
        'author_name' => 'Анна Автор',
        'biography' => 'Биография автора.',
    ]);

    $otherAuthor = Author::create([
        'id_users' => $owner->getKey(),
        'author_name' => 'Другой Автор',
        'biography' => 'Другая биография.',
    ]);

    $book = Book::create([
        'book_name' => 'Чужая книга',
        'price' => 900,
        'discount_percent' => 15,
        'stock_quantity' => 12,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Описание книги.',
        'id_author' => $otherAuthor->getKey(),
    ]);

    $this->actingAs($user)->delete(route('author.books.destroy', $book))
        ->assertForbidden();

    $this->assertDatabaseHas('books', [
        'id_books' => $book->getKey(),
    ]);
});

it('allows an approved author to create a book without a publisher', function () {
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

    $this->actingAs($user)->post(route('author.books.store'), [
        'book_name' => 'Книга без издательства',
        'price' => 750,
        'discount_percent' => 5,
        'stock_quantity' => 8,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 220,
        'description' => 'Описание книги.',
        'id_publishers' => '',
    ])->assertRedirect(route('author.index'));

    $this->assertDatabaseHas('books', [
        'book_name' => 'Книга без издательства',
        'id_author' => $author->getKey(),
        'id_publishers' => null,
    ]);
});

it('allows an approved author to upload a digital book file from the same form', function () {
    Storage::fake('local');

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
        'book_name' => 'Электронная книга',
        'book_file' => UploadedFile::fake()->create('book.pdf', 32, 'application/pdf'),
        'price' => 900,
        'discount_percent' => 15,
        'stock_quantity' => 12,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Описание книги.',
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('author.index'));

    $book = Book::query()->where('book_name', 'Электронная книга')->firstOrFail();

    expect($book->digital_file_path)->not->toBeNull();
    expect($book->digital_file_original_name)->toBe('book.pdf');
    Storage::disk('local')->assertExists($book->digital_file_path);
});

it('shows validation errors under the author book form fields', function () {
    $authorRole = Role::create([
        'role_name' => 'author',
    ]);

    $user = User::factory()->create([
        'id_role' => $authorRole->getKey(),
    ]);

    Author::create([
        'id_users' => $user->getKey(),
        'author_name' => 'Анна Автор',
        'biography' => 'Биография автора.',
    ]);

    $response = $this->actingAs($user)
        ->from(route('author.books.create'))
        ->post(route('author.books.store'), [
            'book_name' => '',
            'price' => -1,
            'discount_percent' => 120,
            'stock_quantity' => -3,
            'publication_date' => 'not-a-date',
            'number_of_pages' => 0,
        ]);

    $response->assertRedirect(route('author.books.create'));

    $this->followRedirects($response)
        ->assertSee('Укажите название книги.')
        ->assertSee('Цена не может быть отрицательной.')
        ->assertSee('Скидка должна быть от 0 до 95%.')
        ->assertSee('Количество не может быть отрицательным.')
        ->assertSee('Укажите корректную дату публикации.')
        ->assertSee('Количество страниц должно быть больше нуля.')
        ->assertSee('form-group error', false);
});

it('allows an author to remove an uploaded cover from the edit page', function () {
    Storage::fake('public');

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

    $book = Book::create([
        'book_name' => 'Новая книга',
        'cover_image' => makePartnerCoverUpload('cover.png')->store('books', 'public'),
        'price' => 900,
        'discount_percent' => 15,
        'stock_quantity' => 12,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Описание книги.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $oldCoverPath = $book->cover_image;
    Storage::disk('public')->assertExists($oldCoverPath);

    $this->actingAs($user)->put(route('author.books.update', $book), [
        'book_name' => 'Новая книга',
        'remove_cover_image' => '1',
        'price' => 990,
        'discount_percent' => 25,
        'stock_quantity' => 9,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Обновленное описание книги.',
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('author.index'));

    expect($book->fresh()->cover_image)->toBeNull();
    Storage::disk('public')->assertMissing($oldCoverPath);
});

it('allows an author to remove the preorder flag from the edit page', function () {
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

    $book = Book::create([
        'book_name' => 'Новая книга',
        'price' => 900,
        'discount_percent' => 15,
        'stock_quantity' => 12,
        'is_preorder' => true,
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Описание книги.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->actingAs($user)->put(route('author.books.update', $book), [
        'book_name' => 'Новая книга',
        'price' => 990,
        'discount_percent' => 25,
        'stock_quantity' => 9,
        'is_preorder' => '0',
        'publication_date' => '2026-03-20',
        'number_of_pages' => 280,
        'description' => 'Обновленное описание книги.',
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('author.index'));

    expect($book->fresh()->is_preorder)->toBeFalse();
});
