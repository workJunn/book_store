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

            <div class="admin-layout__content stack-lg">
                @include('partials.admin-page-head', ['title' => 'Партнерские заявки', 'showSearch' => false])

                @if(session('status'))
                    <div class="success-box">{{ session('status') }}</div>
                @endif

                <section class="stack-md">
                    @forelse($applications as $application)
                        <article class="info-box stack-sm">
                            <div class="order-line">
                                <a href="{{ route('admin.partner-applications.show', $application) }}" class="text-link">
                                    {{ $application->user?->name ?? 'Пользователь' }}
                                </a>
                                <span>{{ $application->user?->email ?? 'Email не указан' }}</span>
                                <span>{{ $application->status === 'approved' ? 'Подтверждена' : 'Ожидает подтверждения' }}</span>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            Новых партнерских заявок пока нет.
                        </div>
                    @endforelse
                </section>
            </div>
        </section>
    </main>
</body>
</html>
