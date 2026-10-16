<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        return Socialite::driver($provider)
            ->with(['redirect' => request('redirect')])
            ->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (InvalidStateException|Throwable $e) {
            return redirect()->route('login')->withErrors([
                'phone' => 'Unable to sign in with '.ucfirst($provider).'. Please try again.',
            ]);
        }

        $email = $socialUser->getEmail();

        if (! $email) {
            return redirect()->route('login')->withErrors([
                'phone' => ucfirst($provider).' did not provide an email address. Try another method.',
            ]);
        }

        $user = DB::transaction(function () use ($provider, $socialUser, $email) {
            return User::query()
                ->where('auth_provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first()
                ?? User::query()->where('email', $email)->first()
                ?? User::create([
                    'name' => $socialUser->getName() ?: 'Customer',
                    'email' => $email,
                    'email_verified_at' => now(),
                    'auth_provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password_set' => false,
                ]);
        });

        Auth::login($user);

        request()->session()->regenerate();

        if ($redirect = request('redirect')) {
            return redirect()->to($redirect);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
