<?php

use App\Models\Coupon;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

it('guest cannot access coupon index', function () {
    $this->get(route('admin.coupons.index'))->assertRedirect(route('login'));
});

it('non-admin cannot access coupons', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $this->actingAs($user)
        ->get(route('admin.coupons.index'))
        ->assertForbidden();
});

it('admin can list coupons', function () {
    $this->actingAs($this->admin);

    Coupon::factory()->create(['title' => 'Summer Sale']);
    Coupon::factory()->create(['title' => 'Winter Sale']);

    $this->get(route('admin.coupons.index'))
        ->assertSuccessful()
        ->assertSee('Summer Sale')
        ->assertSee('Winter Sale');
});

it('admin can create coupon', function () {
    $this->actingAs($this->admin);

    $this->post(route('admin.coupons.store'), [
        'title' => 'New Coupon',
        'code' => 'SAVE20',
        'type' => 'percentage',
        'value' => 20,
        'max_uses' => 100,
        'is_active' => true,
    ])->assertRedirect(route('admin.coupons.index'));

    $this->assertDatabaseHas('coupons', [
        'code' => 'SAVE20',
        'type' => 'percentage',
        'value' => 20.00,
    ]);
});

it('validates coupon code uniqueness', function () {
    $this->actingAs($this->admin);

    Coupon::create([
        'title' => 'Existing',
        'code' => 'DUPLICATE',
        'type' => 'fixed',
        'value' => 10,
    ]);

    $this->post(route('admin.coupons.store'), [
        'title' => 'New',
        'code' => 'DUPLICATE',
        'type' => 'fixed',
        'value' => 10,
    ])->assertSessionHasErrors('code');
});

it('admin can update coupon', function () {
    $this->actingAs($this->admin);

    $coupon = Coupon::factory()->create([
        'title' => 'Old Title',
        'code' => 'OLDCODE',
        'value' => 10,
    ]);

    $this->patch(route('admin.coupons.update', $coupon), [
        'title' => 'Updated Title',
        'code' => 'NEWCODE',
        'type' => 'fixed',
        'value' => 25,
        'is_active' => true,
    ])->assertRedirect(route('admin.coupons.index'));

    $this->assertDatabaseHas('coupons', [
        'id' => $coupon->id,
        'title' => 'Updated Title',
        'code' => 'NEWCODE',
        'value' => 25.00,
    ]);
});

it('admin can delete coupon', function () {
    $this->actingAs($this->admin);

    $coupon = Coupon::factory()->create(['title' => 'To Delete']);

    $this->delete(route('admin.coupons.destroy', $coupon))
        ->assertRedirect(route('admin.coupons.index'));

    $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
});

it('coupon is valid when active and within limits', function () {
    $coupon = Coupon::create([
        'title' => 'Valid Coupon',
        'code' => 'VALID',
        'type' => 'percentage',
        'value' => 10,
        'max_uses' => 100,
        'used_count' => 0,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    expect($coupon->isValid())->toBeTrue();
});

it('coupon is invalid when inactive', function () {
    $coupon = Coupon::create([
        'title' => 'Inactive',
        'code' => 'INACTIVE',
        'type' => 'fixed',
        'value' => 10,
        'is_active' => false,
    ]);

    expect($coupon->isValid())->toBeFalse();
});

it('coupon is invalid when max uses reached', function () {
    $coupon = Coupon::create([
        'title' => 'Expired Uses',
        'code' => 'MAXED',
        'type' => 'fixed',
        'value' => 10,
        'max_uses' => 5,
        'used_count' => 5,
        'is_active' => true,
    ]);

    expect($coupon->isValid())->toBeFalse();
});

it('coupon is invalid before start date', function () {
    $coupon = Coupon::create([
        'title' => 'Not Started',
        'code' => 'FUTURE',
        'type' => 'fixed',
        'value' => 10,
        'starts_at' => now()->addDay(),
        'is_active' => true,
    ]);

    expect($coupon->isValid())->toBeFalse();
});

it('coupon is invalid after expiry date', function () {
    $coupon = Coupon::create([
        'title' => 'Expired',
        'code' => 'EXPIRED',
        'type' => 'fixed',
        'value' => 10,
        'expires_at' => now()->subDay(),
        'is_active' => true,
    ]);

    expect($coupon->isValid())->toBeFalse();
});
