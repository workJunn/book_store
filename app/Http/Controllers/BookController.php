<?php

namespace App\Http\Controllers;

use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with(['author', 'publisher'])->get();

        return view('welcome', compact('books'));
    }

    public function show($id)
    {
        $book = Book::with(['author', 'publisher'])->findOrFail($id);

        return view('books.show', compact('book'));
    }
}