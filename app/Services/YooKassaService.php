<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;

class YooKassaService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(
            config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key')
        );
    }

    public function getPaymentInfo($paymentId)
    {
        try {
            return $this->client->getPaymentInfo($paymentId);
        } catch (\Exception $e) {
            Log::error('Failed to get payment info from YooKassa', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function createPayment($amount, $description, $orderId)
    {
        return $this->client->createPayment(
            [
                'amount' => [
                    'value' => $amount,
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => route('payment.callback'),
                ],
                'capture' => true,
                'description' => $description,
                'metadata' => [
                    'order_id' => $orderId
                ]
            ],
            uniqid('', true)
        );
    }

    public function handleNotification($data)
    {
        try {
            $notification = ($data['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
                ? new NotificationSucceeded($data)
                : new NotificationWaitingForCapture($data);

            $payment = $notification->getObject();

            return [
                'payment_id' => $payment->getId(),
                'status' => $payment->getStatus(),
                'amount' => $payment->getAmount()->getValue(),
                'order_id' => $payment->getMetadata()->offsetGet('order_id'),
            ];
        } catch (\Exception $e) {
            Log::error('YooKassa notification error: ' . $e->getMessage());
            return null;
        }
    }
}
