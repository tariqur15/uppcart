<x-admin-layout>
    <div class="mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Coupon</h2>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-5">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $coupon->title) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Summer Sale 2026" required>
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Code --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="SUMMER20" required>
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Discount Type | Discount Value --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
                        <select name="type" id="type"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed Amount</option>
                            <option value="percentage" @selected(old('type', $coupon->type) === 'percentage')>Percentage</option>
                        </select>
                        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Discount Value</label>
                        <input type="number" step="0.01" name="value" id="value" value="{{ old('value', $coupon->value) }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="10.00" required>
                        @error('value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Minimum Purchase --}}
                <div>
                    <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-1">Minimum Purchase</label>
                    <input type="number" step="0.01" name="min_order_amount" id="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="0.00">
                    @error('min_order_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Valid from | Valid Until --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Valid from</label>
                        <input type="date" name="starts_at" id="starts_at"
                               value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('starts_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label>
                        <input type="date" name="expires_at" id="expires_at"
                               value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('expires_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Usage Limit --}}
                <div>
                    <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
                    <input type="number" name="max_uses" id="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Unlimited">
                    @error('max_uses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Active --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $coupon->is_active))
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        Update Coupon
                    </button>
                    <a href="{{ route('admin.coupons.index') }}"
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
