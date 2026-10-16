<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Enter your phone number and we'll help you recover your account.
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="phone" value="Phone Number" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autofocus placeholder="01XXXXXXXXX" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Continue
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
