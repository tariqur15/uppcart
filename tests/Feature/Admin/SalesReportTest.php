<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($this->admin);

    $this->category = Category::factory()->create();
});

it('admin can view sales report page', function () {
    $this->get(route('admin.sales-reports.index'))
        ->assertSuccessful();
});

it('report shows zero metrics with no orders', function () {
    $this->get(route('admin.sales-reports.index'))
        ->assertSuccessful()
        ->assertSee('0');
});

it('report shows correct metrics with orders', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 25.00,
        'stock' => 100,
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => 50.00,
        'status' => 'completed',
        'payment_status' => 'paid',
        'address' => '123 Test St, City, State 12345',
    ]);
    $order->created_at = now()->subDay();
    $order->save();

    $order->items()->create([
        'product_id' => $product->id,
        'price' => 25.00,
        'quantity' => 2,
    ]);

    $this->get(route('admin.sales-reports.index'))
        ->assertSuccessful()
        ->assertSee('$50.00')
        ->assertSee('1');
});

it('report filters by date range', function () {
    $user = User::factory()->create();

    $oldOrder = Order::create([
        'user_id' => $user->id,
        'total_price' => 100.00,
        'status' => 'completed',
        'payment_status' => 'paid',
        'address' => '123 Test St, City, State 12345',
    ]);
    $oldOrder->created_at = now()->subMonth();
    $oldOrder->save();

    Order::create([
        'user_id' => $user->id,
        'total_price' => 200.00,
        'status' => 'completed',
        'payment_status' => 'paid',
        'address' => '123 Test St, City, State 12345',
    ]);

    $this->get(route('admin.sales-reports.index', [
        'from' => now()->subWeek()->format('Y-m-d'),
        'to' => now()->format('Y-m-d'),
    ]))->assertSuccessful()
        ->assertSee('200');
});

it('report shows top products', function () {
    $user = User::factory()->create();

    $productA = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Top Seller A',
        'price' => 10.00,
    ]);

    $productB = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Top Seller B',
        'price' => 20.00,
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => 50.00,
        'status' => 'completed',
        'payment_status' => 'paid',
        'address' => '123 Test St, City, State 12345',
    ]);

    $order->items()->create(['product_id' => $productA->id, 'price' => 10.00, 'quantity' => 3]);
    $order->items()->create(['product_id' => $productB->id, 'price' => 20.00, 'quantity' => 1]);

    $this->get(route('admin.sales-reports.index'))
        ->assertSuccessful()
        ->assertSee('Top Seller A')
        ->assertSee('Top Seller B');
});

it('guest cannot access sales reports', function () {
    auth()->logout();

    $this->get(route('admin.sales-reports.index'))->assertRedirect(route('login'));
});

it('non-admin cannot access sales reports', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $this->actingAs($user)
        ->get(route('admin.sales-reports.index'))
        ->assertForbidden();
});
