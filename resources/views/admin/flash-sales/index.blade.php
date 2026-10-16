<x-admin-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Flash Sales</h1>
            <p class="mt-1 text-sm text-gray-500">Create, schedule and track your promotional flash sales.</p>
        </div>
        <a href="{{ route('admin.flash-sales.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Flash Sale
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg text-sm">{{ session('warning') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.flash-sales.index') }}"
          class="bg-white rounded-xl border border-gray-200 p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Sale name..."
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
            <select name="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                <option value="">All time</option>
                <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ $dateFilter === 'week' ? 'selected' : '' }}>This week</option>
                <option value="month" {{ $dateFilter === 'month' ? 'selected' : '' }}>This month</option>
                <option value="custom" {{ $dateFilter === 'custom' ? 'selected' : '' }}>Custom range</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}" class="w-full rounded-lg border border-gray-300 px-2 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}" class="w-full rounded-lg border border-gray-300 px-2 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
            </div>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Filter
            </button>
            <a href="{{ route('admin.flash-sales.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-300 rounded-lg">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.flash-sales.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                    </th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.flash-sales.index', array_merge(request()->query(), ['sort' => 'starts_at', 'direction' => $sort === 'starts_at' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Start</a>
                    </th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.flash-sales.index', array_merge(request()->query(), ['sort' => 'ends_at', 'direction' => $sort === 'ends_at' && $direction === 'asc' ? 'desc' : 'asc'])) }}">End</a>
                    </th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.flash-sales.index', array_merge(request()->query(), ['sort' => 'products'])) }}">Products</a>
                    </th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.flash-sales.index', array_merge(request()->query(), ['sort' => 'units'])) }}">Units</a>
                    </th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('admin.flash-sales.index', array_merge(request()->query(), ['sort' => 'revenue'])) }}">Revenue</a>
                    </th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($sales as $sale)
                    @php
                        $badge = match ($sale->effective_status) {
                            'active' => ['text' => 'Active', 'class' => 'bg-emerald-100 text-emerald-700'],
                            'scheduled' => ['text' => 'Scheduled', 'class' => 'bg-blue-100 text-blue-700'],
                            'ended' => ['text' => 'Ended', 'class' => 'bg-gray-200 text-gray-600'],
                            'cancelled' => ['text' => 'Cancelled', 'class' => 'bg-red-100 text-red-700'],
                            default => ['text' => 'Draft', 'class' => 'bg-amber-100 text-amber-700'],
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.flash-sales.show', $sale) }}" class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">{{ $sale->name }}</a>
                            @if ($sale->description)
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $sale->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $sale->starts_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $sale->ends_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-4 text-center text-gray-600">{{ $sale->products_count }}</td>
                        <td class="px-5 py-4 text-center text-gray-600">{{ $sale->units_sold ?? 0 }}</td>
                        <td class="px-5 py-4 text-right font-medium text-gray-900">{{ $currencySymbol }}{{ number_format($sale->revenue ?? 0, 2) }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.flash-sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors">View</a>
                                <a href="{{ route('admin.flash-sales.edit', $sale) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22l8.5-11.5H12L13 2z"/></svg>
                            <p class="text-gray-500">No flash sales found.</p>
                            <a href="{{ route('admin.flash-sales.create') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mt-2 inline-block">Create your first flash sale</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $sales->links() }}
    </div>
</x-admin-layout>
