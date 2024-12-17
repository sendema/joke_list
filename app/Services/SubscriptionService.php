<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\Queue\RabbitMQ\RabbitMQService;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    private const SUBSCRIPTION_TYPES = [
        'monthly' => [
            'amount' => 100,
            'duration' => 30,
            'description' => 'Месячная подписка'
        ],
        'yearly' => [
            'amount' => 1000,
            'duration' => 365,
            'description' => 'Годовая подписка'
        ]
    ];

    private $yookassaService;
    private $rabbitMQService;

    public function __construct(
        YooKassaService $yookassaService,
        RabbitMQService $rabbitMQService
    ) {
        $this->yookassaService = $yookassaService;
        $this->rabbitMQService = $rabbitMQService;
    }

    public function getSubscriptionTypes()
    {
        return self::SUBSCRIPTION_TYPES;
    }

    public function getActiveSubscription($userId)
    {
        return Subscription::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function createSubscription($payment)
    {
        $subscription = Subscription::updateOrCreate(
            ['user_id' => $payment->user_id],
            [
                'type' => $payment->metadata['subscription_type'],
                'started_at' => now(),
                'expires_at' => now()->addDays($payment->metadata['duration']),
                'payment_id' => $payment->id
            ]
        );

        // получаем данные пользователя для письма
        $user = $payment->user;

        // отправляем данные в очередь RabbitMQ для отправки письма
        $this->rabbitMQService->publishMessage('subscription_emails', [
            'email' => $user->email,
            'name' => $user->name,
            'subscription_type' => $payment->metadata['subscription_type'],
            'expires_at' => $subscription->expires_at->format('Y-m-d H:i:s'),
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'description' => $payment->description
        ]);

        return $subscription;
    }

    public function cancelSubscription($userId)
    {
        $subscription = Subscription::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->first();

        if ($subscription) {
            $subscription->update(['expires_at' => now()]);

            // Получаем данные пользователя
            $user = $subscription->user;

            // Отправляем данные в очередь RabbitMQ для отправки письма об отмене
            $this->rabbitMQService->publishMessage('subscription_emails', [
                'email' => $user->email,
                'name' => $user->name,
                'type' => 'cancellation',
                'subscription_type' => $subscription->type,
                'cancelled_at' => now()->format('Y-m-d H:i:s')
            ]);

            Log::info('Subscription cancelled', ['user_id' => $userId]);
        }

        return true;
    }

    public function createRenewalPayment($userId, $subscriptionType)
    {
        if (!array_key_exists($subscriptionType, self::SUBSCRIPTION_TYPES)) {
            throw new \InvalidArgumentException('Некорректный тип подписки');
        }

        $subscription = self::SUBSCRIPTION_TYPES[$subscriptionType];

        try {
            $yookassaPayment = $this->yookassaService->createPayment(
                $subscription['amount'],
                $subscription['description'],
                uniqid('order_', true)
            );

            $payment = Payment::create([
                'payment_id' => $yookassaPayment->getId(),
                'amount' => $subscription['amount'],
                'currency' => 'RUB',
                'status' => $yookassaPayment->getStatus(),
                'description' => $subscription['description'],
                'user_id' => $userId,
                'confirmation_url' => $yookassaPayment->getConfirmation()->getConfirmationUrl(),
                'metadata' => [
                    'payment_created' => now()->toISOString(),
                    'order_id' => $yookassaPayment->getMetadata()->offsetGet('order_id'),
                    'subscription_type' => $subscriptionType,
                    'duration' => $subscription['duration']
                ]
            ]);

            // Получаем данные пользователя
            $user = $payment->user;

            // Отправляем данные в очередь RabbitMQ для отправки письма о начале процесса оплаты
            $this->rabbitMQService->publishMessage('subscription_emails', [
                'email' => $user->email,
                'name' => $user->name,
                'type' => 'renewal_started',
                'subscription_type' => $subscriptionType,
                'amount' => $subscription['amount'],
                'currency' => 'RUB',
                'confirmation_url' => $payment->confirmation_url
            ]);

            return [
                'success' => true,
                'confirmation_url' => $payment->confirmation_url
            ];
        } catch (\Exception $e) {
            Log::error('Renewal payment creation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function handleSuccessfulPayment($payment)
    {
        $subscription = $this->createSubscription($payment);

        Log::info('Subscription created/updated', [
            'user_id' => $payment->user_id,
            'payment_id' => $payment->payment_id,
            'expires_at' => $subscription->expires_at
        ]);

        return $subscription;
    }
}
