<?php

namespace Database\Factories;

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FlashSaleProduct>
 */
class FlashSaleProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        $regular = (float) ($product->sale_price ?? $product->price);

        return [
            'flash_sale_id' => FlashSale::factory(),
            'product_id' => $product->id,
            'scope' => FlashSaleProduct::SCOPE_WHOLE,
            'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
            'discount_value' => 20,
            'regular_price' => $regular,
            'flash_price' => round($regular * 0.8, 2),
            'sale_stock' => 50,
            'sold' => 0,
            'max_per_customer' => null,
        ];
    }
}
