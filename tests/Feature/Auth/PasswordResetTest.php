<?php

use App\Models\Order;
use App\Models\PasswordSetupToken;
use App\Models\User;
use App\Services\PasswordSetupService;

test('reset password link screen can be rendered', function () {
    $this->get(route('password.request'))->assertOk();
});

test('forgot password requires a phone number', function () {
    $this->from(route('password.request'))
        ->post(route('password.email'), [])
        ->assertRedirect(route('password.request'))
        ->assertSessionHasErrors('phone');
});

test('forgot password shows error for unknown phone', function () {
    $this->from(route('password.request'))
        ->post(route('password.email'), ['phone' => '01800000000'])
        ->assertRedirect(route('password.request'))
        ->assertSessionHasErrors('phone');
});

test('guest user without a password is sent to the set-password screen', function () {
    $user = User::factory()->guest()->create();

    $this->post(route('password.email'), ['phone' => $user->phone])
        ->assertRedirect();
});

test('user with a password and a recent order is sent to identity verification', function () {
    $user = User::factory()->create();

    Order::create([
        'user_id' => $user->id,
        'total_price' => 149,
    ]);

    $this->post(route('password.email'), ['phone' => $user->phone])
        ->assertRedirect(route('password.verify'))
        ->assertSessionHas('forgot_user_id', $user->id);
});

test('identity verification redirects to request screen without a pending session', function () {
    $this->get(route('password.verify'))->assertRedirect(route('password.request'));
});

test('identity verification with correct answers opens the password reset form', function () {
    $user = User::factory()->create();

    Order::create([
        'user_id' => $user->id,
        'total_price' => 149,
    ]);

    $this->withSession([
        'forgot_user_id' => $user->id,
        'forgot_question_amount' => (int) round(149, -1),
        'forgot_question_date' => now()->format('d'),
        'forgot_question_month' => now()->format('M'),
    ])->post(route('password.verify.answers'), [
        'amount' => (int) round(149, -1),
        'date' => now()->format('d'),
        'month' => now()->format('M'),
    ])->assertRedirect();

    $this->assertDatabaseHas('password_setup_tokens', [
        'user_id' => $user->id,
    ]);
});

test('identity verification rejects an incorrect amount', function () {
    $user = User::factory()->create();

    Order::create([
        'user_id' => $user->id,
        'total_price' => 149,
    ]);

    $this->withSession([
        'forgot_user_id' => $user->id,
        'forgot_question_amount' => (int) round(149, -1),
        'forgot_question_date' => now()->format('d'),
        'forgot_question_month' => now()->format('M'),
    ])->post(route('password.verify.answers'), [
        'amount' => 5,
        'date' => now()->format('d'),
        'month' => now()->format('M'),
    ])->assertSessionHasErrors('amount');
});

test('a password can be set using a valid token', function () {
    $user = User::factory()->guest()->create();

    $token = app(PasswordSetupService::class)->generateToken($user);

    $this->post(route('password.setup.store', $token->token), [
        'password' => 'Str0ng!Passw0rd',
        'password_confirmation' => 'Str0ng!Passw0rd',
    ])->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'password_set' => true,
    ]);

    $this->assertNotNull(PasswordSetupToken::find($token->id)->used_at);
});