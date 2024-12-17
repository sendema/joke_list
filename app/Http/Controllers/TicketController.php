<?php

namespace App\Http\Controllers;

use App\DTO\TicketDTO;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Services\YougileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private TicketService $ticketService;
    private YougileService $yougileService;

    public function __construct(TicketService $ticketService, YougileService $yougileService)
    {
        $this->ticketService= $ticketService;
        $this->yougileService = $yougileService;
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high'
        ]);

        $ticketDTO = new TicketDTO(
            $validated['name'],
            $validated['email'],
            $validated['phone'] ?? null,
            $validated['message'],
            $validated['priority']
        );

        $ticket = $this->ticketService->createTicket($ticketDTO);

        $yougileData = $this->yougileService->createYougileTicket($ticket, $ticketDTO);

        $updatedTicket = $this->ticketService->updateTicketYougileId($ticket, $yougileData['id']);

        return response()->json([
            'success' => true,
            'message' => 'Ваше обращение успешно создано!',
            'data' => [
                'ticket' => $updatedTicket,
                'yougile' => $yougileData,
            ],
        ], 201);
    }
}
