<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load([
            'orders' => fn ($query) => $query
                ->orderByDesc('id_orders'),
        ]);

        return view('dashboard', compact('user'));
    }

    public function showOrder(Order $order)
    {
        abort_unless((int) $order->id_users === (int) Auth::id(), 403);

        $order->load(['details.book.author', 'user']);

        return view('orders.show', [
            'order' => $order,
            'orderDetails' => $order->details,
            'user' => $order->user,
        ]);
    }
}
