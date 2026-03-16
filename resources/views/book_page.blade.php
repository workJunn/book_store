<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мастер и Маргарита - Книжный Мир</title>
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

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        main {
            flex: 1;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* 🔝 Хлебные крошки */
        .breadcrumbs {
            color: white;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .breadcrumbs a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .breadcrumbs a:hover {
            opacity: 1;
        }

        .breadcrumbs span {
            margin: 0 0.5rem;
            opacity: 0.6;
        }

        /* 📖 Основной блок книги */
        .book-detail {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        .book-detail-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        /* Изображение книги */
        .book-cover-wrapper {
            position: relative;
        }

        .book-cover {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .book-badges {
            position: absolute;
            top: 1rem;
            left: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-new {
            background: #10b981;
            color: white;
        }

        .badge-sale {
            background: #ef4444;
            color: white;
        }

        /* Информация о книге */
        .book-info h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .book-author {
            color: #667eea;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .book-rating-large {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stars-large {
            color: #fbbf24;
            font-size: 1.5rem;
        }

        .rating-text {
            color: #666;
            font-size: 0.95rem;
        }

        .book-description-full {
            color: #555;
            line-height: 1.8;
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        /* Характеристики книги */
        .book-specs {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .book-specs h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
        }

        .spec-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .spec-label {
            color: #666;
            font-weight: 500;
        }

        .spec-value {
            color: #333;
            font-weight: 600;
        }

        /* Цена и кнопки */
        .book-actions {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
        }

        .price-block {
            display: flex;
            flex-direction: column;
        }

        .current-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .old-price {
            font-size: 1.3rem;
            color: #999;
            text-decoration: line-through;
        }

        .discount-percent {
            background: #ef4444;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-left: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex: 1;
            justify-content: flex-end;
        }

        /* 📝 Секция комментариев */
        .reviews-section {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        /* Форма добавления комментария */
        .review-form {
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .rating-input {
            display: flex;
            gap: 0.5rem;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .star {
            color: #ddd;
            transition: color 0.2s;
        }

        .star.active,
        .star:hover {
            color: #fbbf24;
        }

        /* Список комментариев */
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .review-item {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }

        .reviewer-name {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }

        .review-date {
            color: #999;
            font-size: 0.85rem;
        }

        .review-rating {
            color: #fbbf24;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .review-text {
            color: #555;
            line-height: 1.7;
        }

        .review-helpful {
            margin-top: 1rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .helpful-btn {
            background: none;
            border: none;
            color: #667eea;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: color 0.3s;
        }

        .helpful-btn:hover {
            color: #5568d3;
        }

        /* 📚 Похожие книги */
        .related-books {
            margin-top: 2rem;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .related-book {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            cursor: pointer;
        }

        .related-book:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .related-book img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .related-book-info {
            padding: 1rem;
        }

        .related-book-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.3rem;
            font-size: 0.95rem;
        }

        .related-book-author {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .related-book-price {
            color: #667eea;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* 📱 Адаптивность */
        @media (max-width: 968px) {
            .book-detail-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .book-cover-wrapper {
                max-width: 300px;
                margin: 0 auto;
            }

            .book-info h1 {
                font-size: 1.5rem;
            }

            .book-actions {
                flex-direction: column;
                text-align: center;
            }

            .action-buttons {
                justify-content: center;
                width: 100%;
            }

            .specs-grid {
                grid-template-columns: 1fr;
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

            .book-detail,
            .reviews-section {
                padding: 1.5rem;
            }

            .current-price {
                font-size: 2rem;
            }
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
            <!-- Хлебные крошки -->
            <div class="breadcrumbs">
                <a href="{{ route('home') }}">Главная</a>
                <span>/</span>
                <a href="#">Каталог</a>
                <span>/</span>
                <a href="#">Фантастика</a>
                <span>/</span>
                <span>Мастер и Маргарита</span>
            </div>

            <!-- Основной блок книги -->
            <div class="book-detail">
                <div class="book-detail-grid">
                    <!-- Левая колонка: Изображение -->
                    <div class="book-cover-wrapper">
                        <div class="book-badges">
                            <span class="badge badge-new">Новинка</span>
                            <span class="badge badge-sale">-25%</span>
                        </div>
                        <img src="https://via.placeholder.com/350x500/667eea/ffffff?text=Мастер+и+Маргарита" 
                             alt="Мастер и Маргарита" 
                             class="book-cover">
                    </div>

                    <!-- Правая колонка: Информация -->
                    <div class="book-info">
                        <h1>Мастер и Маргарита</h1>
                        <p class="book-author">Михаил Булгаков</p>
                        
                        <div class="book-rating-large">
                            <span class="stars-large">★★★★★</span>
                            <span class="rating-text">4.9 из 5 (124 отзыва)</span>
                        </div>

                        <div class="book-description-full">
                            <p>
                                «Мастер и Маргарита» — самый известный роман Михаила Булгакова, 
                                над которым он работал последние годы своей жизни. Это философское 
                                произведение о вечных вопросах добра и зла, любви и преданности, 
                                искусства и власти.
                            </p>
                            <br>
                            <p>
                                В Москве 1930-х годов появляется таинственный профессор Воланд со 
                                своей свитой, которая устраивает настоящий переполох. Параллельно 
                                разворачивается история любви Мастера и Маргариты, а также события 
                                древней Иудеи, связанные с Понтием Пилатом и Иешуа Га-Ноцри.
                            </p>
                        </div>

                        <!-- Характеристики -->
                        <div class="book-specs">
                            <h3>📋 Характеристики</h3>
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <span class="spec-label">Издательство:</span>
                                    <span class="spec-value">АСТ</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Год издания:</span>
                                    <span class="spec-value">2024</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Количество страниц:</span>
                                    <span class="spec-value">480</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Переплёт:</span>
                                    <span class="spec-value">Твёрдый</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">ISBN:</span>
                                    <span class="spec-value">978-5-17-123456-7</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Язык:</span>
                                    <span class="spec-value">Русский</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Размеры:</span>
                                    <span class="spec-value">130×200 мм</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Вес:</span>
                                    <span class="spec-value">450 г</span>
                                </div>
                            </div>
                        </div>

                        <!-- Цена и кнопки -->
                        <div class="book-actions">
                            <div class="price-block">
                                <div>
                                    <span class="current-price">890 ₽</span>
                                    <span class="discount-percent">-25%</span>
                                </div>
                                <span class="old-price">1 200 ₽</span>
                            </div>
                            <div class="action-buttons">
                                <form action="#" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-large">
                                        🛒 В корзину
                                    </button>
                                </form>
                                <button class="btn btn-secondary btn-large">
                                    ❤️ В избранное
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Секция комментариев -->
            <div class="reviews-section">
                <h2 class="section-title">💬 Отзывы читателей (124)</h2>

                <!-- Форма добавления отзыва -->
                @auth
                    <div class="review-form">
                        <h3 style="margin-bottom: 1rem; color: #333;">Оставить отзыв</h3>
                        <form action="#" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Ваша оценка:</label>
                                <div class="rating-input">
                                    <span class="star" data-rating="1">★</span>
                                    <span class="star" data-rating="2">★</span>
                                    <span class="star" data-rating="3">★</span>
                                    <span class="star" data-rating="4">★</span>
                                    <span class="star" data-rating="5">★</span>
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="0">
                            </div>
                            <div class="form-group">
                                <label for="review-text">Ваш отзыв:</label>
                                <textarea id="review-text" name="text" placeholder="Поделитесь своими впечатлениями о книге..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Отправить отзыв</button>
                        </form>
                    </div>
                @else
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; text-align: center; margin-bottom: 2rem;">
                        <p style="color: #666; margin-bottom: 1rem;">Чтобы оставить отзыв, пожалуйста, <a href="{{ route('User_login') }}" style="color: #667eea; font-weight: 600;">войдите</a> или <a href="{{ route('register') }}" style="color: #667eea; font-weight: 600;">зарегистрируйтесь</a></p>
                    </div>
                @endauth

                <!-- Список отзывов -->
                <div class="reviews-list">
                    <!-- Отзыв 1 -->
                    <div class="review-item">
                        <div class="review-header">
                            <span class="reviewer-name">Александр Петров</span>
                            <span class="review-date">15 февраля 2025</span>
                        </div>
                        <div class="review-rating">★★★★★</div>
                        <p class="review-text">
                            Шедевр русской литературы! Булгаков создал невероятное произведение, 
                            которое хочется перечитывать снова и снова. Каждая глава открывает 
                            новые смыслы. Особенно впечатлила история Мастера и Маргариты — 
                            это настоящая история любви сквозь время и обстоятельства.
                        </p>
                        <div class="review-helpful">
                            <button class="helpful-btn">
                                👍 Полезно (24)
                            </button>
                            <button class="helpful-btn">
                                👎 Пожаловаться
                            </button>
                        </div>
                    </div>

                    <!-- Отзыв 2 -->
                    <div class="review-item">
                        <div class="review-header">
                            <span class="reviewer-name">Елена Соколова</span>
                            <span class="review-date">10 февраля 2025</span>
                        </div>
                        <div class="review-rating">★★★★★</div>
                        <p class="review-text">
                            Книга превзошла все ожидания! Издание качественное, твёрдый переплёт, 
                            хорошая бумага. Сам роман — это что-то невероятное. Воланд и его свита 
                            просто великолепны. Рекомендую всем, кто любит глубокую философскую прозу.
                        </p>
                        <div class="review-helpful">
                            <button class="helpful-btn">
                                👍 Полезно (18)
                            </button>
                            <button class="helpful-btn">
                                👎 Пожаловаться
                            </button>
                        </div>
                    </div>

                    <!-- Отзыв 3 -->
                    <div class="review-item">
                        <div class="review-header">
                            <span class="reviewer-name">Дмитрий Иванов</span>
                            <span class="review-date">5 февраля 2025</span>
                        </div>
                        <div class="review-rating">★★★★☆</div>
                        <p class="review-text">
                            Отличная книга, но не для лёгкого чтения. Нужно вчитываться, 
                            осмысливать. Много библейских аллюзий и философских размышлений. 
                            Тем не менее, это определённо стоит того. Издание хорошее, 
                            доставка быстрая.
                        </p>
                        <div class="review-helpful">
                            <button class="helpful-btn">
                                👍 Полезно (12)
                            </button>
                            <button class="helpful-btn">
                                👎 Пожаловаться
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Кнопка загрузки ещё -->
                <div style="text-align: center; margin-top: 2rem;">
                    <button class="btn btn-secondary">Загрузить ещё отзывы</button>
                </div>
            </div>

            <!-- Похожие книги -->
            <div class="related-books">
                <h2 class="section-title">📚 Похожие книги</h2>
                <div class="related-grid">
                    <div class="related-book">
                        <img src="https://via.placeholder.com/200x250/764ba2/ffffff?text=Белая+гвардия" alt="Белая гвардия">
                        <div class="related-book-info">
                            <div class="related-book-title">Белая гвардия</div>
                            <div class="related-book-author">Михаил Булгаков</div>
                            <div class="related-book-price">750 ₽</div>
                        </div>
                    </div>
                    <div class="related-book">
                        <img src="https://via.placeholder.com/200x250/667eea/ffffff?text=Собачье+сердце" alt="Собачье сердце">
                        <div class="related-book-info">
                            <div class="related-book-title">Собачье сердце</div>
                            <div class="related-book-author">Михаил Булгаков</div>
                            <div class="related-book-price">650 ₽</div>
                        </div>
                    </div>
                    <div class="related-book">
                        <img src="https://via.placeholder.com/200x250/764ba2/ffffff?text=Доктор+Живаго" alt="Доктор Живаго">
                        <div class="related-book-info">
                            <div class="related-book-title">Доктор Живаго</div>
                            <div class="related-book-author">Борис Пастернак</div>
                            <div class="related-book-price">890 ₽</div>
                        </div>
                    </div>
                    <div class="related-book">
                        <img src="https://via.placeholder.com/200x250/667eea/ffffff?text=Тихий+Дон" alt="Тихий Дон">
                        <div class="related-book-info">
                            <div class="related-book-title">Тихий Дон</div>
                            <div class="related-book-author">Михаил Шолохов</div>
                            <div class="related-book-price">1 200 ₽</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Скрипт для рейтинга звёзд -->
    <script>
        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                document.getElementById('rating-input').value = rating;
                
                // Подсветка звёзд
                document.querySelectorAll('.star').forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    </script>
</body>
</html>