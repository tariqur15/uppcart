<?php

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->category = Category::factory()->create();
});

it('allows guest checkout', function () {
    auth()->logout();

    $this->get(route('checkout.index'))->assertSuccessful();
});

it('shows empty cart message when cart is empty', function () {
    $this->get(route('checkout.index'))
        ->assertOk()
        ->assertSee('empty');
});

it('can place order', function () {
    Setting::where('key', 'shipping_zones')->delete();
    Setting::set('free_shipping_type', 'none');

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 29.99,
        'stock' => 10,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => $this->user->phone,
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur, Dhaka',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'user_id' => $this->user->id,
        'total_price' => 89.97 + 60,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'delivery_option' => 'inside_dhaka',
        'delivery_charge' => 60,
        'payment_method' => 'cod',
    ]);

    $this->assertDatabaseHas('order_items', [
        'product_id' => $product->id,
        'price' => 29.99,
        'quantity' => 3,
    ]);
});

it('deducts stock after order', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 10.00,
        'stock' => 5,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur, Dhaka',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
});

it('clears cart after order', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 10.00,
        'stock' => 5,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur, Dhaka',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ]);

    $this->assertDatabaseMissing('cart_items', [
        'user_id' => $this->user->id,
    ]);
});

it('validates full_address is required on checkout', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'stock' => 5,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => '',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ])->assertSessionHasErrors('full_address');
});

it('validates full_address minimum length', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'stock' => 5,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'ab',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ])->assertSessionHasErrors('full_address');
});

it('cannot checkout with insufficient stock', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'stock' => 1,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur, Dhaka',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ])->assertStatus(422);
});

it('checkout page shows items and total', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Checkout Product',
        'price' => 15.50,
        'sale_price' => null,
        'stock' => 10,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 4,
    ]);

    $this->get(route('checkout.index'))
        ->assertSuccessful()
        ->assertSee('Checkout Product');
});

it('checkout page renders free shipping progress bar slot with unlocked state', function () {
    Setting::set('free_shipping_threshold', '50');

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 29.99,
        'sale_price' => null,
        'stock' => 10,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $content = $this->get(route('checkout.index'))
        ->assertSuccessful()
        ->getContent();

    expect($content)
        ->toContain('fs-bar-slot')
        ->toContain('shipping-progress')
        ->toContain('freeShippingThreshold: 50')
        ->toContain('freeShippingQualifies: true')
        ->toContain('weight:');
});

it('checkout page passes below-threshold free shipping data', function () {
    Setting::set('free_shipping_threshold', '500');

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 29.99,
        'sale_price' => null,
        'stock' => 10,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $content = $this->get(route('checkout.index'))
        ->assertSuccessful()
        ->getContent();

    expect($content)
        ->toContain('freeShippingThreshold: 500')
        ->toContain('freeShippingQualifies: false')
        ->toContain('freeShippingType: "order_amount"');
});

it('applies free delivery when cart weight is within the maximum', function () {
    Setting::set('free_shipping_type', 'product_weight');
    Setting::set('free_shipping_weight_threshold', '2');

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 77.77,
        'sale_price' => null,
        'stock' => 10,
        'weight' => 1.5,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur, Dhaka',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'total_price' => 77.77,
        'delivery_charge' => 0,
        'delivery_option' => 'inside_dhaka',
    ]);
});

it('charges delivery when cart weight exceeds the maximum', function () {
    Setting::set('free_shipping_type', 'product_weight');
    Setting::set('free_shipping_weight_threshold', '2');

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 63.63,
        'sale_price' => null,
        'stock' => 10,
        'weight' => 1.5,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur, Dhaka',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'total_price' => 187.26,
        'delivery_charge' => 60,
        'delivery_option' => 'inside_dhaka',
    ]);
});

it('applies free delivery when only selected products are in cart', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 88.88,
        'sale_price' => null,
        'stock' => 10,
    ]);

    Setting::set('free_shipping_type', 'specific_products');
    Setting::set('free_shipping_products', json_encode([$product->id]));

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur, Dhaka',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'total_price' => 88.88,
        'delivery_charge' => 0,
        'delivery_option' => 'inside_dhaka',
    ]);
});

it('charges delivery when a non-selected product is in cart', function () {
    $selected = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 45.45,
        'sale_price' => null,
        'stock' => 10,
    ]);

    $nonSelected = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 33.33,
        'sale_price' => null,
        'stock' => 10,
    ]);

    Setting::set('free_shipping_type', 'specific_products');
    Setting::set('free_shipping_products', json_encode([$selected->id]));

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $selected->id,
        'quantity' => 1,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $nonSelected->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur, Dhaka',
        'delivery_option' => 'inside_dhaka',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'total_price' => 138.78,
        'delivery_charge' => 60,
        'delivery_option' => 'inside_dhaka',
    ]);
});

it('charges flat rate delivery from a shipping zone', function () {
    Setting::set('free_shipping_type', 'none');
    Setting::set('shipping_zones', json_encode([
        [
            'name' => 'Inside Dhaka',
            'regions' => ['Dhaka'],
            'methods' => [
                ['type' => 'flat_rate', 'title' => 'Standard', 'description' => '', 'enabled' => true, 'amount' => 70],
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 20.00,
        'sale_price' => null,
        'stock' => 10,
        'weight' => 1.0,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Road 5, Mirpur',
        'delivery_option' => 'z0m0',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'total_price' => 90.00,
        'delivery_charge' => 70,
        'delivery_option' => 'z0m0',
    ]);
});

it('charges weight based delivery from a shipping zone', function () {
    Setting::set('free_shipping_type', 'none');
    Setting::set('shipping_zones', json_encode([
        [
            'name' => 'Nationwide',
            'regions' => ['All'],
            'methods' => [
                ['type' => 'weight_based', 'title' => 'Weighted', 'description' => '', 'enabled' => true, 'amount' => 20, 'per_kg' => 10],
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 50.00,
        'sale_price' => null,
        'stock' => 10,
        'weight' => 3.0,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Khulna',
        'district' => 'Khulna',
        'full_address' => 'House 1, Khulna',
        'delivery_option' => 'z0m0',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'total_price' => 180.00,
        'delivery_charge' => 80,
        'delivery_option' => 'z0m0',
    ]);
});

it('charges nothing for a free shipping method in a zone', function () {
    Setting::set('free_shipping_type', 'none');
    Setting::set('shipping_zones', json_encode([
        [
            'name' => 'Dhaka',
            'regions' => ['Dhaka'],
            'methods' => [
                ['type' => 'free', 'title' => 'Free', 'description' => '', 'enabled' => true],
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 40.00,
        'sale_price' => null,
        'stock' => 10,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Dhaka',
        'district' => 'Dhaka',
        'full_address' => 'House 12, Mirpur',
        'delivery_option' => 'z0m0',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'total_price' => 40.00,
        'delivery_charge' => 0,
        'delivery_option' => 'z0m0',
    ]);
});

it('matches a zone by upazila (thana)', function () {
    Setting::set('free_shipping_type', 'none');
    Setting::set('shipping_zones', json_encode([
        [
            'name' => 'Khulna Zones',
            'regions' => ['Patuakhali', 'Galachipa'],
            'methods' => [
                ['type' => 'flat_rate', 'title' => 'Coastal', 'description' => '', 'enabled' => true, 'amount' => 45],
            ],
        ],
        [
            'name' => 'Rest',
            'regions' => ['All'],
            'methods' => [
                ['type' => 'flat_rate', 'title' => 'Standard', 'description' => '', 'enabled' => true, 'amount' => 25],
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'price' => 60.00,
        'sale_price' => null,
        'stock' => 10,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.store'), [
        'first_name' => 'Rahat',
        'last_name' => 'Hasan',
        'phone' => '01712345678',
        'division' => 'Barishal',
        'district' => 'Patuakhali',
        'thana' => 'Galachipa',
        'full_address' => 'House 1, Galachipa',
        'delivery_option' => 'z0m0',
        'payment_method' => 'cod',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'total_price' => 105.00,
        'delivery_charge' => 45,
        'delivery_option' => 'z0m0',
    ]);
});
