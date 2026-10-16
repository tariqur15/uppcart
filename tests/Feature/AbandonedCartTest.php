<?php

use App\Models\AbandonedCart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->category = Category::factory()->create();
    $this->product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 50,
        'sale_price' => null,
        'stock' => 10,
    ]);

    $this->trackPayload = [
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
    ];
});

it('creates an abandoned cart record when checkout contact info is submitted', function () {
    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
    ]);

    $this->post(route('checkout.track'), $this->trackPayload)->assertOk();

    $this->assertDatabaseHas('abandoned_carts', [
        'user_id' => $this->user->id,
        'device_id' => null,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'item_count' => 2,
        'total' => 100,
        'recovered_at' => null,
    ]);

    $cart = AbandonedCart::where('user_id', $this->user->id)->first();
    expect($cart)->not->toBeNull()
        ->and($cart->items_data)->toBeArray()
        ->and($cart->items_data[0]['product_id'])->toBe($this->product->id)
        ->and($cart->items_data[0]['quantity'])->toBe(2)
        ->and($cart->items_data[0]['image'])->toBe($this->product->image);
});

it('does not track when the phone is missing', function () {
    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.track'), ['name' => 'Rahat Hasan'])->assertSessionHasErrors('phone');

    expect(AbandonedCart::where('user_id', $this->user->id)->exists())->toBeFalse();
});

it('stores the delivery address when provided', function () {
    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.track'), [
        ...$this->trackPayload,
        'address' => 'House 12, Road 5, Mirpur, Dhaka, Dhaka',
    ])->assertOk();

    $this->assertDatabaseHas('abandoned_carts', [
        'user_id' => $this->user->id,
        'address' => 'House 12, Road 5, Mirpur, Dhaka, Dhaka',
    ]);
});

it('does not track cart additions before checkout contact info is provided', function () {
    $this->post(route('cart.store'), [
        'product_id' => $this->product->id,
        'quantity' => 2,
    ])->assertRedirect();

    expect(AbandonedCart::where('user_id', $this->user->id)->exists())->toBeFalse();
});

it('updates the abandoned cart record with the latest contact info', function () {
    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.track'), $this->trackPayload);
    $this->post(route('checkout.track'), [
        'name' => 'Rahat Hasan',
        'phone' => '01811112222',
    ]);

    expect(AbandonedCart::where('user_id', $this->user->id)->count())->toBe(1)
        ->and(AbandonedCart::where('user_id', $this->user->id)->first()->phone)->toBe('01811112222');
});

it('creates a separate abandoned cart record per device for guests', function () {
    auth()->logout();

    session(['cart' => [[
        'product_id' => $this->product->id,
        'quantity' => 1,
        'variant' => null,
        'custom_fields_data' => null,
    ]]]);

    $this->withCookie('device_id', 'guest-device-1')
        ->post(route('checkout.track'), $this->trackPayload)
        ->assertOk();

    $this->assertDatabaseHas('abandoned_carts', [
        'user_id' => null,
        'device_id' => 'guest-device-1',
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'item_count' => 1,
    ]);
});

it('deletes the abandoned cart record when the cart becomes empty', function () {
    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.track'), $this->trackPayload);

    expect(AbandonedCart::where('user_id', $this->user->id)->exists())->toBeTrue();

    CartItem::where('user_id', $this->user->id)->delete();

    $this->post(route('checkout.track'), $this->trackPayload);

    expect(AbandonedCart::where('user_id', $this->user->id)->exists())->toBeFalse();
});

it('marks the abandoned cart as recovered after checkout', function () {
    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.track'), $this->trackPayload);

    expect(AbandonedCart::where('user_id', $this->user->id)->exists())->toBeTrue();

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

    expect(AbandonedCart::where('user_id', $this->user->id)->first())
        ->recovered_at->not->toBeNull();
});

it('shows abandoned carts in the admin abandoned view', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $cart = AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'address' => 'House 12, Road 5, Mirpur, Dhaka',
        'items_data' => [[
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'quantity' => 1,
            'subtotal' => 50,
        ]],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.index', ['view' => 'abandoned']))
        ->assertOk()
        ->assertSee('Rahat Hasan')
        ->assertSee('House 12, Road 5, Mirpur, Dhaka')
        ->assertSee('wa.me', false)
        ->assertSee($cart->refresh()->coupon_code, false)
        ->assertSee('Abandoned');
});

it('generates a unique 5% discount coupon for an abandoned cart', function () {
    CartItem::create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $this->post(route('checkout.track'), $this->trackPayload)->assertOk();

    $cart = AbandonedCart::where('user_id', $this->user->id)->first();

    expect($cart->coupon_code)->not->toBeNull();

    $coupon = Coupon::where('code', $cart->coupon_code)->first();

    expect($coupon)->not->toBeNull()
        ->and($coupon->type)->toBe('percentage')
        ->and((int) $coupon->value)->toBe(5)
        ->and((int) $coupon->max_uses)->toBe(1)
        ->and($coupon->is_active)->toBeTrue();
});

it('restores a saved cart from the WhatsApp cart link', function () {
    $customer = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $customer->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [[
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'quantity' => 2,
            'subtotal' => 100,
        ]],
        'total' => 100,
        'item_count' => 2,
        'last_activity_at' => now()->subHours(2),
    ]);

    $url = URL::signedRoute('cart.restore', ['abandonedCart' => $cart->id]);

    $this->actingAs($customer)
        ->get($url)
        ->assertRedirect(route('cart.index'));

    expect(CartItem::where('user_id', $customer->id)->count())->toBe(1)
        ->and(CartItem::where('user_id', $customer->id)->first()->quantity)->toBe(2);
});

it('restores a saved cart from the WhatsApp cart link for guests', function () {
    auth()->logout();
    session()->forget('cart');

    $cart = AbandonedCart::create([
        'user_id' => null,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [[
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'quantity' => 2,
            'subtotal' => 100,
        ]],
        'total' => 100,
        'item_count' => 2,
        'last_activity_at' => now()->subHours(2),
    ]);

    $url = URL::signedRoute('cart.restore', ['abandonedCart' => $cart->id]);

    $this->get($url)->assertRedirect(route('checkout.loading'));

    expect(session('cart'))->toBeArray()
        ->and(session('cart')[0]['product_id'])->toBe($this->product->id)
        ->and(session('cart')[0]['quantity'])->toBe(2);

    $this->get(route('checkout.index'))
        ->assertOk()
        ->assertSee('Rahat Hasan')
        ->assertSee('01712345678');
});

it('marks a link-restored abandoned cart as recovered after guest checkout', function () {
    auth()->logout();
    session()->forget('cart');

    $cart = AbandonedCart::create([
        'user_id' => null,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [[
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'quantity' => 2,
            'subtotal' => 100,
        ]],
        'total' => 100,
        'item_count' => 2,
        'last_activity_at' => now()->subHours(2),
    ]);

    $this->get(URL::signedRoute('cart.restore', ['abandonedCart' => $cart->id]))
        ->assertRedirect(route('checkout.loading'));

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

    expect($cart->refresh()->recovered_at)->not->toBeNull();
});

it('rejects a tampered cart restore link', function () {
    $customer = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $customer->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 0,
        'item_count' => 0,
        'last_activity_at' => now()->subHours(2),
    ]);

    $this->actingAs($customer)
        ->get(route('cart.restore', $cart->id))
        ->assertForbidden();
});

it('hides recovered carts from the admin abandoned view', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
        'recovered_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.index', ['view' => 'abandoned']))
        ->assertOk()
        ->assertDontSee($this->user->name);
});

it('shows freshly abandoned carts in the admin abandoned view immediately', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.index', ['view' => 'abandoned']))
        ->assertOk()
        ->assertSee('Rahat Hasan');
});

it('filters abandoned carts by date range', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Old Customer',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subDays(5),
    ]);

    $oldId = AbandonedCart::firstWhere('name', 'Old Customer')->id;

    $this->actingAs($admin)
        ->get(route('admin.orders.index', [
            'view' => 'abandoned',
            'date_from' => now()->subDays(1)->format('Y-m-d'),
        ]))
        ->assertOk()
        ->assertDontSee('Old Customer');

    $this->actingAs($admin)
        ->get(route('admin.orders.index', [
            'view' => 'abandoned',
            'date_from' => now()->subDays(7)->format('Y-m-d'),
        ]))
        ->assertOk()
        ->assertSee('Old Customer');
});

it('records which WhatsApp reminder was sent', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $cart = AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.abandoned-carts.reminder', $cart), ['type' => 'discount'])
        ->assertOk();

    $this->actingAs($admin)
        ->post(route('admin.abandoned-carts.reminder', $cart), ['type' => 'reminder'])
        ->assertOk();

    $cart->refresh();

    expect($cart->reminder_type)->toBe('reminder')
        ->and($cart->reminder_sent_count)->toBe(2)
        ->and($cart->last_reminder_sent_at)->not->toBeNull();
});

it('rejects an invalid reminder type', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $cart = AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.abandoned-carts.reminder', $cart), ['type' => 'spam'])
        ->assertSessionHasErrors('type');

    expect($cart->refresh()->reminder_type)->toBeNull();
});

it('shows which reminder was sent in the abandoned view', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
        'reminder_type' => 'discount',
        'last_reminder_sent_at' => now()->subHours(2),
        'reminder_sent_count' => 1,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.index', ['view' => 'abandoned']))
        ->assertOk()
        ->assertSee('5% discount sent');
});

it('filters abandoned carts by reminder type', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
        'reminder_type' => 'discount',
        'reminder_sent_count' => 1,
    ]);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Another Customer',
        'phone' => '01811112222',
        'items_data' => [],
        'total' => 30,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
        'reminder_type' => 'stock',
        'reminder_sent_count' => 1,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.index', [
            'view' => 'abandoned',
            'reminder_type' => 'discount',
        ]))
        ->assertOk()
        ->assertSee('Rahat Hasan')
        ->assertDontSee('Another Customer');

    $this->actingAs($admin)
        ->get(route('admin.orders.index', [
            'view' => 'abandoned',
            'reminder_type' => 'none',
        ]))
        ->assertOk()
        ->assertDontSee('Rahat Hasan')
        ->assertDontSee('Another Customer');
});

it('marks an abandoned cart as ignored and toggles it back', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $cart = AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
    ]);

    $this->actingAs($admin)
        ->from(route('admin.orders.index', ['view' => 'abandoned']))
        ->post(route('admin.abandoned-carts.ignore', $cart))
        ->assertRedirect(route('admin.orders.index', ['view' => 'abandoned']));

    expect($cart->refresh()->ignored_at)->not->toBeNull();

    $this->actingAs($admin)
        ->post(route('admin.abandoned-carts.ignore', $cart))
        ->assertRedirect(route('admin.orders.index', ['view' => 'abandoned']));

    expect($cart->refresh()->ignored_at)->toBeNull();
});

it('shows ignored carts grayed with an un-ignore button', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Rahat Hasan',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
        'ignored_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.index', ['view' => 'abandoned']))
        ->assertOk()
        ->assertSee('Rahat Hasan')
        ->assertSee('abandoned-row-ignored', false)
        ->assertSee('Ignored')
        ->assertSee('Un-ignore', false);
});

it('filters abandoned carts by ignored status', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Ignored Customer',
        'phone' => '01712345678',
        'items_data' => [],
        'total' => 50,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
        'ignored_at' => now(),
    ]);

    AbandonedCart::create([
        'user_id' => $this->user->id,
        'name' => 'Active Customer',
        'phone' => '01811112222',
        'items_data' => [],
        'total' => 30,
        'item_count' => 1,
        'last_activity_at' => now()->subHours(24),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.index', [
            'view' => 'abandoned',
            'reminder_type' => 'ignored',
        ]))
        ->assertOk()
        ->assertSee('Ignored Customer')
        ->assertDontSee('Active Customer');
});
