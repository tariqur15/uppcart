<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Track Your Order</h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        {{-- Search form --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 mb-8">
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Find Your Order</h3>
                <p class="text-sm text-gray-500 mt-1">Enter your order number and phone number to track</p>
            </div>

            <form action="{{ route('track-order.search') }}" method="POST" id="trackForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="order_suffix" class="block text-sm font-semibold text-gray-700 mb-2">Order Number</label>
                        <div class="flex items-stretch rounded-xl border border-gray-200 shadow-sm overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all bg-gray-50/50">
                            <span class="flex items-center px-4 bg-indigo-50 border-r border-indigo-100 text-indigo-600 text-sm font-bold font-mono tracking-wider select-none shrink-0">ORD-</span>
                            <input type="text" id="order_suffix" required
                                   placeholder="260726-846918"
                                   value="{{ Str::after(old('order_number', ''), 'ORD-') }}"
                                   class="flex-1 min-w-0 px-4 py-3.5 border-0 bg-white text-base font-mono tracking-[0.15em] text-gray-900 font-medium focus:outline-none focus:ring-0 placeholder:text-gray-300 placeholder:tracking-normal">
                        </div>
                        <input type="hidden" id="order_number" name="order_number" value="{{ old('order_number', '') }}">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                                   placeholder="01XXXXXXXXX"
                                   class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-gray-200 shadow-sm bg-gray-50/50 text-base font-mono tracking-[0.15em] text-gray-900 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all placeholder:text-gray-300 placeholder:tracking-normal">
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-3 rounded-xl text-sm font-semibold hover:bg-indigo-700 active:scale-[0.98] transition-all shadow-sm shadow-indigo-200">
                        Track Order
                    </button>
                </div>
            </form>

            @error('order_number')
                <div class="mt-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2.5">
                    <svg class="w-4.5 h-4.5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        @push('scripts')
        <script>
            document.getElementById('trackForm').addEventListener('submit', function(e) {
                var suffix = document.getElementById('order_suffix').value.trim();
                document.getElementById('order_number').value = suffix ? 'ORD-' + suffix : '';
            });
        </script>
        @endpush

        {{-- Order result --}}
        @if (isset($order))
            @php
                $statuses = ['pending', 'processing', 'shipped', 'completed'];
                $cancelled = $order->status === 'cancelled';
                $currentIndex = array_search($order->status, $statuses);
                $found = ! $cancelled && $currentIndex !== false;

                $trackingSteps = ['Accepted', 'Picked', 'In Transit', 'Ready for Delivery', 'Delivered'];
                $currentStep = 0;
                $courierCancelled = false;
                $courierReturned = false;
                if ($courierTracking && $courierTracking['success']) {
                    $cs = strtolower($courierTracking['status'] ?? '');
                    $slug = strtolower($courierTracking['status_slug'] ?? '');
                    $courierCancelled = str_contains($cs, 'cancel') || str_contains($slug, 'cancel');
                    $courierReturned = str_contains($cs, 'return') || str_contains($slug, 'return');
                    if (! $courierCancelled && ! $courierReturned) {
                        $currentStep = match(true) {
                            str_contains($cs, 'deliver') => 4,
                            str_contains($cs, 'out_for') || str_contains($cs, 'ready') => 3,
                            str_contains($cs, 'transit') || str_contains($cs, 'on_the_way') || str_contains($cs, 'dispatch') => 2,
                            str_contains($cs, 'pick') => 1,
                            default => 0,
                        };
                    }
                }
                $isDelivered = $currentStep === 4;
                $courierLabel = match($order->courier_name) { 'pathao' => 'Pathao', 'redx' => 'RedX', default => 'Steadfast' };
                $trackingId = $order->consignment_id ?? $order->tracking_code;
            @endphp

            {{-- Order header card --}}
            <div class="bg-gradient-to-br from-indigo-600 via-indigo-600 to-indigo-700 rounded-2xl p-6 sm:p-8 text-white mb-6 shadow-lg shadow-indigo-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-indigo-200 text-xs uppercase tracking-wider font-medium">Tracking Order</p>
                            <h3 class="text-xl sm:text-2xl font-bold mt-1">{{ $order->order_number }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if ($order->status === 'pending') bg-yellow-400/20 text-yellow-100
                            @elseif ($order->status === 'processing') bg-blue-400/20 text-blue-100
                            @elseif ($order->status === 'shipped') bg-purple-400/20 text-purple-100
                            @elseif ($order->status === 'completed') bg-emerald-400/20 text-emerald-100
                            @else bg-red-400/20 text-red-100
                            @endif">
                            @if ($order->status === 'pending')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            @elseif ($order->status === 'processing')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                            @elseif ($order->status === 'completed')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @endif
                            {{ ucfirst($order->status) }}
                        </span>
                        <span class="text-indigo-200 text-sm">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Status Timeline --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 mb-6">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-6">Order Progress</h4>

                <div class="space-y-0">
                    @foreach ($statuses as $index => $status)
                        @php
                            $isActive = $found && $index <= $currentIndex;
                            $isCurrent = $found && $index === $currentIndex;
                            $isCompleted = $found && $index < $currentIndex;
                            $hasCourierBetween = $status === 'shipped' && $order->consignment_id && $order->courier_name && $found;
                        @endphp
                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center">
                                @if ($isCompleted)
                                    <div class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center shrink-0">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif ($isCurrent)
                                    <div class="w-4 h-4 rounded-full bg-indigo-600 flex items-center justify-center shrink-0 ring-4 ring-indigo-100 relative">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                    </div>
                                @else
                                    <div class="w-4 h-4 rounded-full bg-gray-200 shrink-0"></div>
                                @endif
                                @if (! $loop->last && ! $hasCourierBetween)
                                    <div class="w-0.5 h-8 {{ $isCompleted ? 'bg-emerald-300' : ($isActive ? 'bg-indigo-300' : 'bg-gray-200') }}" style="margin-top: 0;"></div>
                                @endif
                            </div>
                            <div class="pb-6 flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold {{ $isActive ? 'text-gray-900' : 'text-gray-400' }}">
                                        @if ($isCurrent && $order->courier_name && in_array($order->status, ['shipped', 'completed', 'delivered']))
                                            Shipped by {{ $courierLabel }}
                                        @else
                                            {{ ucfirst($status) }}
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-2">
                                        @if ($isCompleted)
                                            <span class="text-xs text-emerald-600 font-medium">Done</span>
                                        @endif
                                        @if ($isCurrent && $order->courier_name && in_array($order->status, ['shipped', 'completed', 'delivered']) && $order->tracking_link)
                                            @php
                                                $externalUrl = $order->tracking_link;
                                            @endphp
                                            <a href="{{ $externalUrl }}" target="_blank" rel="noopener"
                                               class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                Track on {{ $courierLabel }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @if ($isCurrent)
                                    @if ($order->tracking_code || $order->consignment_id)
                                        <p class="text-xs text-indigo-600 mt-1 font-mono">Tracking: {{ $trackingId }}</p>
                                    @else
                                        <p class="text-xs text-indigo-600 mt-1">Current status</p>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if ($hasCourierBetween)
                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-0.5 {{ $isActive ? 'bg-indigo-300' : 'bg-gray-200' }}" style="min-height: 260px; margin-top: 0;"></div>
                                </div>
                                <div class="pb-6 flex-1 pt-1">
                                    @if ($courierCancelled)
                                        <div class="bg-red-50 border border-red-100 rounded-xl p-5 flex items-start gap-4">
                                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-red-800">Shipment Cancelled</p>
                                                <p class="text-xs text-red-600 mt-1">The courier shipment has been cancelled by {{ $courierLabel }}. Please contact support for assistance.</p>
                                            </div>
                                        </div>
                                    @elseif ($courierReturned)
                                        <div class="bg-amber-50 border border-amber-100 rounded-xl p-5 flex items-start gap-4">
                                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-amber-800">Shipment Returned</p>
                                                <p class="text-xs text-amber-600 mt-1">The courier shipment was returned by {{ $courierLabel }}. Please contact support for assistance.</p>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Horizontal Progress Bar --}}
                                        <div class="flex items-start w-full mb-6">
                                            @foreach ($trackingSteps as $i => $step)
                                                @php
                                                    $done = $i < $currentStep;
                                                    $active = $i === $currentStep && ! $isDelivered;
                                                @endphp
                                                <div class="flex-1 text-center">
                                                    <div class="h-7 flex items-center justify-center mb-1">
                                                        @if ($active)
                                                            <svg class="w-7 h-7 text-red-500" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div class="w-full h-1.5 rounded-full {{ $done ? 'bg-red-500' : ($active ? 'bg-gradient-to-r from-red-500 to-gray-200' : 'bg-gray-200') }}"></div>
                                                    <p class="text-[11px] sm:text-xs mt-2 font-medium {{ $done || $active ? 'text-gray-900' : 'text-gray-400' }}">{{ $step }}</p>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Vertical Timeline --}}
                                        @if ($courierTracking && $courierTracking['success'])
                                            <div class="border-t border-gray-100 pt-4">
                                                @foreach ($trackingSteps as $i => $step)
                                                    @php
                                                        $done = $i < $currentStep;
                                                        $active = $i === $currentStep && ! $isDelivered;
                                                        $isFuture = $i > $currentStep;
                                                    @endphp
                                                    @if (! $isFuture)
                                                        <div class="flex mt-0 {{ $done || $active ? '' : 'opacity-40' }}">
                                                            <div class="flex flex-col items-center mr-3.5 self-stretch">
                                                                @if ($done || $active)
                                                                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $done ? 'bg-emerald-100' : 'bg-red-100' }}">
                                                                        @if ($done)
                                                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                        @else
                                                                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                                                        @endif
                                                                    </div>
                                                                    @if (! $loop->last)
                                                                        <div class="w-0.5 flex-1 {{ $done ? 'bg-emerald-200' : 'bg-gray-200' }}"></div>
                                                                    @endif
                                                                @else
                                                                    <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                                                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="flex-1 pb-5">
                                                                <h4 class="font-semibold text-sm {{ $done || $active ? 'text-gray-900' : 'text-gray-400' }}">{{ $step }}</h4>
                                                                @if ($done)
                                                                    <p class="text-xs text-emerald-600 mt-0.5 font-medium">Completed</p>
                                                                @elseif ($active)
                                                                    <p class="text-xs text-red-500 mt-0.5 font-medium">Current status</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-400 text-center py-4 border-t border-gray-100">
                                                Tracking information is not available yet.
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if ($cancelled)
                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-4 h-4 rounded-full bg-red-500 flex items-center justify-center shrink-0 ring-4 ring-red-100">
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Cancelled</p>
                                <p class="text-xs text-red-600 mt-0.5">This order was cancelled</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order Details --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Order Details</h4>
                </div>

                <div class="px-6 sm:px-8 py-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs text-gray-500 mb-1">Customer</p>
                            <p class="text-sm font-semibold text-gray-900">{{ trim(($order->first_name ?? '').' '.($order->last_name ?? '')) ?: 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs text-gray-500 mb-1">Phone</p>
                            <p class="text-sm font-semibold text-gray-900 font-mono">{{ $order->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs text-gray-500 mb-1">Date</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs text-gray-500 mb-1">Payment</p>
                            <p class="text-sm font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs text-gray-500 mb-1">Delivery</p>
                            <p class="text-sm font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $order->delivery_option) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs text-gray-500 mb-1">Items</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="px-6 sm:px-8 pb-6">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Items</h4>
                    <div class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <div class="flex items-center gap-4 py-3.5 first:pt-0 last:pb-0">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center shrink-0 border border-gray-100">
                                    @if ($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="" class="w-10 h-10 object-contain">
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product->name }}</p>
                                    @if ($item->variant)
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach(explode(' / ', $item->variant) as $v)
                                                <span class="inline-block bg-indigo-50 text-indigo-700 text-[11px] font-medium px-2 py-0.5 rounded">{{ $v }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if ($item->custom_fields_data && count($item->custom_fields_data) > 0)
                                        @foreach ($item->custom_fields_data as $label => $value)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $label }}:
                                                @if (is_array($value))
                                                    <span class="text-gray-700 font-medium">{{ basename($value['path'] ?? $value['name'] ?? json_encode($value)) }}</span>
                                                @elseif(is_string($value) && preg_match('/\.\w{2,5}$/', $value) && str_contains($value, '/'))
                                                    <a href="{{ asset('storage/' . $value) }}" target="_blank" class="text-indigo-600 font-medium underline">{{ basename($value) }}</a>
                                                @else
                                                    <span class="text-gray-700 font-medium">{{ $value }}</span>
                                                @endif
                                            </p>
                                        @endforeach
                                    @endif
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item->quantity }} × {{ $currencySymbol }}{{ number_format($item->price, 2) }}</p>
                                </div>
                                <span class="text-sm font-bold text-gray-900 font-mono">{{ $currencySymbol }}{{ number_format($item->price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Subtotal</span>
                            <span class="text-sm text-gray-700 font-mono">{{ $currencySymbol }}{{ number_format($order->total_price - $order->delivery_charge, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Delivery Charge</span>
                            <span class="text-sm text-gray-700 font-mono">{{ $currencySymbol }}{{ number_format($order->delivery_charge, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                            <span class="text-sm font-bold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-indigo-600 font-mono">{{ $currencySymbol }}{{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
