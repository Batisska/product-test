<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        $total = 100000;
        $chunkSize = 5000;
        $now = now();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $chunk = [];
            $currentChunkSize = min($chunkSize, $total - $i);

            for ($j = 0; $j < $currentChunkSize; $j++) {
                $attributes = Product::factory()->make()->getAttributes();

                $attributes['created_at'] = $now;
                $attributes['updated_at'] = $now;

                $chunk[] = $attributes;
            }

            Product::query()->insert($chunk);
        }
    }
}
