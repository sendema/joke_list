<!DOCTYPE html>
<html>
<head>
    <title>Информация о подписке</title>
</head>
<body>
<h1>Здравствуйте, {{ $data['name'] }}!</h1>

@if(isset($data['type']) && $data['type'] === 'cancellation')
    <p>Ваша подписка была отменена {{ $data['cancelled_at'] }}</p>
@elseif(isset($data['type']) && $data['type'] === 'renewal_started')
    <p>Начат процесс продления подписки</p>
    <p>Тип подписки: {{ $data['subscription_type'] }}</p>
    <p>Сумма: {{ $data['amount'] }} {{ $data['currency'] }}</p>
    <p>Для оплаты перейдите по <a href="{{ $data['confirmation_url'] }}">ссылке</a></p>
@else
    <p>Информация о вашей подписке:</p>
    <p>Тип подписки: {{ $data['subscription_type'] }}</p>
    <p>Действует до: {{ $data['expires_at'] }}</p>
@endif
</body>
</html>
