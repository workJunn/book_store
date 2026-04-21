<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BookController extends Controller
{
    public function welcome()
    {
        return view('welcome', [
            'featuredBooks' => $this->getFeaturedBooks(),
            'newArrivals' => $this->buildNewArrivals(),
            'shelves' => $this->buildShelves(),
            'quickRankings' => $this->buildQuickRankings(),
        ]);
    }

    public function catalog(Request $request)
    {
        $query = Book::query()->with(['author', 'publisher', 'genres']);
        $search = trim((string) $request->string('search'));

        $periodFilter = $request->string('period')->toString();
        $isPurchaseTopPeriod = false;

        if ($periodFilter !== '') {
            $isPurchaseTopPeriod = $this->applyPeriodFilter($query, $periodFilter);
        }

        if ($request->filled('genre')) {
            $genreId = (int) $request->input('genre');

            $query->whereHas('genres', function ($genreQuery) use ($genreId) {
                $genreQuery->where('genres.id_genre', $genreId);
            });
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        if ($search !== '') {
            $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where('book_name', $operator, '%'.$search.'%');
        }

        $sort = $request->string('sort')->toString();

        if ($sort === '') {
            $sort = match ($periodFilter) {
                'new' => 'newest',
                'users' => 'rating_desc',
                default => '',
            };
        }

        if ($isPurchaseTopPeriod) {
            $query->orderByDesc('purchased_quantity')->orderBy('book_name');
        } else {
            match ($sort) {
                'price_asc' => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                'rating_desc' => $query->orderByDesc('average_rating')->orderBy('book_name'),
                'newest' => $query->orderByDesc('publication_date')->orderBy('book_name'),
                default => $query->orderBy('book_name'),
            };
        }

        $books = $query
            ->paginate(12)
            ->withQueryString();
        $genres = Genre::query()->orderBy('genre_name')->get();

        return view('catalog', [
            'books' => $books,
            'foundBooksCount' => $books->total(),
            'genres' => $genres,
            'quickRankings' => $this->buildQuickRankings(),
            'periodMeta' => $this->getPeriodMeta($periodFilter),
            'filters' => [
                'search' => $search,
                'genre' => $request->input('genre'),
                'sort' => $sort,
                'in_stock' => $request->boolean('in_stock'),
                'period' => $periodFilter,
            ],
        ]);
    }

    public function favorites()
    {
        return view('favorites');
    }

    public function search(Request $request)
    {
        $search = trim((string) $request->string('search'));

        if ($search === '') {
            return redirect()
                ->back()
                ->with('search_error', 'Введите название книги для поиска.');
        }

        $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';

        $book = Book::query()
            ->where('book_name', $operator, $search)
            ->orWhere('book_name', $operator, '%'.$search.'%')
            ->orderByRaw(
                'CASE WHEN '.($operator === 'ilike' ? 'LOWER(book_name) = LOWER(?)' : 'book_name = ?').' THEN 0 ELSE 1 END',
                [$search]
            )
            ->orderBy('book_name')
            ->first();

        if (! $book) {
            return redirect()
                ->back()
                ->with('search_error', 'Такой книги нет.')
                ->withInput(['search' => $search]);
        }

        return redirect()->route('books.show', $book);
    }

    public function show($id)
    {
        $reviewSort = request()->string('review_sort')->toString() ?: 'newest';
        $book = Book::with([
            'author',
            'publisher',
            'genres',
            'reviews' => fn ($query) => $query->with('user'),
        ])->withCount('reviews')->findOrFail($id);

        $verifiedBuyerIds = $this->getVerifiedBuyerIds($book);
        $reviews = $this->sortReviews($book->reviews, $reviewSort)->values();
        $userReview = Auth::check()
            ? $reviews->firstWhere('id_users', Auth::id())
            : null;

        return view('books.show', [
            'book' => $book,
            'userReview' => $userReview,
            'reviews' => $reviews,
            'reviewSort' => $reviewSort,
            'verifiedBuyerIds' => $verifiedBuyerIds,
        ]);
    }

    public function storeReview(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review_text' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.required' => 'Выберите оценку книги.',
            'rating.between' => 'Оценка должна быть от 1 до 5.',
            'review_text.max' => 'Комментарий не должен превышать 2000 символов.',
        ]);

        Review::updateOrCreate(
            [
                'id_books' => $book->getKey(),
                'id_users' => Auth::id(),
            ],
            [
                'rating' => $validated['rating'],
                'review_text' => trim((string) ($validated['review_text'] ?? '')) ?: null,
                'review_date' => now(),
            ]
        );

        $book->update([
            'average_rating' => round((float) $book->reviews()->avg('rating'), 2),
        ]);

        return redirect()
            ->route('books.show', $book)
            ->with('status', 'Ваш отзыв сохранен.');
    }

    private function buildShelves(): Collection
    {
        return collect([
            [
                'title' => 'Читаем всей семьей',
                'description' => 'Добрые истории, приключения и книги, которые удобно читать вместе.',
                'books' => $this->fetchShelfBooks(function (Builder $query): void {
                    $query->whereHas('genres', function (Builder $genreQuery): void {
                        $this->applyGenreKeywordFilter($genreQuery, ['дет', 'сказ', 'приключ', 'сем']);
                    })
                        ->orderByDesc('average_rating')
                        ->orderBy('book_name');
                }),
            ],
            [
                'title' => 'Книги для школы',
                'description' => 'Романы, рассказы и тексты, которые часто встречаются в школьных списках.',
                'books' => $this->fetchShelfBooks(function (Builder $query): void {
                    $query->where(function (Builder $nestedQuery): void {
                        $nestedQuery->whereHas('genres', function (Builder $genreQuery): void {
                            $this->applyGenreKeywordFilter($genreQuery, ['роман', 'поэз', 'драм', 'истор']);
                        })
                            ->orWhereDate('publication_date', '<', '2005-01-01');
                    })
                        ->orderByDesc('average_rating')
                        ->orderBy('book_name');
                }),
            ],
            [
                'title' => 'Классика',
                'description' => 'Проверенные временем книги с высокой оценкой читателей.',
                'books' => $this->fetchShelfBooks(function (Builder $query): void {
                    $query->where(function (Builder $nestedQuery): void {
                        $nestedQuery->whereDate('publication_date', '<', '1990-01-01')
                            ->orWhere('average_rating', '>=', 4.5);
                    })
                        ->orderBy('publication_date')
                        ->orderBy('book_name');
                }),
            ],
            [
                'title' => 'Новые открытия',
                'description' => 'Более свежие книги, которые стоит посмотреть после классики.',
                'books' => $this->fetchShelfBooks(function (Builder $query): void {
                    $query->whereDate('publication_date', '>=', '2005-01-01')
                        ->orderByDesc('publication_date')
                        ->orderBy('book_name');
                }, function (Builder $query): void {
                    $query->orderByDesc('publication_date')
                        ->orderBy('book_name');
                }),
            ],
        ])->filter(fn (array $shelf) => $shelf['books']->isNotEmpty())->values();
    }

    private function buildNewArrivals(): Collection
    {
        return $this->baseBookQuery()
            ->orderByDesc('publication_date')
            ->orderBy('book_name')
            ->limit(10)
            ->get();
    }

    private function buildQuickRankings(): Collection
    {
        return collect([
            [
                'period' => 'year',
                'title' => 'Топ года',
                'description' => 'Топ 10 книг',
            ],
            [
                'period' => 'month',
                'title' => 'Топ месяца',
                'description' => 'Топ 10 книг',
            ],
            [
                'period' => 'week',
                'title' => 'Топ недели',
                'description' => 'Топ 10 книг',
            ],
            [
                'period' => 'new',
                'title' => 'Новинки',
                'description' => 'Топ 10 книг',
            ],
            [
                'period' => 'users',
                'title' => 'Рейтинг',
                'description' => 'Топ 10 книг',
            ],
        ]);
    }

    private function applyPeriodFilter(Builder $query, string $periodFilter): bool
    {
        if (in_array($periodFilter, ['year', 'month', 'week'], true)) {
            $this->applyPurchaseTopFilter($query, $periodFilter);

            return true;
        }

        match ($periodFilter) {
            'new' => $query->whereBetween('publication_date', $this->resolveNewReleasesRange()),
            'preorder' => $this->applyPreorderFilter($query),
            default => null,
        };

        return false;
    }

    private function baseBookQuery(): Builder
    {
        return Book::query()->with(['author', 'publisher', 'genres']);
    }

    private function getFeaturedBooks(): Collection
    {
        return $this->baseBookQuery()
            ->orderByDesc('average_rating')
            ->orderBy('book_name')
            ->limit(2)
            ->get();
    }

    private function fetchShelfBooks(callable $constraint, ?callable $fallbackOrder = null, int $limit = 10): Collection
    {
        $query = $this->baseBookQuery();
        $constraint($query);
        $books = $query->limit($limit)->get();

        if ($books->isNotEmpty()) {
            return $books;
        }

        $fallbackQuery = $this->baseBookQuery();
        ($fallbackOrder ?? fn (Builder $builder) => $this->applyDefaultShelfFallback($builder))($fallbackQuery);

        return $fallbackQuery->limit($limit)->get();
    }

    private function applyDefaultShelfFallback(Builder $query): void
    {
        $query->orderByDesc('average_rating')
            ->orderBy('book_name');
    }

    private function applyGenreKeywordFilter(Builder $query, array $keywords): void
    {
        $query->where(function (Builder $nestedQuery) use ($keywords): void {
            foreach ($keywords as $index => $keyword) {
                $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                $nestedQuery->{$method}('LOWER(genre_name) LIKE ?', ['%' . mb_strtolower($keyword) . '%']);
            }
        });
    }

    private function getPeriodMeta(string $periodFilter): array
    {
        return match ($periodFilter) {
            'year' => [
                'title' => 'Лучшие книги за год',
                'description' => 'Подборка книг за прошлый календарный год.',
            ],
            'month' => [
                'title' => 'Лучшие книги за месяц',
                'description' => 'Подборка книг текущего месяца.',
            ],
            'week' => [
                'title' => 'Лучшие книги за неделю',
                'description' => 'Подборка книг за последние 7 дней.',
            ],
            'new' => [
                'title' => 'Новинки сайта',
                'description' => 'Книги, выпущенные за последний год.',
            ],
            'preorder' => [
                'title' => 'Предзаказы книг',
                'description' => 'Книги, которые доступны для предзаказа.',
            ],
            'users' => [
                'title' => 'Рейтинг',
                'description' => 'Рейтинг книг на основе оценок пользователей.',
            ],
            default => [
                'title' => 'Каталог книг',
                'description' => 'Все книги магазина с фильтрами и сортировкой.',
            ],
        };
    }

    private function resolveNewReleasesRange(): array
    {
        $endDate = now();

        return [$endDate->copy()->subYear(), $endDate];
    }

    private function applyPurchaseTopFilter(Builder $query, string $periodFilter): void
    {
        [$startDate, $endDate] = $this->resolvePurchaseTopRange($periodFilter);

        $purchaseStats = DB::table('orders_details')
            ->join('orders', 'orders.id_orders', '=', 'orders_details.id_orders')
            ->select('orders_details.id_books')
            ->selectRaw('SUM(orders_details.quantity) as purchased_quantity')
            ->where('orders.status', 'Оплачен')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy('orders_details.id_books');

        $query
            ->joinSub($purchaseStats, 'purchase_stats', function ($join) {
                $join->on('purchase_stats.id_books', '=', 'books.id_books');
            })
            ->select('books.*')
            ->selectRaw('purchase_stats.purchased_quantity as purchased_quantity');
    }

    private function resolvePurchaseTopRange(string $periodFilter): array
    {
        $endDate = now();

        return match ($periodFilter) {
            'year' => [$endDate->copy()->subYear(), $endDate],
            'month' => [$endDate->copy()->subMonth(), $endDate],
            'week' => [$endDate->copy()->subWeek(), $endDate],
            default => [$endDate->copy()->subWeek(), $endDate],
        };
    }

    private function applyPreorderFilter($query): void
    {
        if (! $this->hasPreorderColumn()) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->where('is_preorder', true);
    }

    private function hasPreorderColumn(): bool
    {
        static $hasPreorderColumn;

        if ($hasPreorderColumn !== null) {
            return $hasPreorderColumn;
        }

        return $hasPreorderColumn = Schema::hasColumn('books', 'is_preorder');
    }

    private function sortReviews(EloquentCollection $reviews, string $reviewSort): EloquentCollection
    {
        return match ($reviewSort) {
            'oldest' => $reviews->sortBy(fn (Review $review) => optional($review->review_date)->timestamp ?? 0),
            'rating_desc' => $reviews->sortByDesc(fn (Review $review) => [$review->rating, optional($review->review_date)->timestamp ?? 0]),
            'rating_asc' => $reviews->sortBy(fn (Review $review) => [$review->rating, optional($review->review_date)->timestamp ?? 0]),
            default => $reviews->sortByDesc(fn (Review $review) => optional($review->review_date)->timestamp ?? 0),
        };
    }

    private function getVerifiedBuyerIds(Book $book): Collection
    {
        return DB::table('orders_details')
            ->join('orders', 'orders.id_orders', '=', 'orders_details.id_orders')
            ->where('orders_details.id_books', $book->getKey())
            ->where('orders.status', 'Оплачен')
            ->pluck('orders.id_users')
            ->unique()
            ->values();
    }

}
