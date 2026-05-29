<header class="site-header">
    <div class="container site-header__inner">
        <div class="site-brand">
            <a href="{{ route('home') }}" class="site-logo">Книжный Мир</a>

            @if(($showNav ?? true) === true)
                <nav class="site-nav" aria-label="Основная навигация">
                    <a href="{{ route('catalog') }}">Каталог</a>
                </nav>
            @endif
        </div>

        @if(($showSearch ?? true) === true)
            <form method="GET" action="{{ route('books.search') }}" class="site-search" role="search">
                <label for="site-search-input" class="sr-only">Поиск книг</label>
                <input
                    id="site-search-input"
                    type="search"
                    name="search"
                    value="{{ old('search', request('search', '')) }}"
                    placeholder="Поиск книг"
                >
            </form>
        @endif

        <div class="site-actions">
            @if(($showFavorites ?? true) === true)
                <a href="{{ route('favorites') }}" class="icon-link" title="Избранное" aria-label="Избранное">
                    <span class="bookmark-icon" aria-hidden="true"></span>
                    <span class="cart-count" data-favorites-count>0</span>
                </a>
            @endif

            @if(($showCart ?? true) === true)
                <a href="{{ route('cart.index') }}" class="icon-link" title="Корзина" aria-label="Корзина">
                    <span aria-hidden="true">🛒</span>
                    <span class="cart-count" data-cart-count>
                        {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                    </span>
                </a>
            @endif

            @if(($showCatalogButton ?? false) === true)
                <a href="{{ route('catalog') }}" class="btn btn-secondary">Каталог</a>
            @endif

            @guest
                @if(($showAuthButtons ?? true) === true)
                    <a href="{{ route('login') }}" class="btn btn-secondary">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Регистрация</a>
                @endif
            @endguest

            @auth
                @if(($showProfile ?? true) === true)
                    <a href="{{ auth()->user()?->isAdmin() ? route('admin.index') : route('dashboard') }}" class="icon-link" title="Профиль" aria-label="Профиль">👤</a>
                @endif
            @endauth
        </div>
    </div>

    @if (session('search_error'))
        <div class="container">
            <div class="site-search-notice" role="alert">{{ session('search_error') }}</div>
        </div>
    @endif
</header>
