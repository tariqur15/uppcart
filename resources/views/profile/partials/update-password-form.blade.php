<section>
    <header class="d-panel-head">
        <h2>{{ __('Update Password') }}</h2>
    </header>

    <p class="p-hint">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}" class="p-form">
        @csrf
        @method('put')

        <div class="p-field-group">
            <label for="update_password_current_password" class="p-label">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="p-field" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="p-error" />
        </div>

        <div class="p-field-group">
            <label for="update_password_password" class="p-label">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="p-field" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="p-error" />
        </div>

        <div class="p-field-group">
            <label for="update_password_password_confirmation" class="p-label">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="p-field" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="p-error" />
        </div>

        <div class="p-actions">
            <button type="submit" class="p-btn">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
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