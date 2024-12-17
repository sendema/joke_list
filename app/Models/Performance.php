<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Performance extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'script',
        'audio_id',
        'video_id',
        'user_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // связь с пользователем (автором шутки)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // получить аудио файл, связанный с выступлением
    public function audio(): BelongsTo
    {
        return $this->belongsTo(Audio::class);
    }

    // получить видео файл, связанный с выступлением
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    // получить шутки, включенные в выступление
    public function jokes(): BelongsToMany
    {
        return $this->belongsToMany(Joke::class, 'performance_joke')
            ->withPivot('order')
            ->orderBy('performance_joke.order')
            ->withTimestamps();
    }
}

