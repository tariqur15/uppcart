<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        To verify your identity, please answer the following question about your last order.
    </div>

    <form method="POST" action="{{ route('password.verify.answers') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="amount" value="What was the approximate total amount of your last order?" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">৳</span>
                <x-text-input id="amount" class="block mt-1 w-full pl-8" type="number" name="amount" :value="old('amount')" required autofocus placeholder="e.g. 500" />
            </div>
            <p class="mt-1 text-xs text-gray-400">Enter the nearest whole number (within ৳50 is accepted)</p>
            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="date" value="What day of the month was your last order placed?" />
            <select id="date" name="date" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">Select day</option>
                @for ($d = 1; $d <= 31; $d++)
                    <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}" {{ old('date') == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ $d }}</option>
                @endfor
            </select>
            <x-input-error :messages="$errors->get('date')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="month" value="In which month was your last order placed?" />
            <select id="month" name="month" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">Select month</option>
                @php $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; @endphp
                @foreach ($months as $m)
                    <option value="{{ $m }}" {{ old('month') === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('month')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Verify & Reset Password
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
