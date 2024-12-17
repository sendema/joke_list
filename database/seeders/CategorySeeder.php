<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Программирование',
                'slug' => 'programming',
            ],
            [
                'name' => 'Работа',
                'slug' => 'work',
            ],
            [
                'name' => 'Студенты',
                'slug' => 'students',
            ],
            [
                'name' => 'Семья',
                'slug' => 'family',
            ],
            [
                'name' => 'Дети',
                'slug' => 'kids',
            ]
        ];

        foreach ($categories as $category) {
            $category['created_at'] = now();
            $category['updated_at'] = now();
            DB::table('categories')->insert($category);
        }

        // создаем дополнительные случайные категории, если нужно
        //Category::factory()->count(5)->create();
    }
}
