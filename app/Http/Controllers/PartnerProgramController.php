<?php

namespace App\Http\Controllers;

use App\Models\PartnerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerProgramController extends Controller
{
    public function show()
    {
        return view('partner-program');
    }

    public function create()
    {
        return view('partner-program-apply', [
            'latestApplication' => Auth::user()?->partnerApplications()->latest('created_at')->first(),
        ]);
    }

    public function apply(Request $request)
    {
        $user = $request->user();
        $latestApplication = $user->partnerApplications()->latest('created_at')->first();

        if ($latestApplication && in_array($latestApplication->status, ['pending', 'approved'], true)) {
            return redirect()
                ->route('partner.program')
                ->with('status', $latestApplication->status === 'approved'
                    ? 'Вы уже подключены к партнерской программе.'
                    : 'Ваша заявка уже находится на рассмотрении.');
        }

        $validated = $request->validate([
            'pen_name' => ['required', 'string', 'max:50'],
            'biography' => ['required', 'string', 'max:2000'],
            'experience_summary' => ['nullable', 'string', 'max:1000'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ], [
            'pen_name.required' => 'Укажите имя автора или литературный псевдоним.',
            'biography.required' => 'Добавьте короткую биографию.',
            'biography.max' => 'Биография не должна превышать 2000 символов.',
            'experience_summary.max' => 'Описание опыта не должно превышать 1000 символов.',
            'portfolio_url.url' => 'Ссылка на портфолио должна быть корректной.',
        ]);

        PartnerApplication::create([
            'id_users' => $user->getKey(),
            'pen_name' => $validated['pen_name'],
            'biography' => $validated['biography'],
            'experience_summary' => $validated['experience_summary'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'payment_method' => 'card',
            'status' => 'pending',
        ]);

        return redirect()
            ->route('partner.program')
            ->with('status', 'Заявка отправлена. Администратор рассмотрит её в ближайшее время.');
    }
}
