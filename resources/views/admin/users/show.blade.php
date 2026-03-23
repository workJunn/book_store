<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пользователь - Админ панель</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column admin-page" data-home-url="{{ route('home') }}">
    <main class="site-main">
        <section class="container stack-lg">
            <section class="section-head">
                <div>
                    <h1 class="section-title">{{ $user->name }}</h1>
                    <p class="section-text">Подробные данные пользователя.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Назад</a>
            </section>

            @if(session('status'))
                <div class="success-box">{{ session('status') }}</div>
            @endif

            <section class="stack-md">
                <div class="profile-info">
                    <div class="info-box">
                        <div class="info-label">ID пользователя</div>
                        <div class="info-value">{{ $user->getKey() }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Имя</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Телефон</div>
                        <div class="info-value">{{ $user->phone_number ?: 'Не указан' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Роль</div>
                        <div class="info-value">{{ $user->role?->role_name ?? 'user' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">ID роли</div>
                        <div class="info-value">{{ $user->id_role ?? 'Не указан' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Баланс</div>
                        <div class="info-value">{{ number_format((float) $user->balance, 2, '.', ' ') }} ₽</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Дата регистрации</div>
                        <div class="info-value">{{ $user->registration_date ? $user->registration_date->format('d.m.Y H:i') : 'Не указана' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Email подтвержден</div>
                        <div class="info-value">{{ $user->email_verified_at ? $user->email_verified_at->format('d.m.Y H:i') : 'Нет' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Создан в системе</div>
                        <div class="info-value">{{ $user->created_at ? $user->created_at->format('d.m.Y H:i') : 'Не указано' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Обновлен в системе</div>
                        <div class="info-value">{{ $user->updated_at ? $user->updated_at->format('d.m.Y H:i') : 'Не указано' }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Хеш пароля</div>
                        <div class="info-value">{{ $user->password }}</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label">Remember token</div>
                        <div class="info-value">{{ $user->remember_token ?: 'Не указан' }}</div>
                    </div>
                </div>
            </section>

            <section class="stack-md">
                <div>
                    <h2 class="subheading">Заказы пользователя</h2>
                </div>

                @if($user->orders->isNotEmpty())
                    <div class="stack-md">
                        @foreach($user->orders as $order)
                            <article class="info-box stack-sm">
                                <div class="order-line">
                                    <span>Заказ №{{ $order->getKey() }}</span>
                                    <span>{{ $order->order_date ? $order->order_date->format('d.m.Y H:i') : 'Дата не указана' }}</span>
                                    <span>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} ₽</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        У пользователя пока нет заказов.
                    </div>
                @endif
            </section>

            <section class="stack-md">
                <button type="button" class="btn btn-danger" data-open-user-delete>Удалить пользователя</button>
            </section>
        </section>

        <div class="checkout-modal" id="delete-user-modal" role="dialog" aria-modal="true" aria-labelledby="delete-user-title" aria-hidden="true">
            <div class="checkout-dialog info-box stack-md" tabindex="-1">
                <h2 class="section-title" id="delete-user-title">Удалить пользователя?</h2>
                <p class="section-text">Вы точно хотите удалить пользователя {{ $user->name }} из системы?</p>
                <div class="actions">
                    <button type="button" class="btn btn-secondary" data-close-user-delete>Нет</button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Да</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
</body>
</html>
