<!DOCTYPE html>
<html>
<head>
    <title>Добро пожаловать!</title>
</head>
<body>
<h1>Здравствуйте, {{ $user->name }}!</h1>
<p>Добро пожаловать на наш сайт. Мы рады, что вы присоединились к нам!</p>

<p>Ваши данные для входа:</p>
<ul>
    <li>Email: {{ $user->email }}</li>
</ul>

<p>С уважением,<br>Команда {{ config('app.name') }}</p>
</body>
</html>
