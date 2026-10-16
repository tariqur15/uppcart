<?php

use App\Models\User;

test('admin login screen can be rendered', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

test('admins can authenticate using the admin login screen', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->post('/admin/login', [
        'phone' => $admin->phone,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('regular users can not authenticate using the admin login screen', function () {
    $user = User::factory()->create();

    $this->post('/admin/login', [
        'phone' => $user->phone,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('admins can not authenticate with invalid password', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->post('/admin/login', [
        'phone' => $admin->phone,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('admins are redirected to the admin login after logout', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('admin.login', absolute: false));
});

test('regular users are redirected to the home page after logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});