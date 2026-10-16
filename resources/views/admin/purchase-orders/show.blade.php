@php
    $statusStyles = [
        'pending'   => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'label' => 'Pending'],
        'received'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'label' => 'Received'],
        'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'label' => 'Cancelled'],
    ];
    $s = $statusStyles[$purchaseOrder->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'label' => ucfirst($purchaseOrder->status)];
@endphp

<x-admin-layout>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.purchase-orders.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <nav class="text-xs text-gray-400 mb-0.5">
                <a href="{{ route('admin.purchase-orders.index') }}" class="hover:text-gray-600 transition-colors">Purchase Orders</a>
                <span class="mx-1">/</span>
                <span class="text-gray-700">{{ $purchaseOrder->order_number }}</span>
            </nav>
            <h1 class="text-lg font-semibold text-gray-900 leading-tight">{{ $purchaseOrder->order_number }}</h1>
        </div>
        <div class="ml-auto flex items-center gap-2">
            @if ($purchaseOrder->isPending())
                <form action="{{ route('admin.purchase-orders.receive', $purchaseOrder) }}" method="POST"
                      onsubmit="return confirm('Mark as received? Product stock will be increased.')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark as Received
                    </button>
                </form>
                <form action="{{ route('admin.purchase-orders.cancel', $purchaseOrder) }}" method="POST"
                      onsubmit="return confirm('Cancel this purchase order?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors">
                        Cancel Order
                    </button>
                </form>
                <form action="{{ route('admin.purchase-orders.destroy', $purchaseOrder) }}" method="POST"
                      onsubmit="return confirm('Delete this purchase order?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            @else
                <form action="{{ route('admin.purchase-orders.destroy', $purchaseOrder) }}" method="POST"
                      onsubmit="return confirm('Delete this purchase order?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Status banner --}}
    <div class="mb-6 flex flex-wrap items-center gap-3 rounded-xl border px-4 py-3 {{ $s['bg'] }} {{ $s['text'] }}">
        <span class="inline-flex items-center gap-2 text-sm font-semibold">
            <span class="w-2 h-2 rounded-full bg-current"></span>
            {{ $s['label'] }}
        </span>
        <span class="text-xs opacity-70">
            Created {{ $purchaseOrder->created_at->format('M d, Y h:i A') }}
            @if ($purchaseOrder->received_at)
                · Received {{ $purchaseOrder->received_at->format('M d, Y h:i A') }}
            @endif
        </span>
        <span class="ml-auto text-sm font-bold">৳{{ number_format($purchaseOrder->total_amount, 2) }}</span>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            {{-- Items --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Items ({{ $purchaseOrder->items->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit Cost</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($purchaseOrder->items as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                                                @if ($item->product?->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                                @if ($item->product?->sku)
                                                    <div class="text-xs text-gray-400 mt-0.5">{{ $item->product->sku }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-medium text-gray-700">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3.5 text-right text-gray-600">৳{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right font-semibold text-gray-900">৳{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-gray-200 bg-gray-50/50">
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total</td>
                                <td class="px-6 py-4 text-right text-base font-bold text-gray-900">৳{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if ($purchaseOrder->notes)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-2">Notes</h2>
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $purchaseOrder->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Supplier --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Supplier</h2>
                <a href="{{ route('admin.suppliers.edit', $purchaseOrder->supplier) }}" class="flex items-start gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $purchaseOrder->supplier->name }}</div>
                        @if ($purchaseOrder->supplier->contact_person)
                            <div class="text-sm text-gray-500 mt-0.5">{{ $purchaseOrder->supplier->contact_person }}</div>
                        @endif
                        @if ($purchaseOrder->supplier->phone)
                            <div class="text-sm text-gray-500">{{ $purchaseOrder->supplier->phone }}</div>
                        @endif
                        @if ($purchaseOrder->supplier->email)
                            <div class="text-sm text-gray-500">{{ $purchaseOrder->supplier->email }}</div>
                        @endif
                        @if ($purchaseOrder->supplier->address)
                            <div class="text-xs text-gray-400 mt-0.5">{{ $purchaseOrder->supplier->address }}</div>
                        @endif
                    </div>
                </a>
            </div>

            {{-- Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Details</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $s['bg'] }} {{ $s['text'] }}">{{ $s['label'] }}</span></dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Expected Delivery</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseOrder->expected_date?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Total Amount</dt>
                        <dd class="font-semibold text-gray-900">৳{{ number_format($purchaseOrder->total_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-admin-layout>