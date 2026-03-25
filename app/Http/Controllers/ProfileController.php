<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
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

    public function topUp(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'payment_method' => ['required', 'in:card,sbp,qr'],
        ], [
            'amount.required' => 'Укажите сумму пополнения.',
            'amount.numeric' => 'Сумма пополнения должна быть числом.',
            'amount.min' => 'Минимальная сумма пополнения 1 ₽.',
            'amount.max' => 'Сумма пополнения слишком большая.',
            'payment_method.required' => 'Выберите способ оплаты.',
            'payment_method.in' => 'Выбран неподдерживаемый способ оплаты.',
        ]);

        $user = Auth::user();
        $amount = round((float) $validated['amount'], 2);

        $user->forceFill([
            'balance' => round((float) $user->balance + $amount, 2),
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Баланс пополнен на '.number_format($amount, 2, '.', ' ').' ₽.');
    }
}
