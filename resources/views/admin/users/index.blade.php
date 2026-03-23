<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пользователи - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg">
            @include('partials.admin-page-head', ['title' => 'Пользователи'])

            @if(session('status'))
                <div class="success-box">{{ session('status') }}</div>
            @endif

            <section class="stack-md">
                @forelse($users as $user)
                    <article class="info-box stack-sm">
                        <div class="order-line">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-link">{{ $user->name }}</a>
                            <span>{{ $user->email }}</span>
                            <span>{{ $user->role?->role_name ?? 'user' }}</span>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        Пользователей пока нет.
                    </div>
                @endforelse
            </section>
        </section>
    </main>
</body>
</html>
