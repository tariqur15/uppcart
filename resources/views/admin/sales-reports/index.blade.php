<x-admin-layout>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales Reports</h2>
    </div>

    {{-- Date Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="from" class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="from" id="from" value="{{ $from }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="to" class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="to" id="to" value="{{ $to }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.sales-reports.index') }}"
               class="text-sm font-medium text-gray-500 hover:text-gray-700 px-3 py-2 transition-colors">
                Reset
            </a>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $thisPeriod }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $completedOrders }} completed</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-500">Items Sold</p>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalItems }}</p>
            <p class="text-xs text-gray-400 mt-1">units sold</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-500">Avg Order Value</p>
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($avgOrderValue, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">per completed order</p>
        </div>
    </div>

    {{-- Status Breakdown + Top Products --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        {{-- Status Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Orders by Status</h3>
            @if ($statusCounts->isEmpty())
                <p class="text-sm text-gray-400">No orders in this period.</p>
            @else
                <div class="space-y-3">
                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                        @if ($statusCounts->has($status))
                            @php
                                $colors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $maxCount = $statusCounts->max();
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 w-24">{{ $status }}</span>
                                <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ $maxCount > 0 ? ($statusCounts[$status] / $maxCount) * 100 : 0 }}%"
                                         @class([preg_replace('/text-\w+-\d+/', 'bg-indigo-500', $colors[$status])])></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-8 text-right">{{ $statusCounts[$status] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Top Products --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Top Products</h3>
            @if ($topProducts->isEmpty())
                <p class="text-sm text-gray-400">No sales data in this period.</p>
            @else
                <div class="space-y-3">
                    @foreach ($topProducts as $i => $item)
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-gray-400 w-5">{{ $i + 1 }}</span>
                            <span class="flex-1 text-sm font-medium text-gray-800 truncate">{{ $item->product?->name ?? 'Deleted Product' }}</span>
                            <span class="text-sm text-gray-500">{{ $item->total_qty }} sold</span>
                            <span class="text-sm font-semibold text-gray-900 w-24 text-right">{{ $currencySymbol }}{{ number_format($item->total_revenue, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Daily Sales Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Daily Sales</h3>
            <form action="{{ route('admin.sales-reports.export') }}" method="POST">
                @csrf
                <input type="hidden" name="from" value="{{ $from }}">
                <input type="hidden" name="to" value="{{ $to }}">
                <button type="submit"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                    Export CSV
                </button>
            </form>
        </div>

        @if ($dailySales->isEmpty())
            <div class="p-6 text-center text-sm text-gray-400">No completed sales in this period.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50/50 text-left">
                            <th class="px-5 py-3 font-semibold text-gray-600">Date</th>
                            <th class="px-5 py-3 font-semibold text-gray-600 text-right">Orders</th>
                            <th class="px-5 py-3 font-semibold text-gray-600 text-right">Revenue</th>
                            <th class="px-5 py-3 font-semibold text-gray-600" colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxDailyRevenue = $dailySales->max('revenue'); @endphp
                        @foreach ($dailySales as $day)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                <td class="px-5 py-3.5 text-gray-700 text-right">{{ $day->orders }}</td>
                                <td class="px-5 py-3.5 font-semibold text-gray-900 text-right">{{ $currencySymbol }}{{ number_format($day->revenue, 2) }}</td>
                                <td class="px-0 py-3.5 w-32">
                                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full"
                                             style="width: {{ $maxDailyRevenue > 0 ? ($day->revenue / $maxDailyRevenue) * 100 : 0 }}%"></div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-400 w-12 text-right">
                                    {{ $maxDailyRevenue > 0 ? round(($day->revenue / $maxDailyRevenue) * 100) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t bg-gray-50/50">
                            <td class="px-5 py-3.5 font-semibold text-gray-900">Total</td>
                            <td class="px-5 py-3.5 font-semibold text-gray-900 text-right">{{ $dailySales->sum('orders') }}</td>
                            <td class="px-5 py-3.5 font-bold text-gray-900 text-right">{{ $currencySymbol }}{{ number_format($dailySales->sum('revenue'), 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</x-admin-layout>
