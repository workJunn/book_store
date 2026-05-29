<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Order;
use App\Models\Publisher;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeTestPngUpload(string $filename = 'cover.png'): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'cover_');

    file_put_contents(
        $path,
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9sX6lzQAAAAASUVORK5CYII=')
    );

    return new UploadedFile($path, $filename, 'image/png', null, true);
}

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

    $response = $this->actingAs($admin)->get(route('admin.index'));

    $response
        ->assertOk()
        ->assertSee('Админ панель')
        ->assertSee(route('admin.index'), false)
        ->assertSee(route('admin.books.index'), false)
        ->assertSee(route('admin.partner-applications.index'), false)
        ->assertSee('На главную')
        ->assertSee(route('home'), false)
        ->assertSee(route('admin.search'), false)
        ->assertSee('Поиск: пользователи, авторы, книги')
        ->assertDontSee('Последние заказы')
        ->assertDontSee('Быстрый обзор последних заказов в системе.')
        ->assertDontSee('Профиль')
        ->assertDontSee('Партнеры')
        ->assertDontSee('Назад')
        ->assertSee('Выйти')
        ->assertSee(route('logout'), false);

    $content = $response->getContent();

    expect(strpos($content, '>Админ панель</a>'))
        ->toBeLessThan(strpos($content, '>Пользователи</a>'))
        ->and(strpos($content, '>Пользователи</a>'))
        ->toBeLessThan(strpos($content, '>Заказы</a>'))
        ->and(strpos($content, '>Заказы</a>'))
        ->toBeLessThan(strpos($content, '>Авторы</a>'))
        ->and(strpos($content, '>Авторы</a>'))
        ->toBeLessThan(strpos($content, '>Книги</a>'))
        ->and(strpos($content, '>Книги</a>'))
        ->toBeLessThan(strpos($content, '>Партнерские заявки</a>'));

    $this->actingAs($user)->get(route('admin.index'))
        ->assertForbidden();
});

it('does not show customer dashboard sections to admin users', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
        'phone_number' => '+79990000001',
        'balance' => 0,
        'registration_date' => '2026-03-23 21:54:00',
        'created_at' => '2026-03-23 18:54:00',
        'updated_at' => '2026-03-23 18:54:00',
    ]);

    $this->actingAs($admin)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Админ панель')
        ->assertSee(route('admin.index'), false)
        ->assertDontSee('Телефон')
        ->assertDontSee('+79990000001')
        ->assertDontSee('Баланс')
        ->assertDontSee('0.00 ₽')
        ->assertDontSee('Пополнить')
        ->assertDontSee('Дата регистрации')
        ->assertDontSee('23.03.2026 21:54')
        ->assertDontSee('Последнее обновление')
        ->assertDontSee('23.03.2026 18:54')
        ->assertDontSee('Быстрые действия')
        ->assertDontSee('Управление оплатой, партнерской программой и личным кабинетом автора.')
        ->assertDontSee('Партнерская программа')
        ->assertDontSee('Мои заказы')
        ->assertDontSee('Здесь отображаются оформленные и оплаченные заказы.')
        ->assertDontSee('У вас пока нет заказов.');
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
        'status' => 'Ожидает оплаты',
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

    $authorBook = Book::create([
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

    $otherAuthor = Author::create([
        'author_name' => 'Федор Достоевский',
    ]);

    Book::create([
        'book_name' => 'Идиот',
        'price' => 780,
        'stock_quantity' => 3,
        'publication_date' => '1869-01-01',
        'number_of_pages' => 520,
        'average_rating' => 4.7,
        'description' => 'Роман другого автора.',
        'id_author' => $otherAuthor->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->actingAs($admin)->get(route('admin.authors.show', $author))
        ->assertOk()
        ->assertSee('Лев Толстой')
        ->assertDontSee('Имя автора')
        ->assertDontSee('Биография')
        ->assertDontSee('Русский писатель и мыслитель.')
        ->assertSee('Количество книг:')
        ->assertSee('data-admin-books-count', false)
        ->assertSee('Анна Каренина')
        ->assertSee(route('admin.books.edit', $authorBook), false)
        ->assertSee(route('admin.books.destroy', $authorBook), false)
        ->assertSee('Редактировать')
        ->assertSee('Удалить')
        ->assertDontSee('Идиот');
});

it('allows admin to delete an author without deleting their books', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $authorRole = Role::create([
        'role_name' => 'author',
    ]);

    $userRole = Role::create([
        'role_name' => 'user',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    $authorUser = User::factory()->create([
        'id_role' => $authorRole->getKey(),
    ]);

    $application = \App\Models\PartnerApplication::create([
        'id_users' => $authorUser->getKey(),
        'pen_name' => 'Удаляемый автор',
        'biography' => 'Биография.',
        'experience_summary' => 'Опыт.',
        'payment_method' => 'card',
        'status' => 'approved',
        'processed_at' => now(),
    ]);

    $author = Author::create([
        'id_users' => $authorUser->getKey(),
        'author_name' => 'Удаляемый автор',
        'biography' => 'Биография.',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Сохранённый издатель',
    ]);

    $book = Book::create([
        'book_name' => 'Книга удаляемого автора',
        'price' => 500,
        'discount_percent' => 0,
        'stock_quantity' => 2,
        'publication_date' => '2024-05-01',
        'number_of_pages' => 180,
        'description' => 'Книга должна остаться.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->actingAs($admin)->get(route('admin.authors.show', $author))
        ->assertOk()
        ->assertSee('Удалить автора');

    $this->actingAs($admin)->delete(route('admin.authors.destroy', $author))
        ->assertRedirect(route('admin.authors.index'))
        ->assertSessionHas('status', 'Автор удалён из системы.');

    $this->assertDatabaseMissing('authors', [
        'id_author' => $author->getKey(),
    ]);

    $this->assertDatabaseHas('books', [
        'id_books' => $book->getKey(),
        'id_author' => null,
    ]);

    expect((int) $authorUser->fresh()->id_role)->toBe((int) $userRole->getKey());

    $this->assertDatabaseHas('partner_applications', [
        'id_partner_application' => $application->getKey(),
        'status' => 'removed',
    ]);

    $this->actingAs($authorUser)->post(route('partner.program.apply'), [
        'pen_name' => 'Удаляемый автор снова',
        'biography' => 'Новая биография.',
    ])->assertRedirect(route('partner.program'));

    $this->assertDatabaseHas('partner_applications', [
        'id_users' => $authorUser->getKey(),
        'pen_name' => 'Удаляемый автор снова',
        'status' => 'pending',
    ]);
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
        'discount_percent' => 10,
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
        'discount_percent' => 15,
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

it('allows admin to update books from any author', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    $firstAuthor = Author::create([
        'author_name' => 'Автор Первый',
    ]);

    $secondAuthor = Author::create([
        'author_name' => 'Автор Второй',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Общий издатель',
    ]);

    $book = Book::create([
        'book_name' => 'Книга второго автора',
        'price' => 400,
        'discount_percent' => 0,
        'stock_quantity' => 5,
        'publication_date' => '2024-01-01',
        'number_of_pages' => 210,
        'description' => 'Исходное описание.',
        'id_author' => $secondAuthor->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->actingAs($admin)->put(route('admin.books.update', $book), [
        'book_name' => 'Книга второго автора обновлена',
        'price' => 450,
        'discount_percent' => 5,
        'stock_quantity' => 8,
        'publication_date' => '2024-01-01',
        'number_of_pages' => 210,
        'description' => 'Админ обновил книгу независимо от автора.',
        'id_author' => $firstAuthor->getKey(),
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('admin.books.index'));

    $this->assertDatabaseHas('books', [
        'id_books' => $book->getKey(),
        'book_name' => 'Книга второго автора обновлена',
        'price' => 450,
        'stock_quantity' => 8,
        'id_author' => $firstAuthor->getKey(),
    ]);
});

it('allows admin to upload a cover image for a book', function () {
    Storage::fake('public');

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

    $cover = makeTestPngUpload('dead-souls.png');

    $this->actingAs($admin)->post(route('admin.books.store'), [
        'book_name' => 'Мертвые души',
        'cover' => $cover,
        'price' => 650,
        'discount_percent' => 10,
        'stock_quantity' => 7,
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Поэма в прозе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('admin.books.index'));

    $book = Book::query()->where('book_name', 'Мертвые души')->firstOrFail();

    expect($book->cover_image)->not->toBeNull();
    Storage::disk('public')->assertExists($book->cover_image);
});

it('allows admin to upload and remove a digital book file', function () {
    Storage::fake('local');

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

    $bookFile = UploadedFile::fake()->create('dead-souls.pdf', 32, 'application/pdf');

    $this->actingAs($admin)->post(route('admin.books.store'), [
        'book_name' => 'Мертвые души',
        'book_file' => $bookFile,
        'price' => 650,
        'discount_percent' => 10,
        'stock_quantity' => 7,
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Поэма в прозе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('admin.books.index'));

    $book = Book::query()->where('book_name', 'Мертвые души')->firstOrFail();

    expect($book->digital_file_path)->not->toBeNull();
    expect($book->digital_file_original_name)->toBe('dead-souls.pdf');
    Storage::disk('local')->assertExists($book->digital_file_path);

    $oldFilePath = $book->digital_file_path;

    $this->actingAs($admin)->put(route('admin.books.update', $book), [
        'book_name' => 'Мертвые души',
        'remove_book_file' => '1',
        'price' => 650,
        'discount_percent' => 10,
        'stock_quantity' => 7,
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Поэма в прозе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('admin.books.index'));

    $book->refresh();

    expect($book->digital_file_path)->toBeNull();
    expect($book->digital_file_original_name)->toBeNull();
    Storage::disk('local')->assertMissing($oldFilePath);
});

it('shows a clear validation error when admin uploads a too large digital book file', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'id_role' => $adminRole->getKey(),
    ]);

    $author = Author::create([
        'author_name' => 'Лев Толстой',
    ]);

    $publisher = Publisher::create([
        'publisher_name' => 'Русская классика',
    ]);

    $largeBookFile = UploadedFile::fake()->create('anna-karenina.pdf', 6144, 'application/pdf');

    $this->actingAs($admin)->post(route('admin.books.store'), [
        'book_name' => 'Анна Каренина',
        'book_file' => $largeBookFile,
        'price' => 850,
        'discount_percent' => 0,
        'stock_quantity' => 4,
        'publication_date' => '1877-01-01',
        'number_of_pages' => 640,
        'description' => 'Роман.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ])->assertSessionHasErrors('book_file');
});

it('allows admin to remove the preorder flag from a book', function () {
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

    $book = Book::create([
        'book_name' => 'Мертвые души',
        'price' => 650,
        'discount_percent' => 10,
        'stock_quantity' => 7,
        'is_preorder' => true,
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Поэма в прозе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->actingAs($admin)->put(route('admin.books.update', $book), [
        'book_name' => 'Мертвые души',
        'price' => 650,
        'discount_percent' => 10,
        'stock_quantity' => 7,
        'is_preorder' => '0',
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Поэма в прозе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ])->assertRedirect(route('admin.books.index'));

    expect($book->fresh()->is_preorder)->toBeFalse();
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
        'status' => 'Ожидает оплаты',
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
        ->assertSee('Ожидает оплаты');
});

it('allows admin to delete a book', function () {
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

    $book = Book::create([
        'book_name' => 'Мертвые души',
        'price' => 650,
        'discount_percent' => 10,
        'stock_quantity' => 7,
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Поэма в прозе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $this->actingAs($admin)->get(route('admin.books.index'))
        ->assertOk()
        ->assertSee('Удалить')
        ->assertSee('data-admin-book-card', false)
        ->assertSee('data-admin-book-delete-form', false);

    $this->actingAs($admin)->deleteJson(route('admin.books.destroy', $book))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Книга удалена.',
            'book_id' => $book->getKey(),
        ]);

    $this->assertDatabaseMissing('books', [
        'id_books' => $book->getKey(),
    ]);
});

it('allows admin to delete a book that exists in customer orders', function () {
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

    $book = Book::create([
        'book_name' => 'Мертвые души',
        'price' => 650,
        'discount_percent' => 10,
        'stock_quantity' => 7,
        'publication_date' => '1842-01-01',
        'number_of_pages' => 320,
        'description' => 'Поэма в прозе.',
        'id_author' => $author->getKey(),
        'id_publishers' => $publisher->getKey(),
    ]);

    $user = User::factory()->create();

    $order = Order::create([
        'id_users' => $user->getKey(),
        'status' => 'Оплачен',
        'total_amount' => 650,
    ]);

    $order->details()->create([
        'id_books' => $book->getKey(),
        'quantity' => 1,
        'price_per_item' => 650,
    ]);

    $this->actingAs($admin)->delete(route('admin.books.destroy', $book))
        ->assertRedirect(route('admin.books.index'))
        ->assertSessionHas('status', 'Книга удалена.');

    $this->assertDatabaseMissing('books', [
        'id_books' => $book->getKey(),
    ]);

    $this->assertDatabaseHas('orders', [
        'id_orders' => $order->getKey(),
        'status' => 'Оплачен',
    ]);

    $this->assertDatabaseMissing('orders_details', [
        'id_orders' => $order->getKey(),
        'id_books' => $book->getKey(),
    ]);
});
