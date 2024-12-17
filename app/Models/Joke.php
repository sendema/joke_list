<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Joke extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
   protected $fillable = [
       'title',         // заголовок шутки
       'description',   // текст шутки
       'id_category',   // ID категории
       'rating',        // рейтинг
       'user_id'        // ID пользователя
   ];

    /**
     * Атрибуты, которые должны быть приведены к определенным типам.
     *
     * @var array<int, string>
     */
   protected $casts = [
       'rating' => 'decimal:2',      // рейтинг как десятичное число с 2 знаками после запятой
       'created_at' => 'datetime',   // дата создания как объект DateTime
       'updated_at' => 'datetime'    // дата обновления как объект DateTime
   ];

    // связь с пользователем (автором шутки)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // связь с категорией
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category', 'id');
    }
}
