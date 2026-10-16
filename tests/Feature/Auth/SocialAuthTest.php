<?php

use App\Models\Setting;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

beforeEach(function () {
    Setting::set('google_oauth_client_id', 'db-google-id', 'integration');
    Setting::set('google_oauth_client_secret', 'db-google-secret', 'integration');
    Setting::set('google_oauth_redirect_uri', 'http://localhost/auth/google/callback', 'integration');

    Setting::set('facebook_oauth_client_id', 'db-facebook-id', 'integration');
    Setting::set('facebook_oauth_client_secret', 'db-facebook-secret', 'integration');
    Setting::set('facebook_oauth_redirect_uri', 'http://localhost/auth/facebook/callback', 'integration');
});

function socialFakeDriver(mixed $user = null, ?string $redirectUrl = null): object
{
    return new class ($user, $redirectUrl) {
        public function __construct(public mixed $user, public ?string $redirectUrl)
        {
        }

        public function setConfig(array $config): self
        {
            return $this;
        }

        public function with(array $params): self
        {
            return $this;
        }

        public function redirect()
        {
            return redirect()->away($this->redirectUrl ?? 'https://accounts.google.com/oauth/v2/auth');
        }

        public function user()
        {
            return $this->user;
        }
    };
}

test('social redirect delegates to socialite driver', function () {
    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn(socialFakeDriver(redirectUrl: 'https://accounts.google.com/oauth/v2/auth'));

    $this->get(route('social.redirect', 'google'))
        ->assertRedirect('https://accounts.google.com/oauth/v2/auth');
});

test('unknown provider redirect returns 404', function () {
    $this->get('/auth/invalid/redirect')->assertNotFound();
});

test('social callback creates a new user and logs them in', function () {
    $socialiteUser = new SocialiteUser;
    $socialiteUser->id = 'g12345';
    $socialiteUser->name = 'Google User';
    $socialiteUser->email = 'google.user@example.com';

    Socialite::shouldReceive('driver')->with('google')->andReturn(socialFakeDriver($socialiteUser));

    $response = $this->get(route('social.callback', 'google'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('email', 'google.user@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->auth_provider)->toBe('google')
        ->and($user->provider_id)->toBe('g12345');
});

test('social callback logs in an existing user by provider', function () {
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'auth_provider' => 'google',
        'provider_id' => 'g-999',
    ]);

    $socialiteUser = new SocialiteUser;
    $socialiteUser->id = 'g-999';
    $socialiteUser->name = 'Existing User';
    $socialiteUser->email = 'existing@example.com';

    Socialite::shouldReceive('driver')->with('google')->andReturn(socialFakeDriver($socialiteUser));

    $this->get(route('social.callback', 'google'));

    $this->assertAuthenticatedAs($user);
});

test('social callback without email redirects with error', function () {
    $socialiteUser = new SocialiteUser;
    $socialiteUser->id = 'no-email';
    $socialiteUser->name = 'No Email';

    Socialite::shouldReceive('driver')->with('google')->andReturn(socialFakeDriver($socialiteUser));

    $this->get(route('social.callback', 'google'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('phone');
});
