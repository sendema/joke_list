<?php

namespace App\Services\Queue\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class RabbitMQService
{
    private $connection;
    private $channel;
    private const EXCHANGES = [
        'emails' => [
            'name' => 'emails',
            'type' => 'direct',
            'queues' => [
                'registration_emails' => 'registration', // очередь для писем регистрации по ключу маршрутизации
                'subscription_emails' => 'subscription'
            ]
        ]
    ];

    public function __construct()
    {
        try {
            $this->connection = new AMQPStreamConnection(
                config('queue.connections.rabbitmq.host', 'rabbitmq'),
                config('queue.connections.rabbitmq.port', 5672),
                config('queue.connections.rabbitmq.user', 'guest'),
                config('queue.connections.rabbitmq.password', 'guest')
            );
            $this->channel = $this->connection->channel();
            $this->declareExchanges();
        } catch (\Exception $e) {
            Log::error('RabbitMQ connection failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function declareExchanges()
    {
        foreach (self::EXCHANGES as $exchange) {
            // объявляем обменник
            $this->channel->exchange_declare(
                $exchange['name'],
                $exchange['type'],
                false,
                true,
                false
            );

            // cоздаем очереди и привязываем их к обменнику
            foreach ($exchange['queues'] as $queue => $routingKey) {
                $this->channel->queue_declare($queue, false, true, false, false);
                $this->channel->queue_bind($queue, $exchange['name'], $routingKey);
            }
        }
    }

    public function publishMessage(string $queue, array $data)
    {
        try {
            // cоздаем сообщение
            $message = new AMQPMessage(
                json_encode($data),
                ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
            );

            $exchange = '';
            $routingKey = $queue;

            // находим нужный обменник и ключ маршрутизации
            foreach (self::EXCHANGES as $ex) {
                if (isset($ex['queues'][$queue])) {
                    $exchange = $ex['name'];
                    $routingKey = $ex['queues'][$queue];
                    break;
                }
            }

            // публикуем сообщение
            $this->channel->basic_publish($message, $exchange, $routingKey);
            Log::info('Message published to RabbitMQ', ['queue' => $queue]);

        } catch (\Exception $e) {
            Log::error('Failed to publish message', [
                'queue' => $queue,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function consume(string $queue, callable $callback)
    {
        try {
            $this->channel->basic_qos(null, 1, false);

            $this->channel->basic_consume(
                $queue,
                '',
                false,
                false,
                false,
                false,
                function ($msg) use ($callback, $queue) {
                    try {
                        $data = json_decode($msg->getBody(), true);
                        $callback($data);
                        $msg->ack();
                    } catch (\Exception $e) {
                        Log::error('Error processing message', [
                            'queue' => $queue,
                            'error' => $e->getMessage()
                        ]);
                        $msg->nack(true);
                    }
                }
            );

            while ($this->channel->is_consuming()) {
                $this->channel->wait();
            }

        } catch (\Exception $e) {
            Log::error('Consumer error', [
                'queue' => $queue,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function __destruct()
    {
        $this->close();
    }
}
