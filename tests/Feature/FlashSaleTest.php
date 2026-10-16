<?php

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\FlashSaleService;

function adminUser()
{
    return User::factory()->create(['role' => 'admin']);
}

test('admin can access the create flash sale page', function () {
    $admin = adminUser();

    $this->actingAs($admin)->get(route('admin.flash-sales.create'))->assertOk();
});

test('admin can access the edit flash sale page', function () {
    $admin = adminUser();
    $product = Product::factory()->create(['status' => 'active']);
    $sale = FlashSale::factory()->active()->create();
    $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 15,
        'regular_price' => (float) $product->price,
        'flash_price' => round((float) $product->price * 0.85, 2),
    ]);

    $this->actingAs($admin)->get(route('admin.flash-sales.edit', $sale))->assertOk();
});

test('admin can create a flash sale with products', function () {
    $admin = adminUser();
    $productA = Product::factory()->create(['status' => 'active']);
    $productB = Product::factory()->create(['status' => 'active']);

    $payload = [
        'name' => 'Mega Monday',
        'description' => 'Test sale',
        'starts_at' => now()->addMinutes(5)->format('Y-m-d H:i:s'),
        'ends_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'status' => 'scheduled',
        'products' => json_encode([
            [
                'product_id' => $productA->id,
                'scope' => 'whole',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'sale_stock' => 50,
                'max_per_customer' => 5,
            ],
            [
                'product_id' => $productB->id,
                'scope' => 'whole',
                'discount_type' => 'fixed_price',
                'discount_value' => 9.99,
                'sale_stock' => null,
                'max_per_customer' => null,
            ],
        ]),
    ];

    $response = $this->actingAs($admin)->post(route('admin.flash-sales.store'), $payload);

    $response->assertRedirect();

    $sale = FlashSale::where('name', 'Mega Monday')->first();
    expect($sale)->not->toBeNull();
    expect($sale->status)->toBe('scheduled');

    expect($sale->products()->count())->toBe(2);

    $fa = FlashSaleProduct::where('flash_sale_id', $sale->id)->where('product_id', $productA->id)->whereNull('variant')->first();
    expect($fa->discount_type)->toBe('percentage');
    expect((float) $fa->regular_price)->toBe((float) ($productA->sale_price ?? $productA->price));
    expect((float) $fa->flash_price)->toBe(round((float) ($productA->sale_price ?? $productA->price) * 0.8, 2));

    $fb = FlashSaleProduct::where('flash_sale_id', $sale->id)->where('product_id', $productB->id)->whereNull('variant')->first();
    expect((float) $fb->flash_price)->toBe(9.99);
});

test('overlapping product in an existing active sale is rejected', function () {
    $admin = adminUser();
    $product = Product::factory()->create(['status' => 'active']);

    FlashSale::factory()->active()->create()->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 10,
        'regular_price' => (float) ($product->sale_price ?? $product->price),
        'flash_price' => round((float) ($product->sale_price ?? $product->price) * 0.9, 2),
    ]);

    $response = $this->actingAs($admin)->from(route('admin.flash-sales.create'))->post(route('admin.flash-sales.store'), [
        'name' => 'Conflicting Sale',
        'starts_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
        'status' => 'scheduled',
        'products' => json_encode([
            [
                'product_id' => $product->id,
                'scope' => 'whole',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'sale_stock' => null,
                'max_per_customer' => null,
            ],
        ]),
    ]);

    $response->assertSessionHasErrors('products');
    expect(FlashSale::where('name', 'Conflicting Sale')->exists())->toBeFalse();
});

test('admin can end an active flash sale', function () {
    $admin = adminUser();
    $sale = FlashSale::factory()->active()->create();

    $this->actingAs($admin)->post(route('admin.flash-sales.end', $sale));

    $sale->refresh();
    expect($sale->status)->toBe(FlashSale::STATUS_ENDED);
    expect($sale->ends_at)->not->toBeNull();
    expect($sale->ends_at->gte(now()->subMinute()))->toBeTrue();
});

test('admin can duplicate a flash sale into a draft', function () {
    $admin = adminUser();
    $product = Product::factory()->create(['status' => 'active']);
    $sale = FlashSale::factory()->active()->create();
    $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 15,
        'regular_price' => 100.0,
        'flash_price' => 85.0,
        'sale_stock' => 30,
    ]);

    $this->actingAs($admin)->post(route('admin.flash-sales.duplicate', $sale));

    $copy = FlashSale::where('name', $sale->name.' (Copy)')->first();
    expect($copy)->not->toBeNull();
    expect($copy->status)->toBe(FlashSale::STATUS_DRAFT);
    expect($copy->starts_at)->toBeNull();
    expect($copy->products()->count())->toBe(1);
    expect($copy->products()->first()->sold)->toBe(0);
});

test('effective price returns flash price for an active sale', function () {
    $product = Product::factory()->create(['status' => 'active', 'sale_price' => null]);
    $regular = (float) $product->price;

    $sale = FlashSale::factory()->active()->create();
    $flashProduct = $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 25,
        'regular_price' => $regular,
        'flash_price' => round($regular * 0.75, 2),
    ]);

    $service = app(FlashSaleService::class);
    $result = $service->effectivePrice($product, null, 1, $regular);

    expect($result['flash_product']?->id)->toBe($flashProduct->id);
    expect($result['price'])->toBe(round($regular * 0.75, 2));
});

test('effective price returns fallback when no active sale', function () {
    $product = Product::factory()->create(['status' => 'active', 'sale_price' => null]);

    $result = app(FlashSaleService::class)->effectivePrice($product, null, 1, 42.5);

    expect($result['flash_product'])->toBeNull();
    expect($result['price'])->toBe(42.5);
});

test('customer purchased quantity aggregates across orders', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['status' => 'active']);
    $regular = (float) $product->price;

    $sale = FlashSale::factory()->active()->create();
    $flashProduct = $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 10,
        'regular_price' => $regular,
        'flash_price' => round($regular * 0.9, 2),
    ]);

    foreach ([2, 3] as $qty) {
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'FL-'.fake()->unique()->numerify('######'),
            'total_price' => $flashProduct->flash_price * $qty,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);
        $order->items()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $qty,
            'price' => $flashProduct->flash_price,
            'original_price' => $regular,
            'flash_sale_id' => $sale->id,
            'flash_sale_product_id' => $flashProduct->id,
        ]);
    }

    $total = app(FlashSaleService::class)->customerPurchasedQuantity($sale, $flashProduct, $user->id);

    expect($total)->toBe(5);
});

test('homepage renders the flash sale section when a sale is active', function () {
    $product = Product::factory()->create(['status' => 'active', 'sale_price' => null]);
    $regular = (float) $product->price;

    $sale = FlashSale::factory()->active()->create();
    $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 25,
        'regular_price' => $regular,
        'flash_price' => round($regular * 0.75, 2),
    ]);

    $this->get(route('home'))->assertOk()->assertSee('Flash Sale');
});

test('flash sale listing page shows products on the active sale', function () {
    $product = Product::factory()->create(['status' => 'active', 'sale_price' => null]);
    $regular = (float) $product->price;

    $sale = FlashSale::factory()->active()->create();
    $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 25,
        'regular_price' => $regular,
        'flash_price' => round($regular * 0.75, 2),
    ]);

    $this->get(route('flash-sale.index'))->assertOk()->assertSee($product->name);
});

test('product page shows the flash sale banner for a product on an active sale', function () {
    $product = Product::factory()->create(['status' => 'active', 'sale_price' => null]);
    $regular = (float) $product->price;

    $sale = FlashSale::factory()->active()->create();
    $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 25,
        'regular_price' => $regular,
        'flash_price' => round($regular * 0.75, 2),
    ]);

    $this->get(route('products.show', $product))->assertOk()->assertSee('Flash Sale');
});

test('cart drawer shows the flash price for a product on an active sale', function () {
    $product = Product::factory()->create(['status' => 'active', 'sale_price' => null]);
    $regular = (float) $product->price;

    $sale = FlashSale::factory()->active()->create();
    $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 20,
        'regular_price' => $regular,
        'flash_price' => round($regular * 0.8, 2),
    ]);

    $this->postJson(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertOk();

    $response = $this->getJson(route('cart.drawer'))->assertOk();

    $unitPrice = collect($response->json('items'))->firstWhere('product_id', $product->id)['unit_price'] ?? null;
    expect((float) $unitPrice)->toBe(round($regular * 0.8, 2));
});

test('adding more than the remaining flash stock to the cart is rejected', function () {
    $product = Product::factory()->create(['status' => 'active', 'sale_price' => null, 'stock' => 100]);
    $regular = (float) $product->price;

    $sale = FlashSale::factory()->active()->create();
    $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 20,
        'regular_price' => $regular,
        'flash_price' => round($regular * 0.8, 2),
        'sale_stock' => 5,
        'sold' => 3,
        'max_per_customer' => null,
    ]);

    $this->postJson(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 3,
    ])->assertStatus(422);
});

test('adding more than the flash per-customer limit is rejected', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['status' => 'active', 'sale_price' => null, 'stock' => 100]);
    $regular = (float) $product->price;

    $sale = FlashSale::factory()->active()->create();
    $sale->products()->create([
        'product_id' => $product->id,
        'scope' => FlashSaleProduct::SCOPE_WHOLE,
        'discount_type' => FlashSaleProduct::DISCOUNT_PERCENTAGE,
        'discount_value' => 20,
        'regular_price' => $regular,
        'flash_price' => round($regular * 0.8, 2),
        'sale_stock' => 100,
        'sold' => 0,
        'max_per_customer' => 2,
    ]);

    $this->actingAs($user)
        ->postJson(route('cart.store'), ['product_id' => $product->id, 'quantity' => 3])
        ->assertStatus(422);
});
