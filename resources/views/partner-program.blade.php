<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Партнерская программа - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-home-url="{{ route('catalog') }}">
    @include('partials.site-header')

    <main class="site-main">
        <section class="container stack-lg">
            <section class="stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">Партнерская программа</h1>
                        <p class="section-text">Программа для авторов, экспертов и книжных кураторов, которые хотят продавать свои книги через площадку.</p>
                    </div>
                    <a href="#partner-application" class="btn btn-secondary">Стать партнером</a>
                </div>

                @if(session('status'))
                    <div class="success-box">{{ session('status') }}</div>
                @endif

                <div class="simple-grid simple-grid--3">
                    <article class="info-box stack-sm">
                        <div class="info-label">Аудитория</div>
                        <div class="info-value">42 000+</div>
                        <p class="section-text">Ежемесячно читают карточки книг и подборки на главной.</p>
                    </article>
                    <article class="info-box stack-sm">
                        <div class="info-label">Средняя конверсия</div>
                        <div class="info-value">7.4%</div>
                        <p class="section-text">Для книг, попавших в тематические подборки и сезонные витрины.</p>
                    </article>
                    <article class="info-box stack-sm">
                        <div class="info-label">Выплаты</div>
                        <div class="info-value">2 раза в месяц</div>
                        <p class="section-text">По карте, СБП или через QR-подтверждение.</p>
                    </article>
                </div>
            </section>

            <section class="stack-md">
                <div>
                    <h2 class="subheading">Условия участия</h2>
                    <p class="section-text">Перед отправкой заявки ознакомьтесь с базовыми требованиями программы.</p>
                </div>

                <div class="simple-grid simple-grid--2">
                    <article class="info-box stack-sm">
                        <h3 class="subheading">Что получает партнер</h3>
                        <ul class="program-list">
                            <li>Панель автора с возможностью добавлять книги и обновлять цену со скидкой.</li>
                            <li>Отдельное размещение в подборках и на тематических витринах.</li>
                            <li>Подключение к внутренним акциям и предзаказам.</li>
                        </ul>
                    </article>
                    <article class="info-box stack-sm">
                        <h3 class="subheading">Что требуется от партнера</h3>
                        <ul class="program-list">
                            <li>Минимум 1 готовая книга или подтвержденный рукописный проект.</li>
                            <li>Краткая биография и описание вашего каталога.</li>
                            <li>Готовность поддерживать актуальные цену, скидку и наличие.</li>
                        </ul>
                    </article>
                </div>
            </section>

            <section class="stack-md" id="partner-application">
                <div class="section-head">
                    <div>
                        <h2 class="subheading">Стать партнером</h2>
                        <p class="section-text">После отправки заявка попадет в отдельный раздел админ-панели на подтверждение.</p>
                    </div>
                </div>

                @guest
                    <div class="empty-state">
                        Чтобы подать заявку, войдите в аккаунт. <a href="{{ route('login') }}" class="text-link">Перейти ко входу</a>
                    </div>
                @else
                    @if($latestApplication && $latestApplication->status === 'approved')
                        <div class="success-box">
                            Вы уже участвуете в программе. Перейдите в <a href="{{ route('author.index') }}" class="text-link">панель автора</a>.
                        </div>
                    @elseif($latestApplication && $latestApplication->status === 'pending')
                        <div class="info-box">
                            Ваша заявка уже отправлена {{ $latestApplication->created_at?->format('d.m.Y H:i') }} и ожидает подтверждения администратора.
                        </div>
                    @else
                        <form method="POST" action="{{ route('partner.program.apply') }}" class="auth-card stack-md">
                            @csrf

                            <div class="form-group">
                                <label for="pen_name">Имя автора или псевдоним</label>
                                <input id="pen_name" name="pen_name" type="text" value="{{ old('pen_name', auth()->user()->name) }}" required>
                                @error('pen_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="biography">Краткая биография</label>
                                <textarea id="biography" name="biography" required>{{ old('biography') }}</textarea>
                                @error('biography')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="experience_summary">Опыт, жанры, тематика</label>
                                <textarea id="experience_summary" name="experience_summary">{{ old('experience_summary') }}</textarea>
                                @error('experience_summary')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="portfolio_url">Ссылка на портфолио или соцсети</label>
                                <input id="portfolio_url" name="portfolio_url" type="url" value="{{ old('portfolio_url') }}">
                                @error('portfolio_url')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <span class="payment-methods__label">Предпочтительный способ выплат</span>
                                <div class="payment-methods" role="radiogroup" aria-label="Способ выплат">
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
                                <button type="submit" class="btn btn-primary">Стать партнером</button>
                            </div>
                        </form>
                    @endif
                @endguest
            </section>
        </section>
    </main>

    @include('partials.site-footer')
</body>
</html>
