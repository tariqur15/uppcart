<?php

use App\Models\CustomerMessage;
use App\Models\Order;
use App\Models\User;

function createOrderFor(User $user, string $status = 'pending'): Order
{
    return Order::create([
        'user_id' => $user->id,
        'status' => $status,
        'total_price' => 500,
        'payment_status' => 'unpaid',
        'address' => '123 Test St, Dhaka',
    ]);
}

test('customers index defaults to ordering customers only', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $ordering = User::factory()->create();
    createOrderFor($ordering);

    $chatOnly = User::factory()->create();
    CustomerMessage::create([
        'user_id' => $chatOnly->id,
        'direction' => 'incoming',
        'message' => 'hi',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.customers.index'));

    $response->assertOk()
        ->assertSee($ordering->name)
        ->assertDontSee($chatOnly->name);
});

test('customers index chat view shows live chat customers', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $ordering = User::factory()->create();
    createOrderFor($ordering);

    $chatOnly = User::factory()->create();
    CustomerMessage::create([
        'user_id' => $chatOnly->id,
        'direction' => 'incoming',
        'message' => 'hi',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.customers.index', ['view' => 'chat']));

    $response->assertOk()
        ->assertSee($chatOnly->name)
        ->assertDontSee($ordering->name);
});

test('customers index all view shows both ordering and chat customers', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $ordering = User::factory()->create();
    createOrderFor($ordering);

    $chatOnly = User::factory()->create();
    CustomerMessage::create([
        'user_id' => $chatOnly->id,
        'direction' => 'incoming',
        'message' => 'hi',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.customers.index', ['view' => 'all']));

    $response->assertOk()
        ->assertSee($ordering->name)
        ->assertSee($chatOnly->name);
});
