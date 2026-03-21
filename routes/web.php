<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;



Route::get('/', [BookController::class, 'welcome'])->name('home');
Route::get('/catalog', [BookController::class, 'catalog'])->name('catalog');
Route::get('/favorites', [BookController::class, 'favorites'])->name('favorites');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
Route::post('/books/{book}/reviews', [BookController::class, 'storeReview'])->middleware('auth')->name('books.reviews.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/increase/{id}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{id}', [CartController::class, 'decrease'])->name('cart.decrease');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

Route::get('/login', [LoginController::class, 'create'])->middleware('guest')->name('User_login');
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
