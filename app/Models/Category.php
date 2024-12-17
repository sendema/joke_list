<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
   protected $fillable = [
       'name',    // название категории
       'slug'     // URL-friendly название
   ];

    // связь с шутками
    public function jokes(): HasMany
    {
        return $this->hasMany(Joke::class, 'id_category');
    }
}
