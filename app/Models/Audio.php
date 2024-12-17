<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Audio extends Model
{
    /**
     * Указываем имя таблицы
     *
     * @var string
     */
    protected $table = 'audios';

    /**
     * Поля, доступные для массового заполнения
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path'
    ];

    /**
     * Получить все выступления, использующие данный аудиофайл
     */
    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class);
    }

    /**
     * Получить URL аудиофайла
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
