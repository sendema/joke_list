<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Payment;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    // показать форму логина
    public function showLoginForm()
    {
        return view('login');
    }

    // обработка входа
    public function login(Request $request)
    {
        // валидация данных
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // попытка аутентификации
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            // проверяем активную подписку через сервис
            $activeSubscription = $this->subscriptionService->getActiveSubscription($user->id);

            if (!$activeSubscription) {
                return redirect()->route('subscription.form')
                    ->with('info', 'Для доступа к контенту необходима подписка');
            }

            // если подписка активна, перенаправляем на страницу с шутками
            return redirect('/jokes')
                ->with('success', 'Добро пожаловать!');
        }

        // если логин неудачен, возвращаем с ошибкой
        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->onlyInput('email');
    }

    // обработка выхода
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login')->with('success', 'Вы вышли из системы.');
    }
}
