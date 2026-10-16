<x-admin-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Popups</h1>
            <p class="mt-1 text-sm text-gray-500">Create and manage storefront marketing popups.</p>
        </div>
        <a href="{{ route('admin.popups.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Popup
        </a>
    </div>

    <x-flash-messages />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Popup</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trigger</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Views</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Clicks</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">CTR</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($popups as $popup)
                    @php
                        $cfg = $popup->getConfig();
                        $triggerLabel = $triggers[$cfg['trigger'] ?? 'after_seconds'] ?? 'After X seconds';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                    @if (! empty($cfg['image']))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($cfg['image']) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.popups.edit', $popup) }}" class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">{{ $popup->name ?: 'Untitled popup' }}</a>
                        </td>
                        <td class="px-5 py-4 text-center text-gray-500">{{ $triggerLabel }}</td>
                        <td class="px-5 py-4 text-center text-gray-500">{{ $popup->stats['views'] }}</td>
                        <td class="px-5 py-4 text-center text-gray-500">{{ $popup->stats['clicks'] }}</td>
                        <td class="px-5 py-4 text-center text-gray-500">{{ $popup->stats['ctr'] }}%</td>
                        <td class="px-5 py-4 text-center">
                            @if ($popup->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Active</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.popups.analytics', $popup) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Analytics</a>
                                <a href="{{ route('admin.popups.edit', $popup) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors">Edit</a>
                                <form action="{{ route('admin.popups.destroy', $popup) }}" method="POST" onsubmit="return confirm('Delete this popup?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <p class="text-gray-500">No popups yet.</p>
                            <a href="{{ route('admin.popups.create') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mt-2 inline-block">Create your first popup</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
