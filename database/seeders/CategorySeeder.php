<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Одежда и аксессуары',
            'Электроника и гаджеты',
            'Бытовая техника',
            'Товары для дома и интерьера',
            'Косметика и уход',
            'Товары для детей',
            'Автотовары',
            'Спорт и активный отдых',
            'Продукты питания',
            'Товары для животных',
            'Хобби и творчество',
            'Книги и медиа',
            'Товары для сада и огорода',
            'Здоровье и красота',
            'Умный дом и технологии',
            'Канцелярия и офис',
        ];

        foreach ($categories as $name) {
            Category::query()->firstOrCreate(['name' => $name]);
        }
    }
}
