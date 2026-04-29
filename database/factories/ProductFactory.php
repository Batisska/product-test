<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public const array RUSSIAN_PRODUCTS = [
        'Одежда и аксессуары' => [
            ['name' => 'Зимняя куртка'],
            ['name' => 'Джинсы классические'],
            ['name' => 'Шерстяной шарф'],
            ['name' => 'Кожаные перчатки'],
            ['name' => 'Спортивные кроссовки'],
        ],
        'Электроника и гаджеты' => [
            ['name' => 'Смартфон Pro Max'],
            ['name' => 'Беспроводные наушники'],
            ['name' => 'Умные часы'],
            ['name' => 'Портативная колонка'],
            ['name' => 'Быстрая зарядка 65W'],
        ],
        'Бытовая техника' => [
            ['name' => 'Холодильник Side-by-Side'],
            ['name' => 'Стиральная машина'],
            ['name' => 'Микроволновая печь'],
            ['name' => 'Робот-пылесос'],
            ['name' => 'Электрочайник'],
        ],
        'Товары для дома и интерьера' => [
            ['name' => 'Набор керамической посуды'],
            ['name' => 'Плед вязаный'],
            ['name' => 'Декоративные подушки'],
            ['name' => 'Настольная лампа'],
            ['name' => 'Ковер в гостиную'],
        ],
        'Косметика и уход' => [
            ['name' => 'Увлажняющий крем для лица'],
            ['name' => 'Шампунь для волос'],
            ['name' => 'Парфюмерная вода'],
            ['name' => 'Маска для лица'],
            ['name' => 'Гель для душа'],
        ],
        'Товары для детей' => [
            ['name' => 'Детская коляска 2 в 1'],
            ['name' => 'Конструктор LEGO'],
            ['name' => 'Мягкий мишка 100 см'],
            ['name' => 'Детский велосипед'],
            ['name' => 'Набор для рисования'],
        ],
        'Автотовары' => [
            ['name' => 'Автомобильный аккумулятор'],
            ['name' => 'Моторное масло 5W-30'],
            ['name' => 'Зимние шины R17'],
            ['name' => 'Автомобильный пылесос'],
            ['name' => 'Багажник на крышу'],
        ],
        'Спорт и активный отдых' => [
            ['name' => 'Фитнес-браслет'],
            ['name' => 'Гантели 5 кг'],
            ['name' => 'Горный велосипед'],
            ['name' => 'Палатка туристическая'],
            ['name' => 'Йога-мат'],
        ],
        'Продукты питания' => [
            ['name' => 'Зеленый чай'],
            ['name' => 'Мед натуральный'],
            ['name' => 'Итальянская паста'],
            ['name' => 'Оливковое масло'],
            ['name' => 'Кофе в зернах'],
        ],
        'Товары для животных' => [
            ['name' => 'Корм для кошек'],
            ['name' => 'Игрушка для собак'],
            ['name' => 'Когтеточка'],
            ['name' => 'Аквариумный фильтр'],
            ['name' => 'Поводок для выгула'],
        ],
        'Хобби и творчество' => [
            ['name' => 'Акварельные краски'],
            ['name' => 'Акустическая гитара'],
            ['name' => 'Набор для вышивания'],
            ['name' => 'Пластилин детский'],
            ['name' => 'Фотоаппарат моментальной печати'],
        ],
        'Книги и медиа' => [
            ['name' => 'Роман Достоевского'],
            ['name' => 'Учебник по PHP'],
            ['name' => 'Научно-популярный журнал'],
            ['name' => 'Аудиокнига по саморазвитию'],
            ['name' => 'Подписка на стриминг'],
        ],
        'Товары для сада и огорода' => [
            ['name' => 'Садовый шланг 30 м'],
            ['name' => 'Набор семян овощей'],
            ['name' => 'Газонокосилка электрическая'],
            ['name' => 'Теплица парниковая'],
            ['name' => 'Садовая мебель'],
        ],
        'Здоровье и красота' => [
            ['name' => 'Витаминный комплекс'],
            ['name' => 'Электрическая зубная щетка'],
            ['name' => 'Тонометр'],
            ['name' => 'Массажер для шеи'],
            ['name' => 'Бальзам для губ'],
        ],
        'Умный дом и технологии' => [
            ['name' => 'Умная лампочка'],
            ['name' => 'Видеодомофон'],
            ['name' => 'Датчик движения'],
            ['name' => 'Умная розетка'],
            ['name' => 'Робот-пылесос'],
        ],
        'Канцелярия и офис' => [
            ['name' => 'Набор шариковых ручек'],
            ['name' => 'Ежедневник датированный'],
            ['name' => 'Органайзер для документов'],
            ['name' => 'Степлер офисный'],
            ['name' => 'Бумага для принтера А4'],
        ],
    ];

    public function definition(): array
    {
        $categoryName = $this->faker->randomElement(array_keys(self::RUSSIAN_PRODUCTS));
        $product = $this->faker->randomElement(self::RUSSIAN_PRODUCTS[$categoryName]);
        $category = Category::firstOrCreate(['name' => $categoryName]);

        return [
            'name' => $product['name'],
            'price' => $this->faker->randomFloat(2, 10, 1000000),
            'category_id' => $category->id,
            'in_stock' => $this->faker->boolean(),
            'rating' => $this->faker->randomFloat(1, 0, 5),
        ];
    }

    public function forCategory(string $categoryName): static
    {
        return $this->state(function (array $attributes) use ($categoryName) {
            if (! isset(self::RUSSIAN_PRODUCTS[$categoryName])) {
                return [];
            }

            $product = $this->faker->randomElement(self::RUSSIAN_PRODUCTS[$categoryName]);
            $category = Category::firstOrCreate(['name' => $categoryName]);

            return [
                'name' => $product['name'],
                'price' => $product['price'],
                'category_id' => $category->id,
            ];
        });
    }
}
