<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // связь с шутками
    public function jokes(): HasMany
    {
        return $this->hasMany(Joke::class);
    }

    // связь с выступлениями пользователя
    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class);
    }

    // связь с текущей подпиской
    public function subscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('expires_at', '>', now())
            ->latest();
    }

    // связь со всеми подписками пользователя
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // связь с платежами
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // вспомогательные методы
    public function hasActiveSubscription()
    {
        return $this->subscription()->exists();
    }
    public function getSubscriptionExpiryDate()
    {
        return $this->subscription?->expires_at;
    }
    public function subscriptionType()
    {
        return $this->subscription?->type;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
