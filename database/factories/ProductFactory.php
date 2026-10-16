<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500),
            'sale_price' => fake()->boolean(30) ? fake()->randomFloat(2, 5, 400) : null,
            'stock' => fake()->numberBetween(0, 100),
            'sku' => fake()->unique()->lexify('SKU-?????'),
            'type' => fake()->randomElement(['physical', 'digital']),
            'image' => null,
            'status' => 'active',
            'featured' => fake()->boolean(20),
            'brand_id' => null,
            'collection_id' => null,
        ];
    }
}
