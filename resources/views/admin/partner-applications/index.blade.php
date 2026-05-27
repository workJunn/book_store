<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Партнерские заявки - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg admin-layout">
            @include('partials.admin-sidebar')

            @include('partials.admin-page-head', ['title' => 'Партнерские заявки', 'showSearch' => false])

            @if(session('status'))
                <div class="success-box">{{ session('status') }}</div>
            @endif

            <section class="stack-md">
                @forelse($applications as $application)
                    <article class="info-box stack-md">
                        <div class="order-line">
                            <span>{{ $application->pen_name }}</span>
                            <span>{{ $application->user?->name ?? 'Пользователь' }}</span>
                            <span>{{ $application->status === 'approved' ? 'Подтверждена' : 'Ожидает подтверждения' }}</span>
                        </div>

                        <div class="simple-grid simple-grid--2">
                            <div class="stack-sm">
                                <div><strong>Email:</strong> {{ $application->user?->email }}</div>
                                <div><strong>Выплаты:</strong> {{ match($application->payment_method) {
                                    'card' => 'Карта',
                                    'sbp' => 'СБП',
                                    default => 'QR-код',
                                } }}</div>
                                <div><strong>Дата заявки:</strong> {{ $application->created_at?->format('d.m.Y H:i') }}</div>
                            </div>
                            <div class="stack-sm">
                                <div><strong>Биография:</strong> {{ $application->biography }}</div>
                                @if($application->experience_summary)
                                    <div><strong>Опыт:</strong> {{ $application->experience_summary }}</div>
                                @endif
                                @if($application->portfolio_url)
                                    <div><strong>Портфолио:</strong> <a href="{{ $application->portfolio_url }}" class="text-link">{{ $application->portfolio_url }}</a></div>
                                @endif
                            </div>
                        </div>

                        @if($application->status === 'pending')
                            <form method="POST" action="{{ route('admin.partner-applications.approve', $application) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Принять</button>
                            </form>
                        @else
                            <div class="muted">Подтверждена {{ $application->processed_at?->format('d.m.Y H:i') }}</div>
                        @endif
                    </article>
                @empty
                    <div class="empty-state">
                        Новых партнерских заявок пока нет.
                    </div>
                @endforelse
            </section>
        </section>
    </main>
</body>
</html>
