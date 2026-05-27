<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\ReviewVote;
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
            'genreSlides' => $this->buildGenreSlides(),
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
            'reviews' => fn ($query) => $this->reviewPresentationQuery($query),
        ])->withCount('reviews')->findOrFail($id);

        $verifiedBuyerIds = $this->getVerifiedBuyerIds($book);
        $reviews = $this->sortReviews($book->reviews, $reviewSort)->values();

        return view('books.show', [
            'book' => $book,
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

        $review = Review::create([
            'id_books' => $book->getKey(),
            'id_users' => Auth::id(),
            'rating' => $validated['rating'],
            'review_text' => trim((string) ($validated['review_text'] ?? '')) ?: null,
            'review_date' => now(),
        ]);

        $book->update([
            'average_rating' => round((float) $book->reviews()->avg('rating'), 2),
        ]);

        if ($request->expectsJson()) {
            $book->refresh();
            $review->load('user');
            $reviewsCount = $book->reviews()->count();
            $verifiedBuyerIds = $this->getVerifiedBuyerIds($book);

            return response()->json([
                'success' => true,
                'message' => 'Ваш отзыв сохранен.',
                'review_html' => view('partials.review-card', [
                    'review' => $this->loadReviewForPresentation($review),
                    'verifiedBuyerIds' => $verifiedBuyerIds,
                ])->render(),
                'average_rating' => number_format((float) $book->average_rating, 2, '.', ' '),
                'rounded_rating' => round((float) $book->average_rating),
                'reviews_count' => $reviewsCount,
                'reviews_summary' => 'Основано на '.$reviewsCount.' '.$this->getReviewWord($reviewsCount),
                'review_id' => (int) $review->getKey(),
                'review_text' => '',
                'rating' => null,
                'submit_label' => 'Опубликовать отзыв',
            ]);
        }

        return redirect()
            ->route('books.show', $book)
            ->with('status', 'Ваш отзыв сохранен.');
    }

    public function voteReview(Request $request, Review $review)
    {
        $validated = $request->validate([
            'vote' => ['required', 'in:helpful,not_helpful'],
        ]);

        ReviewVote::updateOrCreate(
            [
                'id_reviews' => $review->getKey(),
                'id_users' => Auth::id(),
            ],
            [
                'is_helpful' => $validated['vote'] === 'helpful',
            ]
        );

        $review = $this->loadReviewForPresentation($review);

        return response()->json([
            'success' => true,
            'message' => 'Ваш голос учтен.',
            'review_id' => (int) $review->getKey(),
            'helpful_count' => (int) $review->helpful_count,
            'not_helpful_count' => (int) $review->not_helpful_count,
            'user_vote' => $validated['vote'],
        ]);
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

    private function buildGenreSlides(): Collection
    {
        $usedBookIds = [];

        return Genre::query()
            ->whereHas('books')
            ->with(['books' => function ($query): void {
                $query
                    ->with(['author', 'publisher', 'genres'])
                    ->orderByDesc('average_rating')
                    ->orderBy('book_name');
            }])
            ->withCount('books')
            ->orderByDesc('books_count')
            ->orderBy('genre_name')
            ->get()
            ->map(function (Genre $genre) use (&$usedBookIds) {
                $books = $genre->books
                    ->reject(fn (Book $book) => in_array((int) $book->getKey(), $usedBookIds, true))
                    ->take(2)
                    ->values();

                foreach ($books as $book) {
                    $usedBookIds[] = (int) $book->getKey();
                }

                return [
                    'genre' => $genre,
                    'title' => $genre->genre_name,
                    'description' => $this->describeGenre($genre->genre_name),
                    'books' => $books,
                ];
            })
            ->filter(fn (array $slide) => $slide['books']->isNotEmpty())
            ->take(5)
            ->values();
    }

    private function describeGenre(string $genreName): string
    {
        $name = mb_strtolower($genreName);

        if (str_contains($name, 'науч') || str_contains($name, 'science')) {
            return 'Понятные книги о знаниях, идеях и исследованиях для тех, кто любит разбираться в устройстве мира. В таких книгах важны ясные объяснения, сильные примеры и ощущение, что сложная тема становится ближе. Это хороший выбор для читателей, которые хотят не просто провести время, а узнать что-то новое.';
        }

        if (str_contains($name, 'фантаст') || str_contains($name, 'fantasy')) {
            return 'Истории о необычных мирах, технологиях, будущем и открытиях, которые расширяют границы привычного. Здесь можно встретить далёкие планеты, альтернативные реальности и героев, которым приходится принимать решения в непривычных обстоятельствах. Жанр хорошо подходит тем, кто любит масштаб, воображение и неожиданные идеи.';
        }

        if (str_contains($name, 'роман') || str_contains($name, 'fiction')) {
            return 'Большие человеческие истории о выборе, чувствах, переменах и событиях, которые остаются с читателем надолго. В центре таких книг обычно стоят отношения, личные испытания и моменты, после которых герои уже не могут оставаться прежними. Это жанр для спокойного, внимательного чтения и сильного эмоционального погружения.';
        }

        if (str_contains($name, 'детектив') || str_contains($name, 'mystery')) {
            return 'Запутанные сюжеты, скрытые мотивы и расследования, где каждая деталь может оказаться ключевой. Детективы держат внимание за счёт вопросов, подозрений и постепенного раскрытия правды. Такие книги особенно хорошо подходят, когда хочется следить за логикой событий и собирать разгадку вместе с героями.';
        }

        if (str_contains($name, 'истор') || str_contains($name, 'history')) {
            return 'Книги о событиях, эпохах и людях прошлого, помогающие увидеть знакомый мир в более широком контексте. В них важны атмосфера времени, детали быта и связь личных судеб с большими переменами. Такой жанр позволяет читать не только о фактах, но и о том, как прошлое влияет на людей.';
        }

        return 'Подборка книг жанра, в котором легко найти новое чтение под настроение и открыть для себя сильных авторов. Здесь собраны разные истории: от лёгких и быстрых до более плотных и вдумчивых. Начните с нескольких заметных книг жанра, чтобы понять его ритм и выбрать направление для следующего чтения.';
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

    private function reviewPresentationQuery($query)
    {
        return $query
            ->with('user')
            ->withCount([
                'votes as helpful_count' => fn (Builder $voteQuery) => $voteQuery->where('is_helpful', true),
                'votes as not_helpful_count' => fn (Builder $voteQuery) => $voteQuery->where('is_helpful', false),
            ])
            ->when(Auth::check(), function (Builder $reviewQuery): void {
                $reviewQuery->with([
                    'votes' => fn ($voteQuery) => $voteQuery->where('id_users', Auth::id()),
                ]);
            });
    }

    private function loadReviewForPresentation(Review $review): Review
    {
        return $this->reviewPresentationQuery(Review::query())
            ->findOrFail($review->getKey());
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

    private function getReviewWord(int $count): string
    {
        return $count === 1 ? 'отзыв' : (($count >= 2 && $count <= 4) ? 'отзыва' : 'отзывов');
    }

}
