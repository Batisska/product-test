<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public const RUSSIAN_NAMES = [
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

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(self::RUSSIAN_NAMES),
        ];
    }
}
