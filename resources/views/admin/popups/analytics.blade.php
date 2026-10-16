<x-admin-layout>
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.popups.index') }}" class="hover:text-indigo-600 transition-colors">Popups</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">Analytics</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Popup performance</h1>
    <p class="text-sm text-gray-500 mb-8">{{ $popup->name ?: 'Untitled popup' }}</p>

    <x-flash-messages />

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Impressions</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['impressions']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Views</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['views']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['clicks']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Closes</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['closes']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">CTR</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['ctr'] }}%</p>
        </div>
    </div>

    <div class="flex justify-end mb-6">
        <a href="{{ route('admin.popups.edit', $popup) }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
            Edit popup
        </a>
    </div>
</x-admin-layout>
