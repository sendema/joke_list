<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap');

        body {
            background: #1a1a1a;
            background-image:
                linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('/api/placeholder/1920/1080');
            background-size: cover;
            color: #fff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .spotlight {
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center,
            rgba(255, 217, 0, 0.1) 0%,
            rgba(255, 217, 0, 0) 40%);
            animation: rotateSpotlight 20s infinite linear;
            pointer-events: none;
        }

        @keyframes rotateSpotlight {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-family: 'Permanent Marker', cursive;
            font-size: 3.5em;
            color: #ff4444;
            text-shadow:
                0 0 10px rgba(255, 68, 68, 0.7),
                3px 3px 0 #000;
            margin-bottom: 30px;
            text-align: center;
            animation: stageLights 2s infinite alternate;
        }

        @keyframes stageLights {
            0% { text-shadow: 0 0 10px rgba(255, 68, 68, 0.7), 3px 3px 0 #000; }
            100% { text-shadow: 0 0 20px rgba(255, 68, 68, 0.9), 3px 3px 0 #000; }
        }

        form {
            background: rgba(30, 30, 30, 0.9);
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 500px;
            box-shadow:
                0 0 30px rgba(255, 68, 68, 0.3),
                inset 0 0 50px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .mic-icon {
            position: absolute;
            top: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: #ff4444;
            transform: rotate(45deg);
            opacity: 0.1;
        }

        label {
            display: block;
            margin: 20px 0 5px;
            font-family: 'Arial', sans-serif;  /* Изменено на Arial */
            color: #ff9900;
            font-size: 1.2em;
            text-shadow: 1px 1px 2px #000;
            font-weight: bold;  /* Добавлен жирный шрифт */
        }

        input {
            width: 100%;
            padding: 12px;
            background: #333;
            border: 2px solid #ff4444;
            border-radius: 8px;
            color: #fff;
            font-size: 1.1em;
            transition: all 0.3s ease;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;  /* Явно указан шрифт */
        }

        input::placeholder {
            font-family: 'Arial', sans-serif;  /* Явно указан шрифт для плейсхолдера */
            opacity: 0.7;
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 15px rgba(255, 68, 68, 0.5);
            background: #444;
        }
        button {
            width: 100%;
            padding: 15px;
            margin-top: 30px;
            background: #ff4444;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1.3em;
            font-family: 'Arial', sans-serif;  /* Изменено на Arial */
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
            font-weight: bold;  /* Добавлен жирный шрифт */
        }

        button:hover {
            background: #ff6666;
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(255, 68, 68, 0.5);
        }

        button::after {
            content: '🎤';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .error-messages {
            background: rgba(255, 0, 0, 0.2);
            border-left: 5px solid #ff4444;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-family: 'Arial', sans-serif;  /* Явно указан шрифт */
        }

        .error-messages ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .error-messages li {
            color: #ff9999;
            margin: 5px 0;
            font-size: 0.9em;
        }

        @media (max-width: 600px) {
            form {
                padding: 20px;
            }

            h1 {
                font-size: 2.5em;
            }
        }
    </style>
</head>
<body>
<div class="spotlight"></div>
<h1>🎤 Регистрация</h1>
@if ($errors->any())
    <div class="error-messages">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('register') }}" method="POST">
    @csrf
    <div class="mic-icon"></div>
    <div>
        <label for="name">Имя:</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required
               placeholder="Введите ваше имя">
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required
               placeholder="your.name@example.com">
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required
               placeholder="Введите пароль">
    </div>
    <div>
        <label for="password_confirmation">Подтверждение пароля:</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required
               placeholder="Повторите пароль">
    </div>
    <div>
        <label for="phone">Телефон:</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
               placeholder="+7 (___) ___--">
    </div>
    <button type="submit">Зарегистрироваться</button>
</form>
</body>
</html>
