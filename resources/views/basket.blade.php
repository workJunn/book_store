<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - Книжный Мир</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo:hover {
            color: #5568d3;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #667eea;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 0.5rem 0.8rem;
            font-size: 0.9rem;
        }

        main {
            flex: 1;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            color: white;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* 🛒 Сетка корзины */
        .cart-wrapper {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Список товаров */
        .cart-items {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .cart-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 2px solid #e0e0e0;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr;
            gap: 1rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-info {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .item-image {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .item-details h3 {
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .item-details p {
            color: #666;
            font-size: 0.9rem;
        }

        .item-price {
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
            padding: 0.3rem;
            width: fit-content;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2rem;
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .quantity-btn:hover {
            background: #667eea;
            color: white;
        }

        .quantity-value {
            font-weight: 600;
            min-width: 40px;
            text-align: center;
            font-size: 1.1rem;
        }

        .item-subtotal {
            font-weight: 700;
            color: #333;
            font-size: 1.2rem;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-size: 1.5rem;
            transition: transform 0.3s;
            padding: 0.5rem;
        }

        .remove-btn:hover {
            transform: scale(1.2);
            color: #dc2626;
        }

        /* Блок итогов заказа */
        .cart-summary {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: #666;
        }

        .summary-row.total {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            border-top: 2px solid #e0e0e0;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .summary-row .label {
            font-weight: 500;
        }

        .summary-row .value {
            font-weight: 600;
        }

        .discount-code {
            margin: 1.5rem 0;
        }

        .discount-code label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .discount-input-wrapper {
            display: flex;
            gap: 0.5rem;
        }

        .discount-input-wrapper input {
            flex: 1;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
        }

        .discount-input-wrapper input:focus {
            outline: none;
            border-color: #667eea;
        }

        .checkout-btn {
            width: 100%;
            padding: 1.2rem;
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .continue-shopping:hover {
            color: #5568d3;
            text-decoration: underline;
        }

        /* 📦 Пустая корзина */
        .empty-cart {
            background: white;
            border-radius: 15px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .empty-cart-icon {
            font-size: 6rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }

        .empty-cart h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .empty-cart p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .empty-cart .btn {
            margin: 0 0.5rem;
        }

        /* 📱 Адаптивность */
        @media (max-width: 968px) {
            .cart-wrapper {
                grid-template-columns: 1fr;
            }

            .cart-summary {
                position: static;
            }

            .cart-header {
                display: none;
            }

            .cart-item {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 2rem 0;
            }

            .item-info {
                grid-column: 1;
            }

            .item-price,
            .quantity-control,
            .item-subtotal,
            .remove-btn {
                justify-self: start;
            }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            nav ul {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .cart-items,
            .cart-summary {
                padding: 1.5rem;
            }

            .item-image {
                width: 60px;
                height: 80px;
            }
        }

        /* Уведомление */
        .notification {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #10b981;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            display: none;
            animation: slideIn 0.3s ease;
            z-index: 1000;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification.show {
            display: block;
        }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('home') }}" class="logo">📚 Книжный Мир</a>
        
        <nav>
            <ul>
                <li><a href="{{ route('home') }}">Главная</a></li>
                <li><a href="#">Каталог</a></li>
                <li><a href="#">Новинки</a></li>
                <li><a href="#">Акции</a></li>
            </ul>
        </nav>
        
        <div class="auth-buttons">
            @guest
                <a href="{{ route('User_login') }}" class="btn btn-secondary">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
            @endauth
        </div>
    </header>

    <main>
        <div class="container">
            <h1 class="page-title">🛒 Ваша корзина</h1>

            <!-- ✅ Корзина с товарами -->
            <div class="cart-wrapper">
                <!-- Список товаров -->
                <div class="cart-items">
                    <div class="cart-header">
                        <div>Товар</div>
                        <div>Цена</div>
                        <div>Количество</div>
                        <div>Сумма</div>
                        <div></div>
                    </div>

                    <!-- Товар 1 -->
                    <div class="cart-item" data-id="1">
                        <div class="item-info">
                            <img src="https://via.placeholder.com/80x100/667eea/ffffff?text=Книга+1" 
                                 alt="Мастер и Маргарита" 
                                 class="item-image">
                            <div class="item-details">
                                <h3>Мастер и Маргарита</h3>
                                <p>Михаил Булгаков</p>
                            </div>
                        </div>
                        <div class="item-price">890 ₽</div>
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity(1, -1)">−</button>
                            <span class="quantity-value">2</span>
                            <button class="quantity-btn" onclick="updateQuantity(1, 1)">+</button>
                        </div>
                        <div class="item-subtotal">1 780 ₽</div>
                        <button class="remove-btn" onclick="removeItem(1)" title="Удалить">🗑️</button>
                    </div>

                    <!-- Товар 2 -->
                    <div class="cart-item" data-id="2">
                        <div class="item-info">
                            <img src="https://via.placeholder.com/80x100/764ba2/ffffff?text=Книга+2" 
                                 alt="Преступление и наказание" 
                                 class="item-image">
                            <div class="item-details">
                                <h3>Преступление и наказание</h3>
                                <p>Фёдор Достоевский</p>
                            </div>
                        </div>
                        <div class="item-price">750 ₽</div>
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity(2, -1)">−</button>
                            <span class="quantity-value">1</span>
                            <button class="quantity-btn" onclick="updateQuantity(2, 1)">+</button>
                        </div>
                        <div class="item-subtotal">750 ₽</div>
                        <button class="remove-btn" onclick="removeItem(2)" title="Удалить">🗑️</button>
                    </div>

                    <!-- Товар 3 -->
                    <div class="cart-item" data-id="3">
                        <div class="item-info">
                            <img src="https://via.placeholder.com/80x100/667eea/ffffff?text=Книга+3" 
                                 alt="Атомные привычки" 
                                 class="item-image">
                            <div class="item-details">
                                <h3>Атомные привычки</h3>
                                <p>Джеймс Клир</p>
                            </div>
                        </div>
                        <div class="item-price">1 200 ₽</div>
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity(3, -1)">−</button>
                            <span class="quantity-value">1</span>
                            <button class="quantity-btn" onclick="updateQuantity(3, 1)">+</button>
                        </div>
                        <div class="item-subtotal">1 200 ₽</div>
                        <button class="remove-btn" onclick="removeItem(3)" title="Удалить">🗑️</button>
                    </div>

                    <!-- Продолжить покупки -->
                    <div style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e0e0e0;">
                        <a href="#" class="continue-shopping">← Продолжить покупки</a>
                    </div>
                </div>

                <!-- Итоги заказа -->
                <div class="cart-summary">
                    <h2 class="summary-title">📦 Итого</h2>
                    
                    <div class="summary-row">
                        <span class="label">Товары (4):</span>
                        <span class="value">3 730 ₽</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Скидка:</span>
                        <span class="value" style="color: #10b981;">−370 ₽</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Доставка:</span>
                        <span class="value">Бесплатно</span>
                    </div>

                    <!-- Промокод -->
                    <div class="discount-code">
                        <label for="promo">Промокод:</label>
                        <div class="discount-input-wrapper">
                            <input type="text" id="promo" placeholder="Введите промокод">
                            <button class="btn btn-secondary btn-sm" onclick="applyPromo()">Применить</button>
                        </div>
                    </div>

                    <div class="summary-row total">
                        <span class="label">Итого:</span>
                        <span class="value" style="color: #667eea; font-size: 1.5rem;">3 360 ₽</span>
                    </div>

                    <form action="#" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary checkout-btn">
                            Оформить заказ →
                        </button>
                    </form>

                    <a href="{{ route('home') }}" class="continue-shopping">
                        ← Продолжить покупки
                    </a>

                    <!-- Безопасность -->
                    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0; text-align: center;">
                        <p style="color: #999; font-size: 0.85rem; margin-bottom: 0.5rem;">
                            🔒 Безопасная оплата
                        </p>
                        <p style="color: #999; font-size: 0.85rem;">
                            ✓ Гарантия возврата 14 дней
                        </p>
                    </div>
                </div>
            </div>

            <!-- ❌ Пустая корзина (показать, если товаров нет) -->
            <!-- 
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Ваша корзина пуста</h2>
                <p>Добавьте книги из каталога, чтобы оформить заказ</p>
                <div>
                    <a href="#" class="btn btn-primary">Перейти в каталог</a>
                    <a href="{{ route('home') }}" class="btn btn-secondary">На главную</a>
                </div>
            </div>
            -->
        </div>
    </main>

    <!-- Уведомление -->
    <div id="notification" class="notification">
        Товар удалён из корзины
    </div>

    <script>
        // Обновление количества
        function updateQuantity(itemId, change) {
            const item = document.querySelector(`.cart-item[data-id="${itemId}"]`);
            const quantityElement = item.querySelector('.quantity-value');
            let quantity = parseInt(quantityElement.textContent);
            
            quantity += change;
            
            if (quantity < 1) {
                if (confirm('Удалить этот товар из корзины?')) {
                    removeItem(itemId);
                }
                return;
            }
            
            quantityElement.textContent = quantity;
            
            // Обновление суммы
            const price = parseInt(item.querySelector('.item-price').textContent.replace(/\D/g, ''));
            const subtotal = quantity * price;
            item.querySelector('.item-subtotal').textContent = subtotal.toLocaleString('ru-RU') + ' ₽';
            
            // Обновление итогов
            updateTotals();
            
            // Здесь можно отправить AJAX запрос на сервер
            console.log(`Update item ${itemId} to quantity ${quantity}`);
        }

        // Удаление товара
        function removeItem(itemId) {
            const item = document.querySelector(`.cart-item[data-id="${itemId}"]`);
            item.style.opacity = '0';
            item.style.transform = 'translateX(-100px)';
            item.style.transition = 'all 0.3s';
            
            setTimeout(() => {
                item.remove();
                updateTotals();
                showNotification();
                
                // Проверка на пустую корзину
                const items = document.querySelectorAll('.cart-item');
                if (items.length === 0) {
                    setTimeout(() => {
                        location.reload(); // Показываем пустую корзину
                    }, 500);
                }
            }, 300);
            
            // Здесь можно отправить AJAX запрос на сервер
            console.log(`Remove item ${itemId}`);
        }

        // Обновление итогов
        function updateTotals() {
            const items = document.querySelectorAll('.cart-item');
            let totalItems = 0;
            let totalPrice = 0;
            
            items.forEach(item => {
                const quantity = parseInt(item.querySelector('.quantity-value').textContent);
                const price = parseInt(item.querySelector('.item-price').textContent.replace(/\D/g, ''));
                totalItems += quantity;
                totalPrice += quantity * price;
            });
            
            // Обновление отображения (здесь можно добавить логику)
            console.log(`Total items: ${totalItems}, Total price: ${totalPrice}`);
        }

        // Применение промокода
        function applyPromo() {
            const promoInput = document.getElementById('promo');
            const promo = promoInput.value.trim();
            
            if (promo) {
                alert(`Промокод "${promo}" применён! Скидка 10%`);
                // Здесь можно отправить AJAX запрос на сервер
            } else {
                alert('Введите промокод');
            }
        }

        // Показ уведомления
        function showNotification() {
            const notification = document.getElementById('notification');
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Cart loaded');
        });
    </script>
</body>
</html>