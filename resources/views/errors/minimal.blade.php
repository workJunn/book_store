<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Ошибка' }} - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column error-page" data-home-url="{{ route('catalog') }}">
    @include('partials.site-header', [
        'showSearch' => false,
    ])

    <main class="site-main error-page__main">
        <section class="container">
            <section class="error-hero">
                <div class="error-hero__glow error-hero__glow--left" aria-hidden="true"></div>
                <div class="error-hero__glow error-hero__glow--right" aria-hidden="true"></div>

                <div class="error-hero__grid">
                    <div class="error-hero__copy">
                        <p class="eyebrow">Системное сообщение</p>
                        <div class="error-hero__code">{{ $code ?? 'Ошибка' }}</div>
                        <h1 class="section-title error-hero__title">{{ $title ?? 'Что-то пошло не так' }}</h1>
                        <p class="section-text error-hero__text">{{ $message ?? 'Попробуйте вернуться на главную страницу или открыть каталог.' }}</p>

                        <div class="actions error-hero__actions">
                            <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
                            <a href="{{ route('catalog') }}" class="btn btn-secondary">Открыть каталог</a>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Назад</a>
                        </div>
                    </div>

                    <div class="error-hero__sidebar">
                        <article class="error-card error-card--accent">
                            <div class="info-label">Что произошло</div>
                            <div class="info-value">{{ $headline ?? 'Страница сейчас недоступна' }}</div>
                            <p>{{ $description ?? 'Если ошибка повторится, попробуйте снова немного позже.' }}</p>
                        </article>

                        <article class="error-card">
                            <div class="info-label">Что можно сделать</div>
                            <div class="info-value">{{ $hint ?? 'Обновите страницу или вернитесь к каталогу.' }}</div>
                        </article>
                    </div>
                </div>
            </section>
        </section>
    </main>

    <div class="sr-only" id="app-live-region" aria-live="polite" aria-atomic="true"></div>
    @include('partials.site-footer')
</body>
</html>
