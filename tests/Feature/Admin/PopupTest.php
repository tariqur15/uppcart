<?php

use App\Models\Popup;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

function popup_config(array $overrides = []): array
{
    return array_merge([
        'title' => 'Grand Sale',
        'message' => 'Get 20% off today.',
        'image' => null,
        'image_position' => 'top',
        'button_text' => 'Shop Now',
        'button_url' => '/shop',
        'button_color' => '#4f46e5',
        'trigger' => 'after_seconds',
        'trigger_seconds' => 3,
        'frequency' => 'once_per_session',
        'hide_after_close' => true,
        'hide_after_click' => false,
        'show_on' => ['all'],
        'devices' => ['all'],
        'customers' => ['everyone'],
        'start' => '',
        'end' => '',
    ], $overrides);
}

function make_popup(array $config = []): Popup
{
    return Popup::factory()->create([
        'name' => 'Sale Popup',
        'is_active' => true,
        'config' => $config === [] ? popup_config() : $config,
    ]);
}

it('guest cannot access the popup manager', function () {
    $this->get(route('admin.popups.index'))->assertRedirect(route('login'));
});

it('admin can create a popup', function () {
    Storage::fake('public');

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
    $this->actingAs($this->admin)
        ->post(route('admin.popups.store'), [
            'name' => 'Eid Offer',
            'config' => json_encode(popup_config(['title' => 'Eid Mega Sale'])),
            'is_active' => 1,
            'sort_order' => 1,
            'image' => UploadedFile::fake()->createWithContent('promo.png', $png),
        ])
        ->assertRedirect(route('admin.settings.index', ['tab' => 'additional', 'sub' => 'popup']));

    $this->assertDatabaseHas('popups', ['name' => 'Eid Offer', 'is_active' => 1, 'sort_order' => 1]);
    $popup = Popup::where('name', 'Eid Offer')->first();
    expect($popup->getConfig()['title'])->toBe('Eid Mega Sale');
});

it('requires an image when creating a popup', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.popups.store'), [
            'name' => 'No Image Popup',
            'config' => json_encode(popup_config()),
            'is_active' => 1,
        ])
        ->assertSessionHasErrors('image');

    $this->assertDatabaseMissing('popups', ['name' => 'No Image Popup']);
});

it('stores an uploaded popup image', function () {
    Storage::fake('public');

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
    $this->actingAs($this->admin)
        ->post(route('admin.popups.store'), [
            'name' => 'Image Popup',
            'config' => json_encode(popup_config()),
            'is_active' => 1,
            'image' => UploadedFile::fake()->createWithContent('promo.png', $png),
        ])
        ->assertRedirect(route('admin.settings.index', ['tab' => 'additional', 'sub' => 'popup']));

    $popup = Popup::where('name', 'Image Popup')->first();
    expect($popup->getConfig()['image'])->not->toBeNull();
    Storage::disk('public')->assertExists($popup->getConfig()['image']);
});

it('replaces and removes the popup image on update', function () {
    Storage::fake('public');

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

    $popup = make_popup();

    $this->actingAs($this->admin)
        ->patch(route('admin.popups.update', $popup), [
            'name' => $popup->name,
            'config' => json_encode(popup_config()),
            'is_active' => 1,
            'image' => UploadedFile::fake()->createWithContent('new-promo.png', $png),
        ]);

    $popup->refresh();
    $first = $popup->getConfig()['image'];
    expect($first)->not->toBeNull();
    Storage::disk('public')->assertExists($first);

    $this->actingAs($this->admin)
        ->patch(route('admin.popups.update', $popup), [
            'name' => $popup->name,
            'config' => json_encode(popup_config()),
            'is_active' => 1,
            'remove_image' => 1,
        ]);

    $popup->refresh();
    expect($popup->getConfig()['image'])->toBeNull();
    Storage::disk('public')->assertMissing($first);
});

it('returns a JSON redirect when saving via the frontend fetch/XHR', function () {
    Storage::fake('public');

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
    $this->actingAs($this->admin)
        ->post(route('admin.popups.store'), [
            'name' => 'XHR Popup',
            'config' => json_encode(popup_config()),
            'is_active' => 1,
            'image' => UploadedFile::fake()->createWithContent('promo.png', $png),
        ], ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'redirect' => route('admin.settings.index', ['tab' => 'additional', 'sub' => 'popup']),
        ]);

    $this->assertDatabaseHas('popups', ['name' => 'XHR Popup']);
});

it('renders an active image popup on the storefront home page', function () {
    make_popup(popup_config(['trigger' => 'immediately', 'image' => 'popups/promo.png']));

    $this->get('/')
        ->assertOk()
        ->assertSee('storePopup')
        ->assertSee('popups/promo.png');
});

it('makes the popup image a clickable link when a button url is set', function () {
    make_popup(popup_config([
        'trigger' => 'immediately',
        'image' => 'popups/promo.png',
        'button_url' => '/collections/sale',
    ]));

    $this->get('/')
        ->assertOk()
        ->assertSee('href="/collections/sale"', false)
        ->assertSee('@click="cta($event)"', false);
});

it('does not wrap the popup image in a link when no button url is set', function () {
    make_popup(popup_config(['trigger' => 'immediately', 'image' => 'popups/promo.png', 'button_url' => '']));

    $this->get('/')
        ->assertOk()
        ->assertSee('@click="close()"', false)
        ->assertDontSee('cta($event)', false);
});

it('does not render an inactive popup', function () {
    Popup::factory()->create([
        'name' => 'Off',
        'is_active' => false,
        'config' => popup_config(),
    ]);

    $this->get('/')->assertOk()->assertDontSee('storePopup');
});

it('does not render a popup before its scheduled start', function () {
    make_popup(popup_config([
        'start' => now()->addDay()->format('Y-m-d\TH:i'),
        'end' => '',
    ]));

    $this->get('/')->assertOk()->assertDontSee('storePopup');
});

it('does not render a popup after its scheduled end', function () {
    make_popup(popup_config([
        'start' => '',
        'end' => now()->subDay()->format('Y-m-d\TH:i'),
    ]));

    $this->get('/')->assertOk()->assertDontSee('storePopup');
});

it('respects page targeting', function () {
    make_popup(popup_config(['show_on' => ['products']]));

    $this->get('/')->assertOk()->assertDontSee('storePopup');
});

it('does not show a logged-in-only popup to a guest', function () {
    make_popup(popup_config(['customers' => ['logged_in']]));

    $this->get('/')->assertOk()->assertDontSee('storePopup');
});

it('tracks popup events via the storefront endpoint', function () {
    $popup = make_popup();

    foreach (['impression', 'view', 'click', 'close'] as $type) {
        $this->post(route('popup.track'), [
            'popup_id' => $popup->id,
            'type' => $type,
            'page' => '/',
        ])->assertOk();
    }

    $this->assertDatabaseCount('popup_events', 4);
    $this->assertDatabaseHas('popup_events', ['popup_id' => $popup->id, 'type' => 'click']);
});

it('rejects invalid track event types', function () {
    $popup = make_popup();

    $this->post(route('popup.track'), [
        'popup_id' => $popup->id,
        'type' => 'bogus',
    ])->assertSessionHasErrors('type');
});

it('computes analytics stats and shows them on the settings popup manager', function () {
    $popup = make_popup();

    foreach (['impression', 'view', 'view', 'click', 'view', 'close'] as $i => $type) {
        \App\Models\PopupEvent::create([
            'popup_id' => $popup->id,
            'type' => $type,
            'page' => '/',
        ]);
    }

    $this->actingAs($this->admin)
        ->get(route('admin.settings.index', ['tab' => 'additional']))
        ->assertOk()
        ->assertSee($popup->name)
        ->assertSee('3')
        ->assertSee('1');
});

it('redirects the standalone popup pages to the settings popup tab', function () {
    $popup = make_popup();

    $this->actingAs($this->admin)
        ->get(route('admin.popups.index'))
        ->assertRedirect(route('admin.settings.index', ['tab' => 'additional']));

    $this->actingAs($this->admin)
        ->get(route('admin.popups.create'))
        ->assertRedirect(route('admin.settings.index', ['tab' => 'additional']));

    $this->actingAs($this->admin)
        ->get(route('admin.popups.edit', $popup))
        ->assertRedirect(route('admin.settings.index', ['tab' => 'additional']));

    $this->actingAs($this->admin)
        ->get(route('admin.popups.analytics', $popup))
        ->assertOk();
});

it('renders the popup manager on the additional settings tab', function () {
    make_popup();

    $this->actingAs($this->admin)
        ->get(route('admin.settings.index', ['tab' => 'additional']))
        ->assertOk()
        ->assertSee('popupManager')
        ->assertSee('Add popup')
        ->assertSee('Sale Popup');
});
