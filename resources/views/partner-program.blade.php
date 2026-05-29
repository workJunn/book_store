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
                </div>

                @if(session('status'))
                    <div class="success-box">{{ session('status') }}</div>
                @endif

                <div class="simple-grid simple-grid--3">
                    <article class="info-box stack-sm">
                        <div class="info-label">Аудитория</div>
                        <p class="partner-audience-highlight">Большинство граждан РФ (71%) читают книги ежедневно или несколько раз в неделю. Об этом свидетельствуют результаты опроса аналитического центра ВЦИОМ</p>
                    </article>
                    <article class="info-box stack-sm">
                        <div class="info-label">Средняя комиссия</div>
                        <div class="info-value partner-commission-value">3%</div>
                    </article>
                    <article class="info-box stack-sm">
                        <div class="info-label">Выплаты</div>
                        <div class="info-value">2 раза в месяц</div>
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

            <section class="stack-md">
                <div class="actions">
                    <a href="{{ route('partner.program.apply.form') }}" class="btn btn-primary">Стать партнером</a>
                </div>
            </section>
        </section>
    </main>

    @include('partials.site-footer')
</body>
</html>
