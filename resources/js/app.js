import './bootstrap';

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const favoritesStorageKey = 'book_store_favorites';
let lastFocusedElement = null;

function normalizeFavoriteEntry(entry) {
    if (!entry || typeof entry !== 'object') {
        return null;
    }

    const id = String(entry.id ?? '').trim();
    const url = String(entry.url ?? '').trim();

    if (!id || !url) {
        return null;
    }

    return {
        id,
        title: String(entry.title ?? '').trim(),
        author: String(entry.author ?? '').trim(),
        price: String(entry.price ?? '0').trim() || '0',
        rating: String(entry.rating ?? '0').trim() || '0',
        image: String(entry.image ?? '').trim(),
        url,
    };
}

function announceToLiveRegion(text) {
    const liveRegion = document.getElementById('app-live-region');

    if (!liveRegion) {
        return;
    }

    liveRegion.textContent = '';

    window.setTimeout(() => {
        liveRegion.textContent = text;
    }, 10);
}

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
        const parsedFavorites = storedFavorites ? JSON.parse(storedFavorites) : [];

        if (!Array.isArray(parsedFavorites)) {
            writeFavorites([]);

            return [];
        }

        const normalizedFavorites = parsedFavorites
            .map((entry) => normalizeFavoriteEntry(entry))
            .filter(Boolean);

        if (normalizedFavorites.length !== parsedFavorites.length) {
            writeFavorites(normalizedFavorites);
        }

        return normalizedFavorites;
    } catch (error) {
        return [];
    }
}

function writeFavorites(favorites) {
    try {
        const normalizedFavorites = favorites
            .map((entry) => normalizeFavoriteEntry(entry))
            .filter(Boolean);

        window.localStorage.setItem(favoritesStorageKey, JSON.stringify(normalizedFavorites));

        return true;
    } catch (error) {
        return false;
    }
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
        favoritesContent.innerHTML = '';
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
        if (!writeFavorites(favorites)) {
            showFlashMessage('Не удалось обновить избранное', 'error');
            return;
        }
        showFlashMessage('Книга удалена из избранного');
    } else {
        favorites.unshift(favorite);
        if (!writeFavorites(favorites)) {
            showFlashMessage('Не удалось сохранить книгу в избранное', 'error');
            return;
        }
        showFlashMessage('Книга добавлена в избранное');
    }

    updateFavoritesCount();
    syncFavoriteButtons();
    renderFavoritesPage();
}

function showFlashMessage(text, type = 'success') {
    const existing = document.querySelector('.flash-message');

    if (existing) {
        existing.remove();
    }

    const message = document.createElement('div');
    message.className = `flash-message flash-message--${type}`;
    message.textContent = text;
    message.setAttribute('role', type === 'error' ? 'alert' : 'status');
    message.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
    document.body.appendChild(message);
    announceToLiveRegion(text);

    window.setTimeout(() => {
        message.remove();
    }, 2500);

    return message;
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

function getFirstValidationMessage(errors) {
    if (!errors || typeof errors !== 'object') {
        return null;
    }

    const firstMessages = Object.values(errors).find((messages) => Array.isArray(messages) && messages.length);

    return firstMessages?.[0] || null;
}

function updateBookReviewMeta(data) {
    const averageRating = document.querySelector('[data-book-average-rating]');
    if (averageRating && typeof data.average_rating !== 'undefined') {
        averageRating.textContent = `${data.average_rating} / 5`;
    }

    const roundedRating = Number(data.rounded_rating || 0);
    const ratingStars = document.querySelector('[data-book-rating-stars]');
    if (ratingStars) {
        ratingStars.textContent = Array.from({ length: 5 }, (_, index) => index < roundedRating ? '★' : '☆').join('');
    }

    const reviewsSummary = document.querySelector('[data-book-reviews-summary]');
    if (reviewsSummary && data.reviews_summary) {
        reviewsSummary.textContent = data.reviews_summary;
    }

    document.querySelectorAll('[data-reviews-count]').forEach((element) => {
        if (typeof data.reviews_count !== 'undefined') {
            element.textContent = data.reviews_count;
        }
    });
}

async function submitReviewForm(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton?.textContent;
    const checkedRating = form.querySelector('[name="rating"]:checked');

    if (!checkedRating) {
        const ratingGroup = form.querySelector('.review-stars');

        showFlashMessage('Выберите оценку книги перед публикацией отзыва.', 'error');
        ratingGroup?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        form.querySelector('[name="rating"]')?.focus();

        return;
    }

    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Сохранение...';
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        });

        const data = await response.json();

        if (!response.ok || data.error) {
            showFlashMessage(getFirstValidationMessage(data.errors) || data.message || 'Не удалось сохранить отзыв', 'error');
            return;
        }

        const reviewsList = document.querySelector('[data-reviews-list]');

        if (reviewsList) {
            reviewsList.querySelector('[data-reviews-empty]')?.remove();
            reviewsList.insertAdjacentHTML('afterbegin', data.review_html);
        }

        updateBookReviewMeta(data);
        showFlashMessage(data.message || 'Ваш отзыв сохранен.');

        const reviewText = form.querySelector('[name="review_text"]');
        if (reviewText && typeof data.review_text !== 'undefined') {
            reviewText.value = data.review_text;
        }

        form.querySelectorAll('[name="rating"]').forEach((ratingInput) => {
            ratingInput.checked = Number(ratingInput.value) === Number(data.rating);
        });

        if (submitButton) {
            submitButton.textContent = data.submit_label || 'Опубликовать отзыв';
        }
    } catch (error) {
        showFlashMessage('Ошибка сервера', 'error');
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
            if (submitButton.textContent === 'Сохранение...') {
                submitButton.textContent = originalButtonText;
            }
        }
    }
}

async function voteReview(button) {
    const loginUrl = button.dataset.loginUrl;

    if (loginUrl) {
        window.location.assign(loginUrl);
        return;
    }

    const reviewCard = button.closest('[data-review-card]');
    const voteUrl = button.dataset.reviewVoteUrl;
    const vote = button.dataset.vote;

    if (!reviewCard || !voteUrl || !vote) {
        return;
    }

    const formData = new FormData();
    formData.append('vote', vote);

    button.disabled = true;

    try {
        const response = await fetch(voteUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        const data = await response.json();

        if (!response.ok || data.error) {
            showFlashMessage(getFirstValidationMessage(data.errors) || data.message || 'Не удалось сохранить голос', 'error');
            return;
        }

        const helpfulCount = reviewCard.querySelector('[data-review-helpful-count]');
        const notHelpfulCount = reviewCard.querySelector('[data-review-not-helpful-count]');

        if (helpfulCount) {
            helpfulCount.textContent = data.helpful_count;
        }

        if (notHelpfulCount) {
            notHelpfulCount.textContent = data.not_helpful_count;
        }

        reviewCard.querySelectorAll('[data-review-vote]').forEach((voteButton) => {
            voteButton.classList.toggle('is-active', voteButton.dataset.vote === data.user_vote);
        });

        showFlashMessage(data.message || 'Ваш голос учтен.');
    } catch (error) {
        showFlashMessage('Ошибка сервера', 'error');
    } finally {
        button.disabled = false;
    }
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

function renderEmptyCart() {
    const cartContent = document.getElementById('cart-content');
    const topButtons = document.getElementById('top-buttons');

    topButtons?.remove();

    if (!cartContent) {
        return;
    }

    cartContent.innerHTML = '';
}

function openCheckoutModal() {
    const modal = document.getElementById('checkout-modal');

    if (!modal) {
        return;
    }

    lastFocusedElement = document.activeElement;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('.checkout-dialog')?.focus();
}

function closeCheckoutModal() {
    const modal = document.getElementById('checkout-modal');

    if (!modal) {
        return;
    }

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');

    if (lastFocusedElement instanceof HTMLElement) {
        lastFocusedElement.focus();
    }
}

function openDeleteUserModal() {
    const modal = document.getElementById('delete-user-modal');

    if (!modal) {
        return;
    }

    lastFocusedElement = document.activeElement;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('.checkout-dialog')?.focus();
}

function closeDeleteUserModal() {
    const modal = document.getElementById('delete-user-modal');

    if (!modal) {
        return;
    }

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');

    if (lastFocusedElement instanceof HTMLElement) {
        lastFocusedElement.focus();
    }
}

function openDeleteAuthorModal() {
    const modal = document.getElementById('delete-author-modal');

    if (!modal) {
        return;
    }

    lastFocusedElement = document.activeElement;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('.checkout-dialog')?.focus();
}

function closeDeleteAuthorModal() {
    const modal = document.getElementById('delete-author-modal');

    if (!modal) {
        return;
    }

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');

    if (lastFocusedElement instanceof HTMLElement) {
        lastFocusedElement.focus();
    }
}

function openTopUpModal() {
    const modal = document.getElementById('topup-modal');

    if (!modal) {
        return;
    }

    lastFocusedElement = document.activeElement;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('.checkout-dialog')?.focus();
    modal.querySelector('input[name="amount"]')?.focus();
}

function closeTopUpModal() {
    const modal = document.getElementById('topup-modal');

    if (!modal) {
        return;
    }

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');

    if (lastFocusedElement instanceof HTMLElement) {
        lastFocusedElement.focus();
    }
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
        const viewport = shelf.querySelector('[data-shelf-viewport]');
        const controls = Array.from(shelf.querySelectorAll('[data-shelf-direction]'));

        if (!track || !viewport || !track.children.length) {
            return;
        }

        const cards = Array.from(track.children).map((card) => card.outerHTML);
        let startIndex = 0;
        let pageSize = getShelfPageSize();
        let touchStartX = null;
        let touchCurrentX = null;
        let hasRendered = false;

        const getVisibleCount = () => Math.min(pageSize, cards.length);
        const canPaginate = () => cards.length > pageSize;

        const syncControls = () => {
            controls.forEach((button) => {
                button.disabled = !canPaginate();
                button.hidden = !canPaginate();
            });
        };

        const renderPage = (withAnimation = true) => {
            const visibleCards = [];
            const visibleCount = getVisibleCount();

            for (let index = 0; index < visibleCount; index += 1) {
                const cardIndex = (startIndex + index) % cards.length;
                visibleCards.push(cards[cardIndex]);
            }

            track.innerHTML = visibleCards.join('');
            track.style.setProperty('--shelf-columns', String(pageSize));
            syncControls();
            if (withAnimation && hasRendered) {
                track.classList.remove('is-animating');
                // Force reflow so the animation can restart on repeated renders.
                void track.offsetWidth;
                track.classList.add('is-animating');
            }
            syncFavoriteButtons();
            hasRendered = true;
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

        renderPage(false);
        window.addEventListener('resize', syncPageSize);

        controls.forEach((button) => {
            button.addEventListener('click', () => {
                if (!canPaginate()) {
                    return;
                }

                const direction = button.dataset.shelfDirection === 'next' ? 1 : -1;
                startIndex = (startIndex + direction + cards.length * 10) % cards.length;
                renderPage();
            });
        });

        viewport.addEventListener('touchstart', (event) => {
            touchStartX = event.touches[0]?.clientX ?? null;
            touchCurrentX = touchStartX;
        }, { passive: true });

        viewport.addEventListener('touchmove', (event) => {
            touchCurrentX = event.touches[0]?.clientX ?? touchCurrentX;
        }, { passive: true });

        viewport.addEventListener('touchend', () => {
            if (touchStartX === null || touchCurrentX === null) {
                touchStartX = null;
                touchCurrentX = null;
                return;
            }

            const deltaX = touchCurrentX - touchStartX;

            if (canPaginate() && Math.abs(deltaX) >= 40) {
                const direction = deltaX < 0 ? 1 : -1;
                startIndex = (startIndex + direction + cards.length * 10) % cards.length;
                renderPage();
            }

            touchStartX = null;
            touchCurrentX = null;
        }, { passive: true });
    });
}

function triggerPurchasedBookDownloads() {
    const autoDownloadLinks = document.querySelectorAll('[data-auto-download]');

    if (!autoDownloadLinks.length) {
        return;
    }

    window.setTimeout(() => {
        autoDownloadLinks.forEach((link, index) => {
            window.setTimeout(() => {
                if (link instanceof HTMLAnchorElement) {
                    link.click();
                }
            }, index * 400);
        });
    }, 600);
}

function initReviewsHub() {
    const hub = document.querySelector('[data-reviews-hub]');

    if (!hub) {
        return;
    }

    const triggers = Array.from(hub.querySelectorAll('[data-reviews-tab-trigger]'));
    const panes = Array.from(hub.querySelectorAll('[data-reviews-tab-pane]'));

    const activateTab = (tabName) => {
        triggers.forEach((trigger) => {
            const isActive = trigger.dataset.reviewsTabTrigger === tabName;
            trigger.classList.toggle('is-active', isActive);
            trigger.setAttribute('aria-selected', isActive ? 'true' : 'false');
            trigger.setAttribute('tabindex', isActive ? '0' : '-1');
        });

        panes.forEach((pane) => {
            const isActive = pane.dataset.reviewsTabPane === tabName;
            pane.classList.toggle('is-active', isActive);
            pane.hidden = !isActive;
        });
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            activateTab(trigger.dataset.reviewsTabTrigger);
        });

        trigger.addEventListener('keydown', (event) => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) {
                return;
            }

            event.preventDefault();

            const currentIndex = triggers.indexOf(trigger);
            let nextIndex = currentIndex;

            if (event.key === 'ArrowRight') {
                nextIndex = (currentIndex + 1) % triggers.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (currentIndex - 1 + triggers.length) % triggers.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = triggers.length - 1;
            }

            const nextTrigger = triggers[nextIndex];
            activateTab(nextTrigger.dataset.reviewsTabTrigger);
            nextTrigger.focus();
        });
    });

    document.querySelectorAll('[data-open-review-form]').forEach((button) => {
        button.addEventListener('click', () => {
            const reviewForm = document.getElementById('review-form-details');

            if (!reviewForm) {
                return;
            }

            reviewForm.open = true;
            reviewForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
}

function initPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const field = button.closest('.password-field');
        const input = field?.querySelector('[data-password-input]');

        if (!input) {
            return;
        }

        const showPassword = () => {
            input.type = 'text';
            button.setAttribute('aria-pressed', 'true');
        };

        const hidePassword = () => {
            input.type = 'password';
            button.setAttribute('aria-pressed', 'false');
        };

        button.addEventListener('pointerdown', (event) => {
            event.preventDefault();
            showPassword();
        });

        button.addEventListener('pointerup', hidePassword);
        button.addEventListener('pointerleave', hidePassword);
        button.addEventListener('pointercancel', hidePassword);

        button.addEventListener('keydown', (event) => {
            if (event.key === ' ' || event.key === 'Enter') {
                event.preventDefault();
                showPassword();
            }
        });

        button.addEventListener('keyup', (event) => {
            if (event.key === ' ' || event.key === 'Enter') {
                hidePassword();
            }
        });

        button.addEventListener('blur', hidePassword);
    });
}

updateFavoritesCount();
syncFavoriteButtons();
renderFavoritesPage();
initBookShelves();
initReviewsHub();
initPasswordToggles();

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

    const reviewVoteButton = event.target.closest('[data-review-vote]');
    if (reviewVoteButton) {
        voteReview(reviewVoteButton);
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

    if (event.target.closest('[data-open-user-delete]')) {
        openDeleteUserModal();
        return;
    }

    if (event.target.closest('[data-close-user-delete]')) {
        closeDeleteUserModal();
        return;
    }

    if (event.target.closest('[data-open-author-delete]')) {
        openDeleteAuthorModal();
        return;
    }

    if (event.target.closest('[data-close-author-delete]')) {
        closeDeleteAuthorModal();
        return;
    }

    if (event.target.closest('[data-open-topup]')) {
        openTopUpModal();
        return;
    }

    if (event.target.closest('[data-close-topup]')) {
        closeTopUpModal();
        return;
    }

    const modal = document.getElementById('checkout-modal');
    if (modal && event.target === modal) {
        closeCheckoutModal();
        return;
    }

    const deleteUserModal = document.getElementById('delete-user-modal');
    if (deleteUserModal && event.target === deleteUserModal) {
        closeDeleteUserModal();
        return;
    }

    const deleteAuthorModal = document.getElementById('delete-author-modal');
    if (deleteAuthorModal && event.target === deleteAuthorModal) {
        closeDeleteAuthorModal();
        return;
    }

    const topUpModal = document.getElementById('topup-modal');
    if (topUpModal && event.target === topUpModal) {
        closeTopUpModal();
    }
});

document.addEventListener('submit', (event) => {
    const reviewForm = event.target.closest('[data-review-form]');

    if (!reviewForm) {
        return;
    }

    event.preventDefault();
    submitReviewForm(reviewForm);
});

document.addEventListener('keydown', (event) => {
    const modal = document.getElementById('checkout-modal');
    const deleteUserModal = document.getElementById('delete-user-modal');
    const deleteAuthorModal = document.getElementById('delete-author-modal');
    const topUpModal = document.getElementById('topup-modal');

    const activeModal = modal?.classList.contains('is-open')
        ? modal
        : deleteUserModal?.classList.contains('is-open')
            ? deleteUserModal
            : deleteAuthorModal?.classList.contains('is-open')
                ? deleteAuthorModal
                : topUpModal?.classList.contains('is-open')
                    ? topUpModal
                : null;

    if (!activeModal) {
        return;
    }

    if (event.key === 'Escape') {
        if (activeModal === modal) {
            closeCheckoutModal();
        } else if (activeModal === topUpModal) {
            closeTopUpModal();
        } else if (activeModal === deleteAuthorModal) {
            closeDeleteAuthorModal();
        } else {
            closeDeleteUserModal();
        }
        return;
    }

    if (event.key !== 'Tab') {
        return;
    }

    const focusableElements = Array.from(
        activeModal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
    ).filter((element) => !element.hasAttribute('disabled'));

    if (!focusableElements.length) {
        return;
    }

    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    if (event.shiftKey && document.activeElement === firstElement) {
        event.preventDefault();
        lastElement.focus();
    } else if (!event.shiftKey && document.activeElement === lastElement) {
        event.preventDefault();
        firstElement.focus();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    triggerPurchasedBookDownloads();
});
