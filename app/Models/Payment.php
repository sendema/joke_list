<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'amount',
        'currency',
        'status',
        'description',
        'user_id',
        'confirmation_url',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    // Возможные статусы платежа
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_CANCELED = 'canceled';

    // Отношение к пользователю
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Проверка, успешен ли платёж
    public function isSuccessful()
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    // Проверка, отменён ли платёж
    public function isCanceled()
    {
        return $this->status === self::STATUS_CANCELED;
    }

    // Проверка, ожидает ли платёж подтверждения
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Получить отформатированную сумму с валютой
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    // Область видимости для получения платежей пользователя
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Область видимости для успешных платежей
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCEEDED);
    }
}
