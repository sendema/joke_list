<?php

namespace App\Services;

use App\DTO\TicketDTO;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;

class YougileService
{
    private string $url;
    private string $token;

    public function __construct()
    {
        $this->url = config('services.yougile.url', 'https://ru.yougile.com/api-v2');
        $this->token = config('services.yougile.token');
    }

    public function createYougileTicket(Ticket $ticket, TicketDTO $ticketDTO): array
    {
        $response = Http::withToken($this->token)
            ->post("{$this->url}/tasks", [
                'title' => "Тикет #{$ticket->id} от {$ticketDTO->name}",
                'description' => $this->formatDescription($ticketDTO)
            ]);

        return $response->json();
    }

    private function formatDescription(TicketDTO $ticketDTO): string
    {
        return sprintf(
            "Информация о запросе:\n\n" .
            "Имя: %s\n" .
            "Email: %s\n" .
            "Телефон: %s\n" .
            "Приоритет: %s\n\n" .
            "Сообщение:\n%s",
            $ticketDTO->name,
            $ticketDTO->email,
            $ticketDTO->phone ?? 'Не указан',
            $ticketDTO->priority,
            $ticketDTO->message
        );
    }
}
