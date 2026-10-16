@php
    $statusStyles = [
        'pending'   => ['class' => 'bg-amber-50 text-amber-700'],
        'received'  => ['class' => 'bg-emerald-50 text-emerald-700'],
        'cancelled' => ['class' => 'bg-red-50 text-red-700'],
    ];
@endphp

<x-admin-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Purchase Orders</h1>
            <p class="mt-1 text-sm text-gray-500">Track orders placed with your suppliers and restock your inventory.</p>
        </div>
        <a href="{{ route('admin.purchase-orders.create') }}"
           class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Purchase Order
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        {{-- Filters --}}
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search order number..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                </div>
                <select name="status" class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All statuses</option>
                    @foreach (array_keys($statusStyles) as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <select name="supplier_id" class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All suppliers</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected((string) request('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    Filter
                </button>
                @if (request()->anyFilled(['search', 'status', 'supplier_id']))
                    <a href="{{ route('admin.purchase-orders.index') }}"
                       class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Order Number</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Expected</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($purchaseOrders as $order)
                        @php $s = $statusStyles[$order->status] ?? ['class' => 'bg-gray-50 text-gray-500']; @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.purchase-orders.show', $order) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                    {{ $order->order_number }}
                                </a>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-5 py-4 text-gray-700">{{ $order->supplier->name }}</td>
                            <td class="px-5 py-4 text-center text-gray-500">{{ $order->items_count }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-900">৳{{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-5 py-4 text-gray-500">{{ $order->expected_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $s['class'] }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.purchase-orders.show', $order) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-indigo-600 hover:bg-indigo-50 transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-14 h-14 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center mb-4">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">No purchase orders found</p>
                                    <p class="text-sm text-gray-500 mb-4">
                                        {{ request()->anyFilled(['search', 'status', 'supplier_id']) ? 'Try adjusting your filters.' : 'Get started by creating your first purchase order.' }}
                                    </p>
                                    @unless (request()->anyFilled(['search', 'status', 'supplier_id']))
                                        <a href="{{ route('admin.purchase-orders.create') }}"
                                           class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            New Purchase Order
                                        </a>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($purchaseOrders->hasPages())
        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing <span class="font-medium text-gray-700">{{ $purchaseOrders->firstItem() }}</span>
                to <span class="font-medium text-gray-700">{{ $purchaseOrders->lastItem() }}</span>
                of <span class="font-medium text-gray-700">{{ $purchaseOrders->total() }}</span> results
            </p>
            {{ $purchaseOrders->links() }}
        </div>
    @endif
</x-admin-layout>