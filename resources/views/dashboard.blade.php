<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - Книжный Мир</title>
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
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-logo:hover {
            color: #5568d3;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cart-link {
            position: relative;
            text-decoration: none;
            color: #667eea;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -12px;
            background: #ef4444;
            color: white;
            font-size: 0.75rem;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            padding: 0 4px;
        }

        .profile-link {
            text-decoration: none;
            font-size: 1.7rem;
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-link:hover {
            color: #5568d3;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .profile-container {
            width: 100%;
            max-width: 500px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .profile-avatar {
            width: 110px;
            height: 110px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.35);
        }

        .profile-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.7rem;
        }

        .profile-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.95rem 1.6rem;
            border: none;
            border-radius: 8px;
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

        .logout-form {
            margin-top: 0.5rem;
        }

        .logout-form button {
            min-width: 220px;
        }

        @media (max-width: 768px) {
            header {
                padding: 1rem;
            }

            main {
                padding: 1rem;
            }

            .profile-card {
                padding: 2rem 1.2rem;
            }

            .profile-title {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('home') }}" class="header-logo">📚 Книжный Мир</a>

        <div class="header-right">
            <a href="{{ route('cart.index') }}" class="cart-link" title="Корзина">
                🛒
                <span class="cart-count">
                    {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                </span>
            </a>

            <a href="{{ route('dashboard') }}" class="profile-link" title="Профиль">
                👤
            </a>
        </div>
    </header>

    <main>
        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-avatar">👤</div>
                <h1 class="profile-title">Профиль</h1>
                <p class="profile-subtitle">Управление аккаунтом</p>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Выход из аккаунта
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>