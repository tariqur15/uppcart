<?php

use App\Models\Setting;

it('does not render fake order notifications when disabled', function () {
    Setting::set('fake_orders_enabled', '', 'additional');

    $this->get('/')
        ->assertOk()
        ->assertDontSee('fakeOrders')
        ->assertDontSee('id="fake-order-interval"');
});

it('renders the fake order notification component with the configured template', function () {
    Setting::set('fake_orders_enabled', '1', 'additional');
    Setting::set('fake_order_message', '{name} from {city} just bought a {product}!', 'additional');
    Setting::set('fake_order_interval_seconds', '25', 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('fakeOrders', false)
        ->assertSee('id="fake-order-interval"', false)
        ->assertSee('25', false)
        ->assertSee('just bought a', false)
        ->assertSee('{name}', false)
        ->assertSee('{city}', false)
        ->assertSee('{product}', false);
});

it('uses the configured display position', function () {
    Setting::set('fake_orders_enabled', '1', 'additional');
    Setting::set('fake_order_position', 'top-right', 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('fixed top-5 right-5', false);
});

it('only renders on pages selected in the show-on setting', function () {
    Setting::set('fake_orders_enabled', '1', 'additional');
    Setting::set('fake_order_pages', 'products,product', 'additional');

    $this->get('/')
        ->assertOk()
        ->assertDontSee('fakeOrders');

    $this->get('/products')
        ->assertOk()
        ->assertSee('fakeOrders', false);
});

it('renders on all pages when "all" is selected', function () {
    Setting::set('fake_orders_enabled', '1', 'additional');
    Setting::set('fake_order_pages', 'all', 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('fakeOrders', false);
});

it('renders only on the matching page for a single preset', function () {
    Setting::set('fake_orders_enabled', '1', 'additional');
    Setting::set('fake_order_pages', 'product', 'additional');

    $product = \App\Models\Product::factory()->create();

    $this->get('/')
        ->assertOk()
        ->assertDontSee('fakeOrders');

    $this->get('/products/'.$product->slug)
        ->assertOk()
        ->assertSee('fakeOrders', false);
});
