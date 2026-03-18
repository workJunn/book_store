<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = $this->calculateTotal($cart);

        return view('cart.index', compact('cart', 'total'));
    }

    public function add($id, Request $request)
    {
        $book = Book::with('author')->findOrFail($id);

        $cart = session()->get('cart', []);
        $currentQuantity = $cart[$id]['quantity'] ?? 0;

        if ($book->stock_quantity <= 0) {
            return response()->json([
                'success' => true,
                'message' => 'Книг больше нет на складе',
                'cart_count' => $this->calculateCartCount($cart),
                'notice' => true,
            ]);
        }

        if ($currentQuantity >= $book->stock_quantity) {
            return response()->json([
                'success' => true,
                'message' => 'Книг больше нет на складе',
                'cart_count' => $this->calculateCartCount($cart),
                'notice' => true,
            ]);
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = $book->toCartItem();
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Книга добавлена в корзину',
            'cart_count' => $this->calculateCartCount($cart),
        ]);
    }

    public function increase($id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'error' => true,
                'message' => 'Товар не найден в корзине',
            ], 404);
        }

        $book = Book::findOrFail($id);

        if ($cart[$id]['quantity'] >= $book->stock_quantity) {
            return response()->json([
                'error' => true,
                'message' => 'Нельзя добавить больше, чем есть на складе',
            ], 422);
        }

        $cart[$id]['quantity']++;
        session()->put('cart', $cart);

        return response()->json([
            'quantity' => $cart[$id]['quantity'],
            'item_total' => $cart[$id]['price'] * $cart[$id]['quantity'],
            'total' => $this->calculateTotal($cart),
            'cart_count' => $this->calculateCartCount($cart),
        ]);
    }

    public function decrease($id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'error' => true,
                'message' => 'Товар не найден в корзине',
            ], 404);
        }

        if ($cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity']--;
            session()->put('cart', $cart);

            return response()->json([
                'quantity' => $cart[$id]['quantity'],
                'item_total' => $cart[$id]['price'] * $cart[$id]['quantity'],
                'total' => $this->calculateTotal($cart),
                'cart_count' => $this->calculateCartCount($cart),
            ]);
        }

        unset($cart[$id]);
        session()->put('cart', $cart);

        return response()->json([
            'removed' => true,
            'total' => $this->calculateTotal($cart),
            'cart_count' => $this->calculateCartCount($cart),
        ]);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Книга удалена из корзины');
    }

    public function clear()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'total' => 0,
            'cart_count' => 0,
        ]);
    }

    private function calculateTotal(array $cart): float
    {
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    private function calculateCartCount(array $cart): int
    {
        return array_sum(array_column($cart, 'quantity'));
    }
}