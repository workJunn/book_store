<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Партнерская заявка - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg admin-layout">
            @include('partials.admin-sidebar')

            <div class="admin-layout__content stack-lg">
                <section class="section-head">
                    <div>
                        <h1 class="section-title">{{ $user?->name ?? 'Пользователь' }}</h1>
                        <p class="section-text">Партнерская заявка пользователя.</p>
                    </div>
                    <a href="{{ route('admin.partner-applications.index') }}" class="btn btn-secondary">Назад</a>
                </section>

                @if(session('status'))
                    <div class="success-box">{{ session('status') }}</div>
                @endif

                <section class="stack-md">
                    <div class="profile-info">
                        <div class="info-box">
                            <div class="info-label">ID пользователя</div>
                            <div class="info-value">{{ $user?->getKey() ?? 'Не указан' }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Имя пользователя</div>
                            <div class="info-value">{{ $user?->name ?? 'Не указан' }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $user?->email ?? 'Не указан' }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Роль</div>
                            <div class="info-value">{{ $user?->role?->role_name ?? 'user' }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Имя автора</div>
                            <div class="info-value">{{ $application->pen_name }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Статус заявки</div>
                            <div class="info-value" data-partner-application-status>{{ $application->status === 'approved' ? 'Подтверждена' : 'Ожидает подтверждения' }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Способ выплат</div>
                            <div class="info-value">{{ match($application->payment_method) {
                                'card' => 'Карта',
                                'sbp' => 'СБП',
                                default => 'QR-код',
                            } }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Дата заявки</div>
                            <div class="info-value">{{ $application->created_at?->format('d.m.Y H:i') ?? 'Не указана' }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Дата обработки</div>
                            <div class="info-value" data-partner-application-processed-at>{{ $application->processed_at?->format('d.m.Y H:i') ?? 'Не обработана' }}</div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Портфолио</div>
                            <div class="info-value">
                                @if($application->portfolio_url)
                                    <a href="{{ $application->portfolio_url }}" class="text-link">{{ $application->portfolio_url }}</a>
                                @else
                                    Не указано
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <section class="stack-md">
                    <div class="info-box stack-sm">
                        <div class="info-label">Биография</div>
                        <div class="info-value">{{ $application->biography }}</div>
                    </div>

                    <div class="info-box stack-sm">
                        <div class="info-label">Опыт</div>
                        <div class="info-value">{{ $application->experience_summary ?: 'Не указан' }}</div>
                    </div>
                </section>

                @if($application->status === 'pending')
                    <section class="stack-md" data-partner-application-actions>
                        <form method="POST" action="{{ route('admin.partner-applications.approve', $application) }}" data-partner-application-approve-form>
                            @csrf
                            <button type="submit" class="btn btn-primary">Принять</button>
                        </form>
                    </section>
                @endif
            </div>
        </section>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
</body>
</html>
