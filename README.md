# Book Store

Интернет-магазин книг на Laravel 12, Blade и Vite. Проект включает публичную витрину, каталог, карточку книги с отзывами, корзину, оформление и оплату заказа, личный кабинет пользователя и отдельную админ-панель.

## Возможности

- главная страница с подборками и быстрыми рейтингами
- каталог книг с категориями и фильтрацией
- поиск книги из хедера с переходом на карточку книги
- карточка книги с отзывами и рейтингом
- корзина и оформление заказа
- страница оплаты и история заказов в профиле
- регистрация, вход и восстановление пароля
- админ-панель с разделами авторов, заказов, пользователей и поиском по админке

## Стек

- PHP 8.2+
- Laravel 12
- Blade
- Vite
- Pest
- SQLite для тестов

## Запуск проекта

1. Установить зависимости:

```bash
composer install
npm install
```

2. Создать конфиг окружения:

```bash
cp .env.example .env
php artisan key:generate
```

3. Настроить базу данных в `.env`.

4. Применить миграции:

```bash
php artisan migrate
```

5. Запустить приложение:

```bash
php artisan serve
npm run dev
```

## Тесты

```bash
php artisan test
```

На текущий момент проект проходит 29 feature/unit тестов.

## Администратор

Обычная регистрация создаёт только стандартного пользователя. Доступ в админ-панель есть только у пользователей, у которых в базе уже назначена роль `admin`.

## Основные маршруты

- `/` — главная страница
- `/catalog` — каталог
- `/cart` — корзина
- `/dashboard` — профиль пользователя
- `/admin` — админ-панель

## Структура

- [routes/web.php](/home/abdullo/book_store/routes/web.php) — веб-маршруты
- [app/Http/Controllers/BookController.php](/home/abdullo/book_store/app/Http/Controllers/BookController.php) — витрина, каталог, книга, отзывы
- [app/Http/Controllers/CartController.php](/home/abdullo/book_store/app/Http/Controllers/CartController.php) — корзина, checkout, оплата
- [app/Http/Controllers/AdminController.php](/home/abdullo/book_store/app/Http/Controllers/AdminController.php) — админский интерфейс
- [tests/Feature](/home/abdullo/book_store/tests/Feature) — основные feature-тесты

## Ограничения текущей версии

- часть витринной логики сосредоточена в контроллерах
- остатки при checkout пока не защищены от конкурентных заказов на уровне блокировок БД
- фронтенд построен на Blade-шаблонах и общем CSS без выделенного дизайн-системного слоя
