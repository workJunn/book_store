<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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

    public function payment(Order $order)
    {
        abort_unless((int) $order->id_users === (int) Auth::id(), 403);

        $order->load(['details.book', 'user']);

        return view('orders.payment', [
            'order' => $order,
            'orderDetails' => $order->details,
            'user' => $order->user,
        ]);
    }

    public function pay(Order $order)
    {
        abort_unless((int) $order->id_users === (int) Auth::id(), 403);

        if ($order->status !== 'Оплачен') {
            $order->update([
                'status' => 'Оплачен',
            ]);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Оплата прошла успешно.')
            ->with('auto_download_book_ids', $order->details()
                ->whereHas('book', fn ($query) => $query->whereNotNull('digital_file_path'))
                ->pluck('id_books')
                ->map(fn ($id) => (int) $id)
                ->all());
    }

    public function download(Order $order, Book $book)
    {
        abort_unless((int) $order->id_users === (int) Auth::id(), 403);
        abort_unless($order->status === 'Оплачен', 403);

        $hasBookInOrder = $order->details()
            ->where('id_books', $book->getKey())
            ->exists();

        abort_unless($hasBookInOrder && $book->digital_file_path, 404);

        return Storage::disk('local')->download(
            $book->digital_file_path,
            $book->digital_file_download_name,
            ['Content-Disposition' => ResponseHeaderBag::DISPOSITION_ATTACHMENT]
        );
    }

    public function checkout(Request $request)
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Для оформления заказа войдите в аккаунт.',
                    'requires_auth' => true,
                    'login_url' => route('login'),
                ], 401);
            }

            return redirect()->route('login');
        }

        $cart = session()->get('cart', []);
        $user = Auth::user();

        if ($cart === []) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Корзина пуста.',
                ], 422);
            }

            return redirect()
                ->route('cart.index')
                ->with('search_error', 'Корзина пуста.');
        }

        $orderTotal = $this->calculateTotal($cart);

        if ((float) $user->balance < $orderTotal) {
            $message = 'Недостаточно средств на балансе для оформления заказа.';

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('cart.index')
                ->with('search_error', $message);
        }

        $bookIds = array_map('intval', array_keys($cart));
        $books = Book::query()
            ->whereIn('id_books', $bookIds)
            ->get()
            ->keyBy('id_books');

        foreach ($cart as $bookId => $item) {
            $book = $books->get((int) $bookId);

            if (! $book) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Одна из книг больше недоступна.',
                    ], 422);
                }

                return redirect()
                    ->route('cart.index')
                    ->with('search_error', 'Одна из книг больше недоступна.');
            }

            if ($item['quantity'] > $book->stock_quantity) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => "Недостаточно экземпляров книги \"{$book->book_name}\".",
                    ], 422);
                }

                return redirect()
                    ->route('cart.index')
                    ->with('search_error', "Недостаточно экземпляров книги \"{$book->book_name}\".");
            }
        }

        $order = DB::transaction(function () use ($cart, $books, $user, $orderTotal) {
            $order = Order::create([
                'id_users' => Auth::id(),
                'status' => 'Оформлен',
                'total_amount' => $orderTotal,
            ]);

            foreach ($cart as $bookId => $item) {
                $book = $books->get((int) $bookId);

                $order->details()->create([
                    'id_books' => $book->getKey(),
                    'quantity' => $item['quantity'],
                    'price_per_item' => $book->price,
                ]);

                $book->decrement('stock_quantity', $item['quantity']);
            }

            $user->decrement('balance', $orderTotal);

            return $order;
        });

        session()->forget('cart');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Заказ успешно оформлен.',
                'order_id' => $order->getKey(),
                'cart_count' => 0,
            ]);
        }

        return redirect()->route('orders.payment', $order);
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
