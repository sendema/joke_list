<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\SubscriptionService;
use App\Services\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $yookassaService;
    private $subscriptionService;

    public function __construct(
        YooKassaService $yookassaService,
        SubscriptionService $subscriptionService
    ) {
        $this->yookassaService = $yookassaService;
        $this->subscriptionService = $subscriptionService;
    }

    public function create(Request $request)
    {
        try {
            $result = $this->subscriptionService->createRenewalPayment(
                Auth::id(),
                $request->input('subscription_type')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Payment creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания платежа'
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        Log::info('Payment webhook received', $request->all());

        $data = $request->all();
        $result = $this->yookassaService->handleNotification($data);

        if ($result && $result['status'] === Payment::STATUS_SUCCEEDED) {
            $payment = Payment::where('payment_id', $result['payment_id'])->first();

            if ($payment) {
                $payment->update([
                    'status' => $result['status'],
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'payment_updated' => now()->toISOString()
                    ])
                ]);

                $this->subscriptionService->handleSuccessfulPayment($payment);
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 400);
    }

    public function callback(Request $request)
    {
        Log::info('Payment callback received', $request->all());

        // получаем последний платеж пользователя
        $payment = Payment::where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($payment) {
            return redirect()->route('payment.status', ['payment' => $payment->id])
                ->with('info', 'Проверяем статус платежа...');
        }

        return redirect()->route('subscription.form')
            ->with('error', 'Информация о платеже не найдена');
    }

    public function status(Payment $payment = null)
    {
        if (!$payment) {
            $payment = Payment::where('user_id', Auth::id())
                ->latest()
                ->first();
        }

        if ($payment && $payment->isPending()) {
            // получаем актуальный статус из ЮKassa
            $paymentInfo = $this->yookassaService->getPaymentInfo($payment->payment_id);
            if ($paymentInfo && $payment->status !== $paymentInfo->getStatus()) {
                $payment->update([
                    'status' => $paymentInfo->getStatus()
                ]);
                // обновляем модель после изменения
                $payment->refresh();

                if ($payment->isSuccessful()) {
                    $this->subscriptionService->handleSuccessfulPayment($payment);
                }
            }
        }

        return view('payment.status', compact('payment'));
    }
}
