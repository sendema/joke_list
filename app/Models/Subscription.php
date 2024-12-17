<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'started_at',
        'expires_at',
        'payment_id'
    ];

    protected $dates = [
        'started_at',
        'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function isActive()
    {
        return $this->expires_at > now();
    }
}

