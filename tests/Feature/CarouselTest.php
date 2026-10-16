<?php

use App\Models\Category;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;

test('homepage flash sale renders as a carousel', function () {
    $category = Category::factory()->create();

    $sale = FlashSale::create([
        'name' => 'Carousel Sale',
        'status' => App\Models\FlashSale::STATUS_ACTIVE,
        'starts_at' => now()->subHour(),
        'ends_at' => now()->addDay(),
    ]);

    Product::factory()->count(3)->create([
        'category_id' => $category->id,
        'status' => 'active',
        'variants_enabled' => false,
    ])->each(function (Product $product) use ($sale) {
        FlashSaleProduct::create([
            'flash_sale_id' => $sale->id,
            'product_id' => $product->id,
            'scope' => 'whole',
            'regular_price' => $product->price,
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'flash_price' => $product->price * 0.8,
            'sale_stock' => 10,
            'sold' => 2,
        ]);
    });

$this->get(route('home'))
        ->assertOk()
        ->assertSee('flashCarousel(3, 2)', false)
        ->assertSee('snap-mandatory', false)
        ->assertSee('x-ref="track"', false)
        ->assertSee('@click="prev()"', false)
        ->assertSee('@click="next()"', false)
        ->assertSee('left in stock', false);
});
