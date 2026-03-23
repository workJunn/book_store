<form method="GET" action="{{ route('admin.search') }}" class="admin-search" role="search">
    <label for="admin-search-input" class="sr-only">Поиск по админ панели</label>
    <input
        id="admin-search-input"
        type="search"
        name="q"
        value="{{ request('q', '') }}"
        placeholder="Поиск: пользователи, авторы, книги"
    >
</form>
