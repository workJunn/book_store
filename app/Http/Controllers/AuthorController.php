<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    public function index()
    {
        $author = Auth::user()->authorProfile()->with(['books.publisher', 'books.genres'])->firstOrFail();

        return view('author.index', [
            'author' => $author,
            'books' => $author->books->sortBy('book_name')->values(),
        ]);
    }

    public function createBook()
    {
        return view('author.books.form', [
            'book' => new Book(),
            'publishers' => Publisher::query()->orderBy('publisher_name')->get(),
            'genres' => Genre::query()->orderBy('genre_name')->get(),
            'selectedGenres' => collect(),
            'formAction' => route('author.books.store'),
            'formTitle' => 'Добавить книгу',
        ]);
    }

    public function storeBook(Request $request)
    {
        $author = Auth::user()->authorProfile()->firstOrFail();
        $validated = $this->validateBook($request);
        $validated['id_author'] = $author->getKey();

        $book = Book::create($validated);
        $book->genres()->sync($request->input('genre_ids', []));

        return redirect()
            ->route('author.index')
            ->with('status', 'Книга добавлена в авторский каталог.');
    }

    public function editBook(Book $book)
    {
        $author = Auth::user()->authorProfile()->firstOrFail();
        abort_unless((int) $book->id_author === (int) $author->getKey(), 403);

        $book->load('genres');

        return view('author.books.form', [
            'book' => $book,
            'publishers' => Publisher::query()->orderBy('publisher_name')->get(),
            'genres' => Genre::query()->orderBy('genre_name')->get(),
            'selectedGenres' => $book->genres->pluck('id_genre'),
            'formAction' => route('author.books.update', $book),
            'formTitle' => 'Редактировать книгу',
        ]);
    }

    public function updateBook(Request $request, Book $book)
    {
        $author = Auth::user()->authorProfile()->firstOrFail();
        abort_unless((int) $book->id_author === (int) $author->getKey(), 403);

        $validated = $this->validateBook($request);
        $validated['id_author'] = $author->getKey();

        $book->update($validated);
        $book->genres()->sync($request->input('genre_ids', []));

        return redirect()
            ->route('author.index')
            ->with('status', 'Книга обновлена.');
    }

    private function validateBook(Request $request): array
    {
        return $request->validate([
            'book_name' => ['required', 'string', 'max:200'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['required', 'integer', 'between:0,95'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'publication_date' => ['nullable', 'date'],
            'number_of_pages' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'id_publishers' => ['nullable', 'exists:publishers,id_publishers'],
            'is_preorder' => ['nullable', 'boolean'],
            'genre_ids' => ['nullable', 'array'],
            'genre_ids.*' => ['exists:genres,id_genre'],
        ]);
    }
}
