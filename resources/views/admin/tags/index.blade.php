<x-admin-layout>
    <div x-data="tagBulkActions()">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tags</h1>
                <p class="mt-1 text-sm text-gray-500">Organize products with tags for better filtering and discovery.</p>
            </div>
            <a href="{{ route('admin.tags.create') }}"
               class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Tag
            </a>
        </div>

        {{-- Bulk Action Toolbar --}}
        <div x-show="selected.length > 0" x-cloak
             class="mb-4 p-3 bg-indigo-50/80 border border-indigo-200 rounded-xl flex flex-wrap items-center gap-3 shadow-sm">
            <span class="text-sm font-medium text-indigo-700" x-text="`${selected.length} tag(s) selected`"></span>
            <div class="flex flex-wrap gap-2 ml-auto">
                <form method="POST" action="{{ route('admin.tags.bulk-delete') }}" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete the selected tags?')">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit"
                            class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete selected
                        </span>
                    </button>
                </form>
                <button type="button" @click="clearAll()"
                        class="px-3 py-1.5 border border-gray-300 bg-white text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
                    Cancel
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            {{-- Search --}}
            <div class="p-4 border-b border-gray-100">
                <form method="GET" class="flex items-center gap-3">
                    <div class="relative flex-1 max-w-sm">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search tags by name or slug..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                    </div>
                    <button type="submit"
                            class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                        Search
                    </button>
                    @if (request('search'))
                        <a href="{{ route('admin.tags.index') }}"
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
                            <th class="w-12 px-5 py-3.5">
                                <input type="checkbox"
                                       :checked="selected.length === @js($tags->count()) && selected.length > 0"
                                       @change="toggleAll($event.target.checked)"
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Slug</th>
                            <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Products</th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tags as $tag)
                            <tr class="hover:bg-gray-50/50 transition-colors"
                                :class="{ 'bg-indigo-50/30': selected.includes({{ $tag->id }}) }">
                                <td class="w-12 px-5 py-4">
                                    <input type="checkbox"
                                           :checked="selected.includes({{ $tag->id }})"
                                           @change="toggle({{ $tag->id }})"
                                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $tag->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-400 text-xs">{{ $tag->slug }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $tag->products_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-400' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        {{ $tag->products_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.tags.edit', $tag) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-indigo-600 hover:bg-indigo-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        @if ($tag->products_count === 0)
                                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this tag?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-14 h-14 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center mb-4">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 mb-1">No tags found</p>
                                        <p class="text-sm text-gray-500 mb-4">
                                            {{ request('search') ? 'Try adjusting your search query.' : 'Get started by creating your first tag.' }}
                                        </p>
                                        @unless (request('search'))
                                            <a href="{{ route('admin.tags.create') }}"
                                               class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                New Tag
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

        @if ($tags->hasPages())
            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Showing <span class="font-medium text-gray-700">{{ $tags->firstItem() }}</span>
                    to <span class="font-medium text-gray-700">{{ $tags->lastItem() }}</span>
                    of <span class="font-medium text-gray-700">{{ $tags->total() }}</span> results
                </p>
                <div class="flex items-center gap-1.5">
                    @if ($tags->onFirstPage())
                        <span class="px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $tags->previousPageUrl() }}"
                           class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Previous</a>
                    @endif

                    @foreach ($tags->getUrlRange(1, $tags->lastPage()) as $page => $url)
                        @if ($page == $tags->currentPage())
                            <span class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 border border-indigo-600 rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($tags->hasMorePages())
                        <a href="{{ $tags->nextPageUrl() }}"
                           class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Next</a>
                    @else
                        <span class="px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @pushOnce('scripts')
    <script>
        function tagBulkActions() {
            return {
                selected: [],
                toggle(id) {
                    const idx = this.selected.indexOf(id);
                    if (idx === -1) {
                        this.selected.push(id);
                    } else {
                        this.selected.splice(idx, 1);
                    }
                },
                toggleAll(checked) {
                    const ids = JSON.parse('{{ $tags->pluck("id")->toJson() }}');
                    this.selected = checked ? ids : [];
                },
                clearAll() {
                    this.selected = [];
                },
            };
        }
    </script>
    @endPushOnce
</x-admin-layout>