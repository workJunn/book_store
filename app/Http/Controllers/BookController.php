<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

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

        if ($request->filled('genre')) {
            $genreId = (int) $request->input('genre');

            $query->whereHas('genres', function ($genreQuery) use ($genreId) {
                $genreQuery->where('genres.id_genre', $genreId);
            });
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        $sort = $request->string('sort')->toString();

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating_desc' => $query->orderByDesc('average_rating')->orderBy('book_name'),
            'newest' => $query->orderByDesc('publication_date')->orderBy('book_name'),
            default => $query->orderBy('book_name'),
        };

        $books = $query->get();

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));

            $books = $books
                ->filter(fn (Book $book) => mb_stripos($book->book_name, $search) !== false)
                ->values();
        }

        $books = $this->paginateCollection($books, 9, $request);
        $genres = Genre::query()->orderBy('genre_name')->get();

        return view('catalog', [
            'books' => $books,
            'genres' => $genres,
            'filters' => [
                'search' => (string) $request->string('search'),
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
        $book = Book::with(['author', 'publisher', 'genres'])->findOrFail($id);

        return view('books.show', compact('book'));
    }

    private function paginateCollection(Collection $items, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
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
}
