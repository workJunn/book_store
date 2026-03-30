<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Способы оплаты - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-home-url="{{ route('catalog') }}">
    @include('partials.site-header')

    <main class="site-main">
        <section class="container stack-lg">
            <section class="stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">Способы оплаты</h1>
                        <p class="section-text">На сайте доступны те же форматы оплаты, что и в профиле при пополнении баланса.</p>
                    </div>
                </div>

                <div class="simple-grid simple-grid--3">
                    <article class="info-box stack-sm">
                        <h2 class="subheading">Банковская карта</h2>
                        <p>Моментальная оплата Visa, Mastercard и Мир. Среднее подтверждение: до 10 секунд.</p>
                    </article>
                    <article class="info-box stack-sm">
                        <h2 class="subheading">СБП</h2>
                        <p>Оплата по QR и через банковское приложение. Комиссия для покупателя: 0%.</p>
                    </article>
                    <article class="info-box stack-sm">
                        <h2 class="subheading">QR-код</h2>
                        <p>Подходит для быстрого подтверждения на мобильных устройствах и офлайн-платежей.</p>
                    </article>
                </div>
            </section>
        </section>
    </main>

    @include('partials.site-footer')
</body>
</html>
