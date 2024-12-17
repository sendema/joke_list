<?php

namespace App\Services;

use App\DTO\TicketDTO;
use App\Models\Ticket;

class TicketService
{
    public function createTicket(TicketDTO $ticketDTO): Ticket
    {
        return Ticket::create([
            'name' => $ticketDTO->name,
            'email' => $ticketDTO->email,
            'phone' => $ticketDTO->phone,
            'message' => $ticketDTO->message,
            'priority' => $ticketDTO->priority,
            'status' => 'new',
            'user_id' => auth()->id()
        ]);
    }

    public function updateTicketYougileId(Ticket $ticket, string $yougileTicketId): Ticket
    {
        $ticket->update(['yougile_ticket_id' => $yougileTicketId]);
        return $ticket->fresh();
    }
}
