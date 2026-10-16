<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;

function createGeoOrder(array $attributes = []): Order
{
    return Order::create(array_merge([
        'user_id' => User::factory()->create()->id,
        'status' => 'pending',
        'total_price' => 500,
        'payment_status' => 'unpaid',
        'address' => '123 Test St, Dhaka',
    ], $attributes));
}

test('geo returns error when order has no ip address', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = createGeoOrder();

    $this->actingAs($admin)
        ->getJson(route('admin.orders.geo', $order))
        ->assertOk()
        ->assertJson(['success' => false]);
});

test('geo rejects private and reserved ips without calling the api', function () {
    Http::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $order = createGeoOrder(['ip_address' => '127.0.0.1']);

    $this->actingAs($admin)
        ->getJson(route('admin.orders.geo', $order))
        ->assertOk()
        ->assertJson([
            'success' => false,
            'message' => 'Private or reserved IP — location cannot be determined.',
        ]);

    Http::assertNothingSent();
});

test('geo returns location data for a public ip', function () {
    Http::fake([
        'ipwho.is/*' => Http::response([
            'success' => true,
            'ip' => '8.8.8.8',
            'city' => 'San Jose',
            'region' => 'California',
            'country' => 'United States',
            'country_code' => 'US',
            'latitude' => 37.3361663,
            'longitude' => -121.8905913,
            'connection' => ['isp' => 'Google LLC', 'org' => 'Google LLC', 'asn' => 15169],
            'timezone' => ['id' => 'America/Los_Angeles'],
        ]),
    ]);

    $admin = User::factory()->create(['role' => 'admin']);
    $order = createGeoOrder(['ip_address' => '8.8.8.8']);

    $this->actingAs($admin)
        ->getJson(route('admin.orders.geo', $order))
        ->assertOk()
        ->assertJsonPath('data.isp', 'Google LLC')
        ->assertJsonPath('data.city', 'San Jose')
        ->assertJsonPath('data.latitude', 37.3361663);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'ipwho.is'));
});

test('geo supports a query string ip override for private ip orders', function () {
    Http::fake([
        'ipwho.is/*' => Http::response([
            'success' => true,
            'ip' => '123.49.0.10',
            'city' => 'Dhaka',
            'region' => 'Dhaka Division',
            'country' => 'Bangladesh',
            'latitude' => 23.7643863,
            'longitude' => 90.3890144,
            'connection' => ['isp' => 'AmberIT Limited'],
        ]),
    ]);

    $admin = User::factory()->create(['role' => 'admin']);
    $order = createGeoOrder(['ip_address' => '127.0.0.1']);

    $this->actingAs($admin)
        ->getJson(route('admin.orders.geo', ['order' => $order, 'ip' => '123.49.0.10']))
        ->assertOk()
        ->assertJsonPath('data.city', 'Dhaka');

    Http::assertSent(fn ($request) => str_contains($request->url(), '123.49.0.10'));
});
