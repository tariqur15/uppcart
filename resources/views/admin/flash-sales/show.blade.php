<x-admin-layout>
    @php
        $badge = match ($sale->effective_status) {
            'active' => ['text' => 'Active', 'class' => 'bg-emerald-100 text-emerald-700'],
            'scheduled' => ['text' => 'Scheduled', 'class' => 'bg-blue-100 text-blue-700'],
            'ended' => ['text' => 'Ended', 'class' => 'bg-gray-200 text-gray-600'],
            'cancelled' => ['text' => 'Cancelled', 'class' => 'bg-red-100 text-red-700'],
            default => ['text' => 'Draft', 'class' => 'bg-amber-100 text-amber-700'],
        };
    @endphp

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.flash-sales.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $sale->name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $sale->starts_at?->format('M d, Y H:i') ?? 'Unset' }} — {{ $sale->ends_at?->format('M d, Y H:i') ?? 'Unset' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.flash-sales.edit', $sale) }}"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Edit
            </a>
            <form action="{{ route('admin.flash-sales.duplicate', $sale) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Duplicate
                </button>
            </form>
            @if ($sale->effective_status === 'active')
                <form action="{{ route('admin.flash-sales.end', $sale) }}" method="POST"
                      onsubmit="return confirm('End this flash sale now and restore normal pricing?')" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-600 transition">
                        End Sale
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.flash-sales.destroy', $sale) }}" method="POST"
                  onsubmit="return confirm('Delete or archive this flash sale?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg text-sm">{{ session('warning') }}</div>
    @endif

    @if ($sale->description)
        <div class="mb-6 text-sm text-gray-600 bg-white border border-gray-200 rounded-xl px-4 py-3">{{ $sale->description }}</div>
    @endif

    {{-- Analytics --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Products</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $sale->products->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Units sold</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $soldCount }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Discount given</p>
            <p class="mt-2 text-2xl font-bold text-indigo-600">{{ $currencySymbol }}{{ number_format($totalDiscount, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Avg order value</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($averageOrderValue, 2) }}</p>
        </div>
    </div>

    @if ($bestSeller && $bestSeller->sold > 0)
        <div class="mb-8 bg-indigo-50 border border-indigo-200 rounded-xl px-5 py-4 text-sm text-indigo-800">
            <strong>Best seller:</strong>
            {{ $bestSeller->product?->name }}{{ $bestSeller->variant ? ' ('.$bestSeller->variant.')' : '' }}
            — {{ $bestSeller->sold }} units sold.
        </div>
    @endif

    {{-- Products table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Products</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Scope</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Regular</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Flash</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Discount</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sold</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Remaining</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($sale->products as $fp)
                    @php
                        $discountPct = $fp->regular_price > 0 ? round((1 - $fp->flash_price / $fp->regular_price) * 100) : 0;
                        $remaining = $fp->sale_stock === null ? null : max(0, $fp->sale_stock - $fp->sold);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                    @if ($fp->product?->image)
                                        <img src="{{ Storage::url($fp->product->image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $fp->product?->name ?? 'Product #'.$fp->product_id }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $fp->scope === \App\Models\FlashSaleProduct::SCOPE_VARIANTS ? 'Variant: '.$fp->variant : 'Whole product' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            @if ($fp->scope === \App\Models\FlashSaleProduct::SCOPE_VARIANTS)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-blue-50 text-blue-700">Variant</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">Whole</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right text-gray-500 line-through">{{ $currencySymbol }}{{ number_format($fp->regular_price, 2) }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-indigo-600">{{ $currencySymbol }}{{ number_format($fp->flash_price, 2) }}</td>
                        <td class="px-5 py-3 text-right">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">-{{ $discountPct }}%</span>
                        </td>
                        <td class="px-5 py-3 text-center text-gray-700 font-medium">{{ $fp->sold }}</td>
                        <td class="px-5 py-3 text-center text-gray-600">
                            @if ($remaining === null)
                                <span class="text-gray-400">Unlimited</span>
                            @else
                                {{ $remaining }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
