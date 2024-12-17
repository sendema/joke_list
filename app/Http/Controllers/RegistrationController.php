<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use App\Services\Queue\RabbitMQ\RabbitMQService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    private $rabbitMQService;
    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->rabbitMQService = $rabbitMQService;
    }
    // показать форму регистрации
    public function showRegistrationForm()
    {
        return view('register');
    }

    // обработка регистрации
    public function register(Request $request)
    {
        // валидация данных
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
        ]);

        // создаем пользователя
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // хэшируем пароль
            'phone' => $validated['phone'],
        ]);

        // Отправляем данные в RabbitMQ вместо прямой отправки письма
        $this->rabbitMQService->publishMessage('registration_emails', [
            'email' => $user->email,
            'name' => $user->name,
            'id' => $user->id
        ]);
        return redirect('/login')->with('success', 'Регистрация успешно завершена!');

    }
}
