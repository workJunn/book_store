<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявка партнера - Книжный Мир</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page-shell page-shell--column" data-home-url="{{ route('catalog') }}">
    @include('partials.site-header')

    <main class="site-main">
        <section class="container stack-lg">
            <section class="stack-md">
                <div class="section-head">
                    <div>
                        <h1 class="section-title">Стать партнером</h1>
                    </div>
                    <a href="{{ route('partner.program') }}" class="btn btn-secondary">Назад</a>
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

                            <div class="actions">
                                <button type="submit" class="btn btn-primary">Отправить заявку</button>
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
