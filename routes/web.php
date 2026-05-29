<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PartnerProgramController;


Route::get('/', [BookController::class, 'welcome'])->name('home');
Route::get('/catalog', [BookController::class, 'catalog'])->name('catalog');
Route::get('/search', [BookController::class, 'search'])->name('books.search');
Route::get('/favorites', [BookController::class, 'favorites'])->name('favorites');
Route::view('/payment-methods', 'payment-methods')->name('payment-methods');
Route::get('/partner-program', [PartnerProgramController::class, 'show'])->name('partner.program');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
Route::post('/books/{book}/reviews', [BookController::class, 'storeReview'])->middleware('auth')->name('books.reviews.store');
Route::post('/reviews/{review}/vote', [BookController::class, 'voteReview'])->middleware('auth')->name('reviews.vote');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/increase/{id}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{id}', [CartController::class, 'decrease'])->name('cart.decrease');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/orders/{order}/payment', [CartController::class, 'payment'])->middleware('auth')->name('orders.payment');
Route::post('/orders/{order}/pay', [CartController::class, 'pay'])->middleware('auth')->name('orders.pay');
Route::get('/orders/{order}', [ProfileController::class, 'showOrder'])->middleware('auth')->name('orders.show');
Route::get('/orders/{order}/books/{book}/download', [CartController::class, 'download'])->middleware('auth')->name('orders.books.download');

Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

Route::get('/login', [LoginController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');

Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'edit'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'update'])->middleware('guest')->name('password.update');

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

Route::get('/dashboard', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');
Route::get('/partner-program/apply', [PartnerProgramController::class, 'create'])
    ->name('partner.program.apply.form');
Route::post('/partner-program/apply', [PartnerProgramController::class, 'apply'])
    ->middleware('auth')
    ->name('partner.program.apply');
Route::post('/balance/top-up', [ProfileController::class, 'topUp'])
    ->middleware('auth')
    ->name('balance.topup');
Route::get('/author', [AuthorController::class, 'index'])
    ->middleware(['auth', 'author'])
    ->name('author.index');
Route::get('/author/books/create', [AuthorController::class, 'createBook'])
    ->middleware(['auth', 'author'])
    ->name('author.books.create');
Route::post('/author/books', [AuthorController::class, 'storeBook'])
    ->middleware(['auth', 'author'])
    ->name('author.books.store');
Route::get('/author/books/{book}/edit', [AuthorController::class, 'editBook'])
    ->middleware(['auth', 'author'])
    ->name('author.books.edit');
Route::match(['put', 'patch'], '/author/books/{book}', [AuthorController::class, 'updateBook'])
    ->middleware(['auth', 'author'])
    ->name('author.books.update');
Route::delete('/author/books/{book}', [AuthorController::class, 'destroyBook'])
    ->middleware(['auth', 'author'])
    ->name('author.books.destroy');
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.index');
Route::get('/admin/search', [AdminController::class, 'search'])
    ->middleware(['auth', 'admin'])
    ->name('admin.search');
Route::get('/admin/authors', [AdminController::class, 'authors'])
    ->middleware(['auth', 'admin'])
    ->name('admin.authors.index');
Route::get('/admin/authors/{author}', [AdminController::class, 'showAuthor'])
    ->middleware(['auth', 'admin'])
    ->name('admin.authors.show');
Route::delete('/admin/authors/{author}', [AdminController::class, 'destroyAuthor'])
    ->middleware(['auth', 'admin'])
    ->name('admin.authors.destroy');
Route::get('/admin/users', [AdminController::class, 'users'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.index');
Route::get('/admin/partner-applications', [AdminController::class, 'partnerApplications'])
    ->middleware(['auth', 'admin'])
    ->name('admin.partner-applications.index');
Route::get('/admin/partner-applications/{application}', [AdminController::class, 'showPartnerApplication'])
    ->middleware(['auth', 'admin'])
    ->name('admin.partner-applications.show');
Route::post('/admin/partner-applications/{application}/approve', [AdminController::class, 'approvePartnerApplication'])
    ->middleware(['auth', 'admin'])
    ->name('admin.partner-applications.approve');
Route::get('/admin/users/{user}', [AdminController::class, 'showUser'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.show');
Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.destroy');
Route::get('/admin/books', [AdminController::class, 'books'])
    ->middleware(['auth', 'admin'])
    ->name('admin.books.index');
Route::get('/admin/books/create', [AdminController::class, 'createBook'])
    ->middleware(['auth', 'admin'])
    ->name('admin.books.create');
Route::post('/admin/books', [AdminController::class, 'storeBook'])
    ->middleware(['auth', 'admin'])
    ->name('admin.books.store');
Route::get('/admin/books/{book}/edit', [AdminController::class, 'editBook'])
    ->middleware(['auth', 'admin'])
    ->name('admin.books.edit');
Route::match(['put', 'patch'], '/admin/books/{book}', [AdminController::class, 'updateBook'])
    ->middleware(['auth', 'admin'])
    ->name('admin.books.update');
Route::delete('/admin/books/{book}', [AdminController::class, 'destroyBook'])
    ->middleware(['auth', 'admin'])
    ->name('admin.books.destroy');
Route::get('/admin/orders', [AdminController::class, 'orders'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.index');
Route::get('/admin/orders/{order}', [AdminController::class, 'showOrder'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.show');

    
