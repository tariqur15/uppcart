<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order #{{ $order->order_number }}</h2>
        </div>
    </x-slot>

    @php
        $order->loadMissing('items.product', 'downloadTokens');
        $hasDigital = $order->hasDigitalItems();
    @endphp

    <div class="max-w-4xl mx-auto">
        {{-- Password setup prompt for new users --}}
        @if (session('show_password_setup'))
            <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-xl px-5 py-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-indigo-800">We've created an account using your mobile number.</p>
                        <p class="text-sm text-indigo-700 mt-1">Set a password now to access your order history and manage future orders.</p>
                        <a href="{{ route('password.setup', ['token' => session('password_setup_token')]) }}"
                           class="mt-3 inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Set Password
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Action buttons --}}
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="{{ route('orders.invoice.download', $order) }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download Invoice
            </a>
            <a href="{{ route('orders.invoice.print', $order) }}" target="_blank"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Invoice
            </a>
            @if (! $hasDigital)
                <a href="{{ route('track-order') }}"
                       class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Track Order
                </a>
            @endif
        </div>

        {{-- Digital Downloads --}}
        @if ($hasDigital && $order->downloadTokens->isNotEmpty())
            <div class="mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl px-6 py-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-emerald-900">Your Downloads</h3>
                        <p class="text-sm text-emerald-700">Click below to download your files.</p>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach ($order->downloadTokens as $token)
                        <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-emerald-100">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 text-sm truncate">{{ $token->file_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if ($token->download_limit)
                                            {{ $token->download_count }}/{{ $token->download_limit }} downloads used
                                        @else
                                            {{ $token->download_count }} downloads
                                        @endif
                                        @if ($token->expires_at)
                                            &middot; Expires {{ $token->expires_at->format('M d, Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if ($token->canDownload())
                                <a href="{{ route('download.show', $token) }}"
                                   class="shrink-0 inline-flex items-center gap-1.5 bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                            @else
                                <span class="shrink-0 text-xs font-medium text-gray-400 bg-gray-100 px-3 py-2 rounded-lg">
                                    {{ $token->isExpired() ? 'Expired' : 'Limit reached' }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Order receipt card --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            {{-- Receipt header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-6 sm:px-8 sm:py-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-indigo-200 text-sm uppercase tracking-wider">Order Confirmed</p>
                        <h3 class="text-2xl font-bold mt-1">Order #{{ $order->order_number }}</h3>
                    </div>
                    <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-indigo-200">Status</p>
                        <p class="font-semibold capitalize">{{ $order->status }}</p>
                    </div>
                    <div>
                        <p class="text-indigo-200">Payment</p>
                        <p class="font-semibold capitalize">{{ $order->payment_status }}</p>
                    </div>
                    <div>
                        <p class="text-indigo-200">Total</p>
                        <p class="font-semibold">{{ $currencySymbol }}{{ number_format($order->total_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-indigo-200">Date</p>
                        <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Shipping address --}}
            @if ($order->address)
                <div class="px-6 sm:px-8 py-4 border-b border-gray-100 bg-gray-50">
                    <p class="text-sm font-medium text-gray-600">Shipping Address</p>
                    <p class="text-sm text-gray-900 mt-0.5">{{ $order->address }}</p>
                </div>
            @endif

            {{-- Items --}}
            <div class="px-6 sm:px-8 py-6">
                <h4 class="font-semibold text-gray-900 mb-4">Items Ordered</h4>

                {{-- Desktop table --}}
                <div class="hidden sm:block">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-200">
                            <tr>
                                <th class="text-left pb-3 font-medium text-gray-600">Product</th>
                                <th class="text-center pb-3 font-medium text-gray-600">Price</th>
                                <th class="text-center pb-3 font-medium text-gray-600">Qty</th>
                                <th class="text-right pb-3 font-medium text-gray-600">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                                @if ($item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="" class="w-8 h-8 object-contain">
                                                @else
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $item->product->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">{{ $currencySymbol }}{{ number_format($item->price, 2) }}</td>
                                    <td class="py-3 text-center">{{ $item->quantity }}</td>
                                    <td class="py-3 text-right font-medium">{{ $currencySymbol }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile items --}}
                <div class="sm:hidden space-y-3">
                    @foreach ($order->items as $item)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shrink-0">
                                @if ($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="" class="w-10 h-10 object-contain">
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} × {{ $currencySymbol }}{{ number_format($item->price, 2) }}</p>
                            </div>
                            <span class="font-medium text-gray-900">{{ $currencySymbol }}{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">Total</span>
                    <span class="text-xl font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($order->total_price, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Continue shopping --}}
        <div class="mt-8 text-center">
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Continue Shopping
            </a>
        </div>
    </div>
</x-app-layout>
