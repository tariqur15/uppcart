<?php

use App\Models\Setting;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

function top_bar_payload(array $overrides = []): string
{
    $bar = array_merge([
        'id' => 'tb_test_1',
        'sort' => 0,
        'name' => 'Free Shipping Bar',
        'status' => true,
        'type' => 'free_shipping',
        'message' => 'Free Shipping on orders over',
        'highlight_text' => 'Shop Now',
        'button_text' => 'Shop Sale',
        'button_url' => '/shop',
        'icon' => 'icons/truck.svg',
        'coupon_code' => '',
        'bg_color' => '#4f46e5',
        'text_color' => '#ffffff',
        'highlight_color' => '#fbbf24',
        'button_color' => '#10b981',
        'font_size' => 'sm',
        'font_weight' => 'semibold',
        'bar_height' => '12',
        'text_align' => 'center',
        'icon_position' => 'left',
        'show_close' => true,
        'sticky' => false,
        'animation' => 'static',
        'countdown_enabled' => false,
        'countdown_end' => '',
        'countdown_start' => '',
        'expired_message' => '',
        'show_on' => ['all'],
        'devices' => ['all'],
        'start' => '',
        'end' => '',
    ], $overrides);

    return json_encode([$bar]);
}

it('guest cannot access settings', function () {
    $this->get(route('admin.settings.index', ['tab' => 'additional']))
        ->assertRedirect(route('login'));
});

it('renders the top bar builder on the additional settings tab', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.settings.index', ['tab' => 'additional']))
        ->assertOk()
        ->assertSee('topBarBuilder')
        ->assertSee('name="top_bars"', false);
});

it('persists top bars as json via the update endpoint', function () {
    $payload = top_bar_payload();

    $this->actingAs($this->admin)
        ->patch(route('admin.settings.update'), [
            'tab' => 'additional',
            'top_bars' => $payload,
        ])->assertRedirect(route('admin.settings.index', ['tab' => 'additional']));

    $saved = Setting::get('top_bars');
    $this->assertIsString($saved);
    $this->assertJson($saved);
    $this->assertSame('Free Shipping Bar', json_decode($saved, true)[0]['name']);
});

it('renders an active top bar on the storefront home page', function () {
    Setting::set('top_bars', top_bar_payload(), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('Free Shipping on orders over')
        ->assertSee('Shop Now')
        ->assertSee('Shop Sale')
        ->assertSee('4f46e5');
});

it('does not render an inactive top bar on the storefront', function () {
    Setting::set('top_bars', top_bar_payload(['status' => false]), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertDontSee('Free Shipping on orders over');
});

it('renders an enabled countdown bar with an end time', function () {
    Setting::set('top_bars', top_bar_payload([
        'type' => 'countdown',
        'message' => 'Sale ends in',
        'countdown_enabled' => true,
        'countdown_end' => now()->addHours(5)->format('Y-m-d\TH:i'),
        'countdown_start' => now()->addMinutes(30)->format('Y-m-d\TH:i'),
        'expired_message' => 'Sale over',
    ]), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('Sale ends in')
        ->assertSee('topBarCountdown')
        ->assertSee('Sale over');
});

it('renders countdown with an end time even when enable toggle is off', function () {
    Setting::set('top_bars', top_bar_payload([
        'type' => 'countdown',
        'message' => 'Sale ends in',
        'countdown_enabled' => false,
        'countdown_end' => now()->addHours(5)->format('Y-m-d\TH:i'),
        'expired_message' => 'Sale over',
    ]), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('topBarCountdown');
});

it('does not render countdown without an end time', function () {
    Setting::set('top_bars', top_bar_payload([
        'type' => 'countdown',
        'message' => 'Sale ends in',
        'countdown_enabled' => false,
        'countdown_end' => '',
    ]), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertDontSee('topBarCountdown');
});

it('exposes countdown start and end through the content builder', function () {
    $svc = app(\App\Services\TopBarService::class);
    $content = $svc->content([
        'type' => 'countdown',
        'countdown_enabled' => true,
        'countdown_end' => '2026-09-01T18:00',
        'countdown_start' => '2026-08-30T09:00',
        'expired_message' => 'Ended',
    ]);

    expect($content['countdown_end'])->toBe('2026-09-01T18:00')
        ->and($content['countdown_start'])->toBe('2026-08-30T09:00')
        ->and($content['expired_message'])->toBe('Ended');
});

it('respects page targeting', function () {
    Setting::set('top_bars', top_bar_payload(['show_on' => ['products']]), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertDontSee('Free Shipping on orders over');
});

it('applies the marquee track styles when marquee is selected', function () {
    Setting::set('top_bars', top_bar_payload(['animation' => 'marquee']), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('tb-marquee-track');
});

it('applies the fade animation styles when fade is selected', function () {
    Setting::set('top_bars', top_bar_payload(['animation' => 'fade']), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('tb-fade');
});

it('applies the typewriter reveal to the message when typewriter is selected', function () {
    Setting::set('top_bars', top_bar_payload(['animation' => 'typewriter']), 'additional');

    $this->get('/')
        ->assertOk()
        ->assertSee('tb-type-inner');
});
