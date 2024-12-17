<?php

namespace Database\Seeders;

use App\Models\Joke;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JokeSeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->first()->id;

        $jokes = [
            [
                'title' => 'Программист и лифт',
                'description' => 'Программист заходит в лифт, а там написано L1, L2, L3... Думает: "Ага, кэш..."',
                'id_category' => DB::table('categories')->where('slug', 'programming')->first()->id,
                'rating' => 4.25,
                'user_id' => $userId,
            ],
            [
                'title' => 'Хэллоуин и Рождество',
                'description' => 'Почему программисты путают Хэллоуин и Рождество? Потому что OCT 31 = DEC 25',
                'id_category' => DB::table('categories')->where('slug', 'programming')->first()->id,
                'rating' => 3.85,
                'user_id' => $userId,
            ],
            [
                'title' => 'Рекурсия',
                'description' => 'Как объяснить рекурсию программисту? - Для начала объясните ему рекурсию.',
                'id_category' => DB::table('categories')->where('slug', 'programming')->first()->id,
                'rating' => 5.00,
                'user_id' => $userId,
            ],
            [
                'title' => 'Опоздание на работу',
                'description' => 'Начальник спрашивает: "Почему вы опоздали?" - "Пробки!" - "А почему не вышли пораньше?" - "Так рано пробок еще не было!"',
                'id_category' => DB::table('categories')->where('slug', 'work')->first()->id,
                'rating' => 4.50,
                'user_id' => $userId,
            ],
            [
                'title' => 'Студент на экзамене',
                'description' => 'Студент приходит на экзамен. Преподаватель: "Берите билет". Студент: "А может я лучше пешком пойду?"',
                'id_category' => DB::table('categories')->where('slug', 'students')->first()->id,
                'rating' => 3.75,
                'user_id' => $userId,
            ]
        ];

        foreach ($jokes as $joke) {
            $joke['created_at'] = now();
            $joke['updated_at'] = now();
            DB::table('jokes')->insert($joke);
        }

        // создаем дополнительные случайные шутки
        //Joke::factory()->count(10)->create();
    }
}
