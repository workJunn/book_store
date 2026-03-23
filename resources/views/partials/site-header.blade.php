<header class="site-header">
    <div class="container site-header__inner">
        <a href="{{ route('home') }}" class="site-logo">Книжный Мир</a>

        @if(($showNav ?? true) === true)
            <nav class="site-nav" aria-label="Основная навигация">
                <a href="{{ route('home') }}">Главная</a>
                <a href="{{ route('catalog') }}">Каталог</a>
                <a href="{{ route('favorites') }}">Избранное</a>
            </nav>
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
                    <a href="{{ route('User_login') }}" class="btn btn-secondary">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                @endif
            @endguest

            @auth
                @if(($showProfile ?? true) === true)
                    <a href="{{ route('dashboard') }}" class="icon-link" title="Профиль" aria-label="Профиль">👤</a>
                @endif
            @endauth
        </div>
    </div>
</header>
