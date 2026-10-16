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

it('allows guest to view cart', function () {
    auth()->logout();

    $this->get(route('cart.index'))->assertOk();
});

it('can add product to cart', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $this->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertRedirect(route('cart.index'));

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
});

it('can increment quantity when adding existing product', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 3,
    ])->assertRedirect(route('cart.index'));

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);
});

it('cannot add inactive product to cart', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'status' => 'inactive',
        'stock' => 10,
    ]);

    $this->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSessionHasErrors('product_id');
});

it('cannot add out-of-stock product to cart', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'type' => 'physical',
        'stock' => 0,
    ]);

    $this->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSessionHasErrors('product_id');
});

it('can update cart item quantity', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $item = CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->patch(route('cart.update', $item), ['quantity' => 5])
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseHas('cart_items', [
        'id' => $item->id,
        'quantity' => 5,
    ]);
});

it('can remove cart item', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $item = CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->delete(route('cart.destroy', $item))
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
});

it('cannot update another users cart item', function () {
    $other = User::factory()->create();
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $item = CartItem::create([
        'user_id' => $other->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->patch(route('cart.update', $item), ['quantity' => 3])
        ->assertForbidden();
});

it('cannot remove another users cart item', function () {
    $other = User::factory()->create();
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $item = CartItem::create([
        'user_id' => $other->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->delete(route('cart.destroy', $item))
        ->assertForbidden();
});

it('ajax add to cart returns json', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $this->postJson(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->assertJson([
        'success' => true,
        'message' => 'Item added to cart.',
    ]);
});

it('buy now redirects to checkout', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $this->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
        '_redirect' => 'checkout',
    ])->assertRedirect(route('checkout.loading'));
});

it('cart page shows items', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Test Product',
        'price' => 49.99,
        'sale_price' => null,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->get(route('cart.index'))
        ->assertSuccessful()
        ->assertSee('Test Product')
        ->assertSee('49.99');
});

it('drawer returns cart items as json for authenticated user', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Drawer Product',
        'price' => 19.99,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $this->get(route('cart.drawer'))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'count' => 3,
        ])
        ->assertJsonPath('items.0.name', 'Drawer Product')
        ->assertJsonPath('items.0.quantity', 3)
        ->assertJsonPath('items.0.is_session', false);
});

it('drawer returns session cart items for guest', function () {
    auth()->logout();

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Guest Drawer Product',
        'price' => 9.99,
    ]);

    session(['cart' => [
        ['product_id' => $product->id, 'quantity' => 2, 'variant' => null],
    ]]);

    $this->get(route('cart.drawer'))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'count' => 2,
        ])
        ->assertJsonPath('items.0.name', 'Guest Drawer Product')
        ->assertJsonPath('items.0.quantity', 2)
        ->assertJsonPath('items.0.is_session', true);
});

it('can update session cart quantity', function () {
    auth()->logout();

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 9.99,
    ]);

    session(['cart' => [
        ['product_id' => $product->id, 'quantity' => 2, 'variant' => null],
    ]]);

    $this->patchJson(route('cart.update-session', $product->id), ['quantity' => 5])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'cart_count' => 5,
        ]);

    $this->assertEquals(5, session('cart.0.quantity'));
});

it('validates session cart quantity bounds', function () {
    auth()->logout();

    $product = Product::factory()->create(['category_id' => $this->category->id]);

    session(['cart' => [
        ['product_id' => $product->id, 'quantity' => 1, 'variant' => null],
    ]]);

    $this->patchJson(route('cart.update-session', $product->id), ['quantity' => 0])
        ->assertStatus(422);
});

it('update cart item quantity returns json for ajax requests', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $item = CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->patchJson(route('cart.update', $item), ['quantity' => 4])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'cart_count' => 4,
        ]);
});

it('returns cart panel fragment as json for ajax requests', function () {
    $this->getJson(route('cart.index'))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'free_shipping_qualifies' => false,
        ])
        ->assertJsonStructure(['html']);
});

it('cart panel fragment includes cart items', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Fragment Product',
        'price' => 25.50,
    ]);

    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->getJson(route('cart.index'));

    expect($response->json('success'))->toBeTrue()
        ->and($response->json('html'))->toContain('Fragment Product');
});

it('cart panel fragment works for guests', function () {
    auth()->logout();

    $response = $this->getJson(route('cart.index'));

    expect($response->json('success'))->toBeTrue()
        ->and($response->json('html'))->toContain('Your cart is empty');
});

it('cart page exposes selected free shipping product ids', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Freebie',
        'price' => 39.99,
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

    $content = $this->get(route('cart.index'))
        ->assertSuccessful()
        ->getContent();

    expect($content)
        ->toContain('type: "specific_products"')
        ->toContain('productIds: ['.$product->id.']');
});
