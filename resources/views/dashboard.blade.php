<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column {{ $user->isAdmin() ? 'admin-page' : '' }}" data-home-url="{{ route('home') }}">
    @if(! $user->isAdmin())
        @include('partials.site-header', ['showAuthButtons' => false])
    @endif

    <main class="site-main">
        <section class="container">
            <section class="stack-md">
                <div class="section-head {{ $user->isAdmin() ? 'section-head--admin' : '' }}">
                    <div>
                        <h1 class="section-title">{{ $user->name }}</h1>
                    </div>
                    @if($user->isAdmin())
                        @include('partials.admin-search')
                        @include('partials.admin-nav')
                    @else
                        <div class="status-badge {{ $user->email_verified_at ? 'status-badge--ok' : 'status-badge--warn' }}">
                            {{ $user->email_verified_at ? 'Email подтверждён' : 'Email не подтверждён' }}
                        </div>
                    @endif
                </div>

                @if(session('status'))
                    <div class="success-box">{{ session('status') }}</div>
                @endif

                <section class="profile-section stack-md">
                    <div>
                        <h2 class="subheading">Личные данные</h2>
                        <p class="section-text">Все основные сведения о вашем аккаунте собраны в одной аккуратной сетке.</p>
                    </div>

                    <div class="profile-info-grid">
                        <article class="info-box profile-card">
                            <div class="info-label">Имя</div>
                            <div class="info-value">{{ $user->name }}</div>
                        </article>
                        <article class="info-box profile-card">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $user->email }}</div>
                        </article>
                        <article class="info-box profile-card">
                            <div class="info-label">Телефон</div>
                            <div class="info-value">{{ $user->phone_number ?: 'Не указан' }}</div>
                        </article>
                        <article class="info-box profile-card">
                            <div class="balance-row">
                                <div class="balance-row__details">
                                    <div class="info-label">Баланс</div>
                                    <div class="info-value balance-row__value">{{ number_format((float) $user->balance, 2, '.', ' ') }} ₽</div>
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-secondary balance-row__button"
                                    data-open-topup
                                    aria-haspopup="dialog"
                                    aria-controls="topup-modal"
                                >
                                    Пополнить
                                </button>
                            </div>
                        </article>
                        <article class="info-box profile-card">
                            <div class="info-label">Дата регистрации</div>
                            <div class="info-value">{{ $user->registration_date ? $user->registration_date->format('d.m.Y H:i') : 'Не указана' }}</div>
                        </article>
                        <article class="info-box profile-card">
                            <div class="info-label">Последнее обновление</div>
                            <div class="info-value">{{ $user->updated_at ? $user->updated_at->format('d.m.Y H:i') : 'Не обновлялся' }}</div>
                        </article>
                    </div>
                </section>

                <section class="stack-md">
                    <div>
                        <h2 class="subheading">Мои заказы</h2>
                        <p class="section-text">Здесь отображаются оформленные и оплаченные заказы.</p>
                    </div>

                    @if($user->orders->isNotEmpty())
                        <div class="stack-md">
                            @foreach($user->orders as $order)
                                <article class="info-box stack-sm">
                                    <div class="order-line">
                                        <a href="{{ route('orders.show', $order) }}" class="text-link">Заказ №{{ $order->getKey() }}</a>
                                        <span>{{ $order->order_date ? $order->order_date->format('d.m.Y H:i') : 'Дата не указана' }}</span>
                                        <span>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} ₽</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            У вас пока нет заказов.
                        </div>
                    @endif
                </section>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-secondary cart-checkout-button">Выйти</button>
                </form>
            </section>
        </section>
    </main>

    <div
        class="checkout-modal {{ $errors->has('amount') || $errors->has('payment_method') ? 'is-open' : '' }}"
        id="topup-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="topup-title"
        aria-hidden="{{ $errors->has('amount') || $errors->has('payment_method') ? 'false' : 'true' }}"
    >
        <div class="checkout-dialog info-box stack-md topup-dialog" tabindex="-1">
            <div class="stack-sm">
                <h2 class="subheading" id="topup-title">Пополнение баланса</h2>
                <p class="section-text">Укажите сумму и выберите удобный способ оплаты.</p>
            </div>

            <form method="POST" action="{{ route('balance.topup') }}" class="stack-md">
                @csrf

                <div class="form-group">
                    <label for="topup-amount">Сумма пополнения</label>
                    <input
                        id="topup-amount"
                        type="text"
                        name="amount"
                        inputmode="numeric"
                        pattern="[0-9]+([.,][0-9]{1,2})?"
                        placeholder="Например, 1500"
                        value="{{ old('amount') }}"
                        required
                    >
                    @error('amount')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <span class="payment-methods__label">Способ оплаты</span>
                    <div class="payment-methods" role="radiogroup" aria-label="Способ оплаты">
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="card" @checked(old('payment_method') === 'card') required>
                            <span>Картой</span>
                        </label>
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="sbp" @checked(old('payment_method') === 'sbp') required>
                            <span>СБП</span>
                        </label>
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="qr" @checked(old('payment_method') === 'qr') required>
                            <span>QR-кодом</span>
                        </label>
                    </div>
                    @error('payment_method')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="actions">
                    <button type="button" class="btn btn-secondary" data-close-topup>Отмена</button>
                    <button type="submit" class="btn btn-primary">Оплатить</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
