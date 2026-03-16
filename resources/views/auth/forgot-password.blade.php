<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля - Книжный Мир</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
        .logo { text-align: center; margin-bottom: 2rem; }
        .logo h1 { color: #667eea; font-size: 2rem; margin-bottom: 0.5rem; }
        .logo p { color: #666; font-size: 0.95rem; line-height: 1.5; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            background: #667eea;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn:hover {
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
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link a:hover { text-decoration: underline; }
        .form-group.error input {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            border: 1px solid #86efac;
        }
        header {
            display: flex;
            align-items: center;
            padding: 1.5rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
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
        .header-logo:hover { color: #5568d3; }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('home') }}" class="header-logo">📚 Книжный Мир</a>
    </header>

    <main>
        <div class="login-container">
            <div class="logo">
                <h1>🔐 Сброс пароля</h1>
                <p>Введите ваш email, и мы отправим инструкцию по восстановлению доступа</p>
            </div>

            <!-- Сообщение об успехе -->
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            <form method="post" action="{{ route('password.email') }}" autocomplete="off" novalidate>
                @csrf

                <!-- Поле Email -->
                <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Введите ваш email"   autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Кнопка отправки -->
                <button type="submit" class="btn">Отправить ссылку</button>
            </form>
        </div>
    </main>
</body>
</html>