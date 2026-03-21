<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function welcome()
    {
        $books = Book::query()
            ->with(['author', 'publisher', 'genres'])
            ->orderByDesc('average_rating')
            ->orderBy('book_name')
            ->get();

        return view('welcome', [
            'featuredBooks' => $books->take(4)->values(),
            'shelves' => $this->buildShelves($books),
        ]);
    }

    public function catalog(Request $request)
    {
        $query = Book::query()->with(['author', 'publisher', 'genres']);
        $search = trim((string) $request->string('search'));

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
            $query->where('book_name', $operator, '%' . $search . '%');
        }

        $sort = $request->string('sort')->toString();

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating_desc' => $query->orderByDesc('average_rating')->orderBy('book_name'),
            'newest' => $query->orderByDesc('publication_date')->orderBy('book_name'),
            default => $query->orderBy('book_name'),
        };

        $books = $query->paginate(9)->withQueryString();
        $genres = Genre::query()->orderBy('genre_name')->get();

        return view('catalog', [
            'books' => $books,
            'genres' => $genres,
            'filters' => [
                'search' => $search,
                'genre' => $request->input('genre'),
                'sort' => $sort,
                'in_stock' => $request->boolean('in_stock'),
            ],
        ]);
    }

    public function favorites()
    {
        return view('favorites');
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
        $externalReviews = $this->buildExternalReviews($book);
        $userReview = Auth::check()
            ? $reviews->firstWhere('id_users', Auth::id())
            : null;

        return view('books.show', [
            'book' => $book,
            'userReview' => $userReview,
            'reviews' => $reviews,
            'externalReviews' => $externalReviews,
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

    private function buildShelves(Collection $books): Collection
    {
        $byRating = fn (Collection $items) => $items->sortByDesc(fn (Book $book) => (float) $book->average_rating)->values();
        $byNewest = fn (Collection $items) => $items->sortByDesc(fn (Book $book) => optional($book->publication_date)->timestamp ?? 0)->values();
        $byOldest = fn (Collection $items) => $items->sortBy(fn (Book $book) => optional($book->publication_date)->timestamp ?? PHP_INT_MAX)->values();

        return collect([
            [
                'title' => 'Читаем всей семьей',
                'description' => 'Добрые истории, приключения и книги, которые удобно читать вместе.',
                'books' => $this->pickShelfBooks(
                    $books,
                    fn (Book $book) => $this->bookHasGenres($book, ['дет', 'сказ', 'приключ', 'сем']),
                    $byRating
                ),
            ],
            [
                'title' => 'Книги для школы',
                'description' => 'Романы, рассказы и тексты, которые часто встречаются в школьных списках.',
                'books' => $this->pickShelfBooks(
                    $books,
                    fn (Book $book) => $this->bookHasGenres($book, ['роман', 'поэз', 'драм', 'истор'])
                        || (optional($book->publication_date)->year ?? 9999) < 2005,
                    $byRating
                ),
            ],
            [
                'title' => 'Классика',
                'description' => 'Проверенные временем книги с высокой оценкой читателей.',
                'books' => $this->pickShelfBooks(
                    $books,
                    fn (Book $book) => (optional($book->publication_date)->year ?? 9999) < 1990
                        || (float) $book->average_rating >= 4.5,
                    $byOldest
                ),
            ],
            [
                'title' => 'Новые открытия',
                'description' => 'Более свежие книги, которые стоит посмотреть после классики.',
                'books' => $this->pickShelfBooks(
                    $books,
                    fn (Book $book) => (optional($book->publication_date)->year ?? 0) >= 2005,
                    $byNewest
                ),
            ],
        ])->map(function (array $shelf) {
            $shelf['books'] = $shelf['books']->take(10)->values();

            return $shelf;
        })->filter(fn (array $shelf) => $shelf['books']->isNotEmpty())->values();
    }

    private function pickShelfBooks(Collection $books, callable $predicate, callable $sorter): Collection
    {
        $selected = $sorter($books->filter($predicate));

        if ($selected->isNotEmpty()) {
            return $selected;
        }
        

        return $sorter($books);
    }

    private function bookHasGenres(Book $book, array $needles): bool
    {
        $genreNames = $book->genres
            ->pluck('genre_name')
            ->map(fn (string $name) => mb_strtolower($name))
            ->implode(' ');

        foreach ($needles as $needle) {
            if (str_contains($genreNames, $needle)) {
                return true;
            }
        }

        return false;
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
            ->where('orders.status', 'confirmed')
            ->pluck('orders.id_users')
            ->unique()
            ->values();
    }

    private function buildExternalReviews(Book $book): Collection
    {
        $authorName = $book->author->author_name ?? 'автора';
        $year = optional($book->publication_date)->format('Y') ?? 'неизвестного периода';

        return collect([
            [
                'source' => 'ReadRate',
                'author' => 'Редакция ReadRate',
                'date' => '12.02.2026',
                'rating' => 5,
                'text' => "Книга {$book->book_name} выделяется выразительным авторским стилем {$authorName} и хорошо работает как рекомендация для читателей, которым нужны сильные эмоции и насыщенный сюжет.",
            ],
            [
                'source' => 'BookMix',
                'author' => 'Обзор BookMix',
                'date' => '27.01.2026',
                'rating' => 4,
                'text' => "Издание {$year} года публикации в карточке выглядит как удачный выбор для домашней библиотеки: книга держит внимание, а описание и жанровая принадлежность хорошо совпадают с ожиданиями аудитории интернет-магазина.",
            ],
        ]);
    }
}
