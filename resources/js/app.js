import './bootstrap';

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const favoritesStorageKey = 'book_store_favorites';

function getHomeUrl() {
    return document.body.dataset.homeUrl || '/';
}

function updateCartCount(count) {
    document.querySelectorAll('[data-cart-count]').forEach((element) => {
        element.textContent = count;
    });
}

function readFavorites() {
    try {
        const storedFavorites = window.localStorage.getItem(favoritesStorageKey);

        return storedFavorites ? JSON.parse(storedFavorites) : [];
    } catch (error) {
        return [];
    }
}

function writeFavorites(favorites) {
    window.localStorage.setItem(favoritesStorageKey, JSON.stringify(favorites));
}

function updateFavoritesCount() {
    const count = readFavorites().length;

    document.querySelectorAll('[data-favorites-count]').forEach((element) => {
        element.textContent = count;
    });
}

function syncFavoriteButtons() {
    const favorites = readFavorites();
    const favoriteIds = new Set(favorites.map((favorite) => String(favorite.id)));

    document.querySelectorAll('[data-favorite-toggle]').forEach((button) => {
        const isFavorite = favoriteIds.has(button.dataset.bookId);
        button.classList.toggle('is-active', isFavorite);
        button.setAttribute('aria-pressed', isFavorite ? 'true' : 'false');
    });
}

function buildFavoriteFromButton(button) {
    return {
        id: button.dataset.bookId,
        title: button.dataset.bookTitle,
        author: button.dataset.bookAuthor,
        price: button.dataset.bookPrice,
        rating: button.dataset.bookRating,
        image: button.dataset.bookImage,
        url: button.dataset.bookUrl,
    };
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function renderFavoritesPage() {
    const favoritesContent = document.getElementById('favorites-content');

    if (!favoritesContent) {
        return;
    }

    const favorites = readFavorites();

    if (!favorites.length) {
        favoritesContent.innerHTML = `
            <div class="favorites-empty">
                <h2>В избранном пока пусто</h2>
                <p>Нажмите на значок закладки у книги, и она появится на этой странице.</p>
                <a href="${getHomeUrl()}" class="btn btn-primary">Перейти в каталог</a>
            </div>
        `;
        return;
    }

    favoritesContent.innerHTML = `
        <div class="favorites-grid">
            ${favorites.map((book) => `
                <article class="favorite-card">
                    <button
                        class="favorite-button favorite-button--overlay is-active"
                        type="button"
                        data-favorite-toggle
                        data-book-id="${escapeHtml(book.id)}"
                        data-book-title="${escapeHtml(book.title)}"
                        data-book-author="${escapeHtml(book.author)}"
                        data-book-price="${escapeHtml(book.price)}"
                        data-book-rating="${escapeHtml(book.rating)}"
                        data-book-image="${escapeHtml(book.image)}"
                        data-book-url="${escapeHtml(book.url)}"
                        aria-label="Убрать из избранного"
                    >
                        <span class="bookmark-icon bookmark-icon--button" aria-hidden="true"></span>
                    </button>

                    <a href="${escapeHtml(book.url)}" class="favorite-card__image-link">
                        <img src="${escapeHtml(book.image)}" class="favorite-card__image" alt="${escapeHtml(book.title)}">
                    </a>

                    <div class="favorite-card__body">
                        <a href="${escapeHtml(book.url)}" class="favorite-card__title">${escapeHtml(book.title)}</a>
                        <p class="favorite-card__author">${escapeHtml(book.author)}</p>
                        <div class="favorite-card__meta">
                            <span>${escapeHtml(book.price)} ₽</span>
                            <span>★ ${escapeHtml(book.rating)}</span>
                        </div>
                    </div>
                </article>
            `).join('')}
        </div>
    `;

    syncFavoriteButtons();
}

function toggleFavorite(button) {
    const favorite = buildFavoriteFromButton(button);
    const favorites = readFavorites();
    const favoriteIndex = favorites.findIndex((item) => String(item.id) === String(favorite.id));

    if (favoriteIndex >= 0) {
        favorites.splice(favoriteIndex, 1);
        writeFavorites(favorites);
        showFlashMessage('Книга удалена из избранного');
    } else {
        favorites.unshift(favorite);
        writeFavorites(favorites);
        showFlashMessage('Книга добавлена в избранное');
    }

    updateFavoritesCount();
    syncFavoriteButtons();
    renderFavoritesPage();
}

function showFlashMessage(text, type = 'success') {
    return undefined;
}

async function postJson(url) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            Accept: 'application/json',
        },
    });

    return response.json();
}

async function addToCart(bookId) {
    try {
        const data = await postJson(`/cart/add/${bookId}`);

        if (typeof data.cart_count !== 'undefined') {
            updateCartCount(data.cart_count);
        }

        showFlashMessage(data.message || 'Книга добавлена', data.notice ? 'error' : 'success');
    } catch (error) {
        showFlashMessage('Ошибка сервера', 'error');
    }
}

function renderEmptyCart(message = 'Добавьте книги из каталога') {
    const cartContent = document.getElementById('cart-content');
    const topButtons = document.getElementById('top-buttons');

    topButtons?.remove();

    if (!cartContent) {
        return;
    }

    cartContent.innerHTML = `
        <div class="empty-cart">
            <h2>Корзина пуста</h2>
            <p>${message}</p>
            <a href="${getHomeUrl()}" class="btn btn-primary">Перейти в каталог</a>
        </div>
    `;
}

function openCheckoutModal() {
    document.getElementById('checkout-modal')?.classList.add('is-open');
}

function closeCheckoutModal() {
    document.getElementById('checkout-modal')?.classList.remove('is-open');
}

async function updateCartItem(id, action) {
    try {
        const data = await postJson(`/cart/${action}/${id}`);

        if (typeof data.cart_count !== 'undefined') {
            updateCartCount(data.cart_count);
        }

        if (data.error) {
            showFlashMessage(data.message || 'Не удалось обновить корзину', 'error');
            return;
        }

        if (data.removed) {
            document.getElementById(`item-${id}`)?.remove();

            const cartTotal = document.getElementById('cart-total');
            if (cartTotal) {
                cartTotal.textContent = data.total;
            }

            if (!document.querySelector('.cart-item')) {
                renderEmptyCart();
            }

            return;
        }

        const qty = document.getElementById(`qty-${id}`);
        const itemTotal = document.getElementById(`item-total-${id}`);
        const cartTotal = document.getElementById('cart-total');

        if (qty) {
            qty.textContent = data.quantity;
        }

        if (itemTotal) {
            itemTotal.textContent = `${data.item_total} ₽`;
        }

        if (cartTotal) {
            cartTotal.textContent = data.total;
        }
    } catch (error) {
        showFlashMessage('Ошибка сервера', 'error');
    }
}

async function clearCart(withConfirmation = true) {
    if (withConfirmation && !window.confirm('Очистить корзину?')) {
        return;
    }

    try {
        await postJson('/cart/clear');
        updateCartCount(0);
        renderEmptyCart();
    } catch (error) {
        showFlashMessage('Ошибка сервера', 'error');
    }
}

async function confirmCheckout() {
    try {
        const data = await postJson('/cart/checkout');

        if (data.requires_auth && data.login_url) {
            window.location.assign(data.login_url);
            return;
        }

        if (data.error) {
            showFlashMessage(data.message || 'Не удалось оформить заказ', 'error');
            return;
        }

        updateCartCount(data.cart_count ?? 0);
        closeCheckoutModal();

        const cartContent = document.getElementById('cart-content');
        const topButtons = document.getElementById('top-buttons');

        topButtons?.remove();

        if (!cartContent) {
            return;
        }

        cartContent.innerHTML = `
            <div class="success-box">
                ${escapeHtml(data.message || 'Заказ успешно оформлен.')}
            </div>
            <div class="empty-cart">
                <h2>Корзина пуста</h2>
                <p>Заказ №${escapeHtml(data.order_id ?? '')} сохранен. Вы можете вернуться в каталог и выбрать новые книги.</p>
                <a href="${getHomeUrl()}" class="btn btn-primary">Перейти в каталог</a>
            </div>
        `;
    } catch (error) {
        showFlashMessage('Ошибка сервера', 'error');
    }
}

function getShelfPageSize() {
    if (window.innerWidth <= 640) {
        return 1;
    }

    if (window.innerWidth <= 860) {
        return 2;
    }

    if (window.innerWidth <= 1160) {
        return 3;
    }

    return 5;
}

function initBookShelves() {
    document.querySelectorAll('[data-book-shelf]').forEach((shelf) => {
        const track = shelf.querySelector('[data-shelf-track]');

        if (!track || !track.children.length) {
            return;
        }

        const cards = Array.from(track.children).map((card) => card.outerHTML);
        let startIndex = 0;
        let pageSize = getShelfPageSize();

        const renderPage = () => {
            const visibleCards = [];

            for (let index = 0; index < pageSize; index += 1) {
                const cardIndex = (startIndex + index) % cards.length;
                visibleCards.push(cards[cardIndex]);
            }

            track.innerHTML = visibleCards.join('');
            track.style.setProperty('--shelf-columns', String(pageSize));
            syncFavoriteButtons();
        };

        const syncPageSize = () => {
            const nextPageSize = getShelfPageSize();

            if (nextPageSize === pageSize) {
                return;
            }

            pageSize = nextPageSize;
            startIndex = startIndex % cards.length;
            renderPage();
        };

        renderPage();
        window.addEventListener('resize', syncPageSize);

        shelf.querySelectorAll('[data-shelf-direction]').forEach((button) => {
            button.addEventListener('click', () => {
                const direction = button.dataset.shelfDirection === 'next' ? 1 : -1;
                startIndex = (startIndex + direction + cards.length * 10) % cards.length;
                renderPage();
            });
        });
    });
}

function initReviewsHub() {
    const hub = document.querySelector('[data-reviews-hub]');

    if (!hub) {
        return;
    }

    const triggers = Array.from(hub.querySelectorAll('[data-reviews-tab-trigger]'));
    const panels = Array.from(hub.querySelectorAll('[data-reviews-tab-panel]'));

    const activateTab = (tabName) => {
        triggers.forEach((trigger) => {
            const isActive = trigger.dataset.reviewsTabTrigger === tabName;
            trigger.classList.toggle('is-active', isActive);
            trigger.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        panels.forEach((panel) => {
            const isActive = panel.dataset.reviewsTabPanel === tabName;
            panel.classList.toggle('is-active', isActive);
            panel.hidden = !isActive;
        });
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            activateTab(trigger.dataset.reviewsTabTrigger);
        });
    });

    document.querySelectorAll('[data-open-review-form]').forEach((button) => {
        button.addEventListener('click', () => {
            const reviewForm = document.getElementById('review-form-panel');

            if (!reviewForm) {
                return;
            }

            reviewForm.open = true;
            reviewForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
}

updateFavoritesCount();
syncFavoriteButtons();
renderFavoritesPage();
initBookShelves();
initReviewsHub();

document.addEventListener('click', (event) => {
    const favoriteButton = event.target.closest('[data-favorite-toggle]');
    if (favoriteButton) {
        toggleFavorite(favoriteButton);
        return;
    }

    const addToCartButton = event.target.closest('[data-add-to-cart]');
    if (addToCartButton) {
        addToCart(addToCartButton.dataset.addToCart);
        return;
    }

    const cartActionButton = event.target.closest('[data-cart-action]');
    if (cartActionButton) {
        updateCartItem(cartActionButton.dataset.itemId, cartActionButton.dataset.cartAction);
        return;
    }

    if (event.target.closest('[data-clear-cart]')) {
        clearCart(true);
        return;
    }

    if (event.target.closest('[data-open-checkout]')) {
        openCheckoutModal();
        return;
    }

    if (event.target.closest('[data-close-checkout]')) {
        closeCheckoutModal();
        return;
    }

    if (event.target.closest('[data-confirm-checkout]')) {
        confirmCheckout();
        return;
    }

    const modal = document.getElementById('checkout-modal');
    if (modal && event.target === modal) {
        closeCheckoutModal();
    }
});
