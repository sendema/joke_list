<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\Queue\RabbitMQ\RabbitMQService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    private $subscriptionService;
    private $rabbitMQService;

    public function __construct(
        SubscriptionService $subscriptionService,
        RabbitMQService $rabbitMQService
    )
    {
        $this->subscriptionService = $subscriptionService;
        $this->rabbitMQService = $rabbitMQService;
    }

    // показ формы выбора подписки
    public function showSubscriptionForm()
    {
        $activeSubscription = $this->subscriptionService->getActiveSubscription(Auth::id());

        if ($activeSubscription) {
            return redirect()->route('jokes.index')
                ->with('info', 'У вас уже есть активная подписка до ' .
                    $activeSubscription->expires_at->format('d.m.Y'));
        }

        return view('subscription.create', [
            'subscriptionTypes' => $this->subscriptionService->getSubscriptionTypes()
        ]);
    }

    // получение информации о текущей подписке
    public function current()
    {
        $subscription = $this->subscriptionService->getActiveSubscription(Auth::id());

        return view('subscription.current', compact('subscription'));
    }

    // отмена подписки
    public function cancel()
    {
        $this->subscriptionService->cancelSubscription(Auth::id());
        return redirect()->back()->with('success', 'Подписка успешно отменена');
    }

    // продление подписки
    public function renew(Request $request)
    {
        $result = $this->subscriptionService->createRenewalPayment(
            Auth::id(),
            $request->input('subscription_type')
        );

        if ($result) {
            $user = Auth::user();
            $this->rabbitMQService->publishMessage('subscription_emails', [
                'email' => $user->email,
                'name' => $user->name,
                'subscription_type' => $request->input('subscription_type'),
                'expires_at' => $result->expires_at
            ]);
        }

        return $result;
    }
}
