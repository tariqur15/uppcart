<x-admin-layout>
    <div class="mb-6 flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Coupons</h2>
        <a href="{{ route('admin.coupons.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
            Add Coupon
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($coupons->isEmpty())
            <div class="p-6 text-center text-gray-500">No coupons yet.</div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="px-6 py-3 font-semibold text-gray-600">Title</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Code</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Type</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Value</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Used</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Expires</th>
                        <th class="px-6 py-3 font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coupons as $coupon)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $coupon->title }}</td>
                            <td class="px-6 py-4 font-mono font-semibold text-gray-900">{{ $coupon->code }}</td>
                            <td class="px-6 py-4 capitalize text-gray-600">{{ $coupon->type }}</td>
                            <td class="px-6 py-4 text-gray-900">
                                {{ $coupon->type === 'percentage' ? $coupon->value . '%' : '$' . number_format($coupon->value, 2) }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $coupon->used_count }}@if ($coupon->max_uses)/{{ $coupon->max_uses }}@endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($coupon->isValid())
                                    <span class="text-green-600 font-medium">Active</span>
                                @else
                                    <span class="text-red-600 font-medium">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $coupon->expires_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Delete this coupon?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium ml-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
