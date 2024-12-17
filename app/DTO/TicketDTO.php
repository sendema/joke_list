<?php

namespace App\DTO;

class TicketDTO
{
    public string $name;
    public string $email;
    public string $phone;
    public string $message;
    public string $priority;

    public function __construct(string $name, string $email, string $phone, string $message, string $priority)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->message = $message;
        $this->priority = $priority;
    }
}
