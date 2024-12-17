<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Queue\RabbitMQ\RabbitMQService;
use App\Mail\WelcomeMail;
use App\Mail\SubscriptionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ConsumeEmailsCommand extends Command
{
    protected $signature = 'rabbitmq:consume-emails {queue}';
    protected $description = 'Consume emails from RabbitMQ queue';

    private RabbitMQService $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService)
    {
        parent::__construct();
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle()
    {
        $queue = $this->argument('queue');

        $this->info("Started consuming messages from {$queue}");

        $this->rabbitMQService->consume($queue, function ($data) use ($queue) {
            switch ($queue) {
                case 'registration_emails':
                    // находим пользователя по ID
                    $user = \App\Models\User::find($data['id']);
                    if ($user) {
                        Mail::to($data['email'])->send(new WelcomeMail($user));
                        Log::info('Welcome email sent', ['email' => $data['email']]);
                    }
                    break;

                case 'subscription_emails':
                    try {
                        Log::info('Отправка письма о подписке:', $data);
                        Mail::to($data['email'])->send(new SubscriptionMail($data));
                        Log::info('Письмо о подписке отправлено:', ['email' => $data['email']]);
                    } catch (\Exception $e) {
                        Log::error('Ошибка отправки письма о подписке:', [
                            'email' => $data['email'],
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                    break;
            }
        });
    }
}
