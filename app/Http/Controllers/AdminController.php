<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Order;
use App\Models\PartnerApplication;
use App\Models\Publisher;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $latestOrders = Order::query()
            ->with('user')
            ->orderByDesc('id_orders')
            ->take(5)
            ->get();

        return view('admin.index', [
            'usersCount' => User::query()->count(),
            'booksCount' => Book::query()->count(),
            'ordersCount' => Order::query()->count(),
            'paidOrdersCount' => Order::query()->where('status', 'Оплачен')->count(),
            'partnerApplicationsCount' => PartnerApplication::query()->where('status', 'pending')->count(),
            'latestOrders' => $latestOrders,
        ]);
    }

    public function authors()
    {
        return view('admin.authors.index', [
            'authors' => Author::query()
                ->withCount('books')
                ->orderBy('author_name')
                ->get(),
        ]);
    }

    public function users()
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->leftJoin('roles', 'roles.id_role', '=', 'users.id_role')
                ->with('role')
                ->orderByRaw("CASE WHEN roles.role_name = 'admin' THEN 0 ELSE 1 END")
                ->orderBy('users.name')
                ->select('users.*')
                ->get(),
        ]);
    }

    public function partnerApplications()
    {
        return view('admin.partner-applications.index', [
            'applications' => PartnerApplication::query()
                ->with('user')
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function approvePartnerApplication(PartnerApplication $application)
    {
        if ($application->status === 'approved') {
            return redirect()
                ->route('admin.partner-applications.index')
                ->with('status', 'Заявка уже была подтверждена ранее.');
        }

        DB::transaction(function () use ($application) {
            $authorRole = Role::query()->firstOrCreate([
                'role_name' => 'author',
            ]);

            $application->user()->update([
                'id_role' => $authorRole->getKey(),
            ]);

            Author::query()->updateOrCreate(
                ['id_users' => $application->id_users],
                [
                    'author_name' => $application->pen_name,
                    'biography' => $application->biography,
                ]
            );

            $application->update([
                'status' => 'approved',
                'processed_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.partner-applications.index')
            ->with('status', 'Заявка подтверждена. Пользователь переведен в роль автора.');
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->string('q'));
        $keywords = $this->extractKeywords($query);
        $showOrders = $query !== '' && Str::startsWith(mb_strtolower($query), 'заказ');
        $orderKeywords = $showOrders ? $this->extractKeywords(trim((string) preg_replace('/^заказ\s*/ui', '', $query))) : [];

        return view('admin.search.index', [
            'query' => $query,
            'showOrders' => $showOrders,
            'users' => $query === ''
                ? collect()
                : $this->applyKeywordSearch(
                    User::query()
                    ->leftJoin('roles', 'roles.id_role', '=', 'users.id_role')
                    ->with('role')
                    ->select('users.*'),
                    $keywords,
                    ['users.name', 'users.email']
                )
                    ->orderByRaw("CASE WHEN roles.role_name = 'admin' THEN 0 ELSE 1 END")
                    ->orderBy('users.name')
                    ->get(),
            'authors' => $query === ''
                ? collect()
                : $this->applyKeywordSearch(
                    Author::query()->withCount('books'),
                    $keywords,
                    ['author_name', 'biography']
                )
                    ->orderBy('author_name')
                    ->get(),
            'books' => $query === ''
                ? collect()
                : $this->applyBookKeywordSearch(
                    Book::query()->with('author'),
                    $keywords
                )
                    ->orderBy('book_name')
                    ->get(),
            'orders' => ! $showOrders
                ? collect()
                : $this->applyOrderKeywordSearch(
                    Order::query()->with('user'),
                    $orderKeywords
                )
                    ->orderByDesc('id_orders')
                    ->get(),
        ]);
    }

    public function showUser(User $user)
    {
        $user->load(['role', 'orders' => fn ($query) => $query->orderByDesc('id_orders')]);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function destroyUser(User $user)
    {
        if ((int) $user->getKey() === (int) auth()->id()) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('status', 'Нельзя удалить текущего администратора.');
        }

        DB::transaction(function () use ($user) {
            $orderIds = $user->orders()->pluck('id_orders');

            if ($orderIds->isNotEmpty()) {
                DB::table('orders_details')->whereIn('id_orders', $orderIds)->delete();
                DB::table('orders')->whereIn('id_orders', $orderIds)->delete();
            }

            Review::query()->where('id_users', $user->getKey())->delete();
            DB::table('sessions')->where('user_id', $user->getKey())->delete();
            $user->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Пользователь удалён.');
    }

    public function showAuthor(Author $author)
    {
        $author->load(['books.publisher', 'books.genres']);

        return view('admin.authors.show', [
            'author' => $author,
            'books' => $author->books->sortBy('book_name')->values(),
        ]);
    }

    public function books()
    {
        return view('admin.books.index', [
            'books' => Book::query()
                ->with(['author', 'publisher', 'genres'])
                ->orderBy('book_name')
                ->get(),
        ]);
    }

    public function createBook()
    {
        return view('admin.books.form', [
            'book' => new Book(),
            'authors' => Author::query()->orderBy('author_name')->get(),
            'publishers' => Publisher::query()->orderBy('publisher_name')->get(),
            'genres' => Genre::query()->orderBy('genre_name')->get(),
            'selectedGenres' => collect(),
            'formAction' => route('admin.books.store'),
            'formTitle' => 'Добавить книгу',
            'backUrl' => route('admin.authors.index'),
        ]);
    }

    public function storeBook(Request $request)
    {
        $validated = $this->validateBook($request);
        $validated['cover_image'] = $this->storeCoverImage($request);

        $book = Book::create($validated);
        $book->genres()->sync($request->input('genre_ids', []));

        return redirect()
            ->route('admin.books.index')
            ->with('status', 'Книга добавлена.');
    }

    public function editBook(Book $book)
    {
        $book->load('genres');

        return view('admin.books.form', [
            'book' => $book,
            'authors' => Author::query()->orderBy('author_name')->get(),
            'publishers' => Publisher::query()->orderBy('publisher_name')->get(),
            'genres' => Genre::query()->orderBy('genre_name')->get(),
            'selectedGenres' => $book->genres->pluck('id_genre'),
            'formAction' => route('admin.books.update', $book),
            'formTitle' => 'Редактировать книгу',
            'backUrl' => $book->id_author
                ? route('admin.authors.show', $book->id_author)
                : route('admin.authors.index'),
        ]);
    }

    public function updateBook(Request $request, Book $book)
    {
        $validated = $this->validateBook($request);
        $validated['cover_image'] = $this->resolveCoverImagePath($request, $book);

        $book->update($validated);
        $book->genres()->sync($request->input('genre_ids', []));

        return redirect()
            ->route('admin.books.index')
            ->with('status', 'Книга обновлена.');
    }

    public function destroyBook(Book $book)
    {
        $this->deleteCoverImage($book->cover_image);
        $this->deleteDigitalBookFile($book->digital_file_path);
        $book->genres()->detach();
        $book->delete();

        return redirect()
            ->route('admin.books.index')
            ->with('status', 'Книга удалена.');
    }

    public function orders()
    {
        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with('user')
                ->orderByDesc('id_orders')
                ->get(),
        ]);
    }

    public function showOrder(Order $order)
    {
        $order->load(['user', 'details.book.author']);

        return view('admin.orders.show', [
            'order' => $order,
            'orderDetails' => $order->details,
            'user' => $order->user,
        ]);
    }

    private function validateBook(Request $request): array
    {
        return $request->validate([
            'book_name' => ['required', 'string', 'max:200'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['required', 'integer', 'between:0,95'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'publication_date' => ['nullable', 'date'],
            'number_of_pages' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'id_author' => ['nullable', 'exists:authors,id_author'],
            'id_publishers' => ['nullable', 'exists:publishers,id_publishers'],
            'is_preorder' => ['nullable', 'boolean'],
            'genre_ids' => ['nullable', 'array'],
            'genre_ids.*' => ['exists:genres,id_genre'],
        ]);
    }

    private function storeCoverImage(Request $request): ?string
    {
        if (! $request->hasFile('cover')) {
            return null;
        }

        return $request->file('cover')->store('books', 'public');
    }

    private function resolveCoverImagePath(Request $request, Book $book): ?string
    {
        if ($request->boolean('remove_cover_image')) {
            $this->deleteCoverImage($book->cover_image);

            return null;
        }

        if (! $request->hasFile('cover')) {
            return $book->cover_image;
        }

        $newCoverImagePath = $this->storeCoverImage($request);
        $this->deleteCoverImage($book->cover_image);

        return $newCoverImagePath;
    }

    private function deleteCoverImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function deleteDigitalBookFile(?string $path): void
    {
        if ($path) {
            Storage::disk('local')->delete($path);
        }
    }

    private function extractKeywords(string $query): array
    {
        return array_values(array_filter(preg_split('/\s+/u', $query) ?: []));
    }

    private function applyKeywordSearch(Builder $builder, array $keywords, array $columns): Builder
    {
        foreach ($keywords as $keyword) {
            $builder->where(function (Builder $nestedBuilder) use ($columns, $keyword) {
                foreach ($columns as $index => $column) {
                    if ($index === 0) {
                        $nestedBuilder->where($column, 'like', '%'.$keyword.'%');
                    } else {
                        $nestedBuilder->orWhere($column, 'like', '%'.$keyword.'%');
                    }
                }
            });
        }

        return $builder;
    }

    private function applyOrderKeywordSearch(Builder $builder, array $keywords): Builder
    {
        if ($keywords === []) {
            return $builder;
        }

        foreach ($keywords as $keyword) {
            $builder->where(function (Builder $nestedBuilder) use ($keyword) {
                $nestedBuilder
                    ->where('id_orders', 'like', '%'.$keyword.'%')
                    ->orWhere('status', 'like', '%'.$keyword.'%')
                    ->orWhereHas('user', function (Builder $userBuilder) use ($keyword) {
                        $userBuilder
                            ->where('name', 'like', '%'.$keyword.'%')
                            ->orWhere('email', 'like', '%'.$keyword.'%');
                    });
            });
        }

        return $builder;
    }

    private function applyBookKeywordSearch(Builder $builder, array $keywords): Builder
    {
        foreach ($keywords as $keyword) {
            $builder->where(function (Builder $nestedBuilder) use ($keyword) {
                $nestedBuilder
                    ->where('book_name', 'like', '%'.$keyword.'%')
                    ->orWhere('description', 'like', '%'.$keyword.'%')
                    ->orWhereHas('author', function (Builder $authorBuilder) use ($keyword) {
                        $authorBuilder->where('author_name', 'like', '%'.$keyword.'%');
                    });
            });
        }

        return $builder;
    }
}
