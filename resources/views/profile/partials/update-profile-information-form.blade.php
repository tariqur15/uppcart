<section>
    <header class="d-panel-head">
        <h2>{{ __('Profile Information') }}</h2>
    </header>

    <p class="p-hint">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="p-form">
        @csrf
        @method('patch')

        <div class="p-field-group">
            <label for="name" class="p-label">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="p-field" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="p-error" />
        </div>

        <div class="p-field-group">
            <label for="email" class="p-label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="p-field" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="p-error" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="p-hint" style="margin:6px 0 0;">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="p-link">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="p-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="p-actions">
            <button type="submit" class="p-btn">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="p-saved"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>