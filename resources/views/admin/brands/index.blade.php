<x-admin-layout>
    <div x-data="brandTree()" @keydown.escape.window="selected = []">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Brands</h2>
                <p class="mt-0.5 text-sm text-gray-500">Manage product brands and sub-brands.</p>
            </div>
            <a href="{{ route('admin.brands.create') }}"
               class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Brand
            </a>
        </div>

        {{-- Bulk Actions Bar --}}
        <div x-show="selected.length > 0" x-cloak
             class="mb-4 flex items-center justify-between px-4 py-2.5 bg-indigo-50 rounded-xl border border-indigo-200">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-indigo-900" x-text="selected.length + ' brand(s) selected'"></span>
                <button @click="selected = []" type="button"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors underline underline-offset-2">
                    Clear selection
                </button>
            </div>
            <form method="POST" action="{{ route('admin.brands.bulk-delete') }}" @submit.prevent="submitBulkDelete"
                  class="flex items-center gap-2">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <select x-model="bulkAction"
                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                    <option value="">Bulk actions</option>
                    <option value="delete">Delete</option>
                </select>
                <button type="submit"
                        class="px-4 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-50"
                        :disabled="!bulkAction">
                    Apply
                </button>
            </form>
        </div>

        {{-- Summary --}}
        @php
            $rootBrands = $brands->where('depth', 0);
            $allChildren = $brands->where('depth', '>', 0);
        @endphp
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-2xl font-bold text-gray-900">{{ $rootBrands->count() }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Parent brands</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-2xl font-bold text-gray-900">{{ $allChildren->count() }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Sub-brands</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-2xl font-bold text-indigo-600">{{ $brands->sum('products_count') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total products</p>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-6">
            <form method="GET" class="flex items-center gap-3">
                <div class="relative flex-1 max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search brands by name or slug..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                </div>
                <button type="submit"
                        class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    Search
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.brands.index') }}"
                       class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Tree Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="w-12 px-4 py-3.5">
                            <input type="checkbox" @click="toggleAll($event.target.checked)"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Name</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Slug</th>
                        <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Products</th>
                        <th class="text-right px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($brands as $brand)
                        @php $isRoot = $brand->depth === 0; @endphp
                        <tr class="transition-colors"
                            :class="{ 'bg-indigo-50/40': selected.includes({{ $brand->id }}) }"
                            @class(['bg-gray-50/80' => $isRoot])>
                            <td class="px-4 py-3 align-middle">
                                <input type="checkbox" :checked="selected.includes({{ $brand->id }})"
                                       @click="toggle({{ $brand->id }})"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="flex items-center gap-2.5">
                                    @if ($brand->depth > 0)
                                        <div class="flex items-end shrink-0 self-stretch" style="width: {{ $brand->depth * 20 }}px">
                                            @for ($i = 0; $i < $brand->depth; $i++)
                                                <div class="w-5 flex items-center justify-center self-stretch">
                                                    <div class="w-px h-full bg-gray-200"></div>
                                                </div>
                                            @endfor
                                        </div>
                                        <div class="relative w-4 shrink-0 self-stretch">
                                            <div class="absolute left-0 top-0 w-3 h-1/2 border-b border-l border-gray-300 rounded-bl"></div>
                                        </div>
                                    @endif

                                    @if ($brand->image)
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 overflow-hidden shrink-0 ring-1 ring-gray-200">
                                            <img src="{{ Storage::url($brand->image) }}" alt="" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 ring-1 ring-gray-200"
                                             style="background-color: {{ $isRoot ? 'rgb(238 242 255)' : 'rgb(249 250 251)' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                 style="color: {{ $isRoot ? 'rgb(99 102 241)' : 'rgb(156 163 175)' }}">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="{{ $isRoot ? 'font-semibold text-gray-900' : 'text-gray-900' }}">{{ $brand->name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 align-middle {{ $isRoot ? 'text-gray-500' : 'text-gray-400' }} font-mono text-xs">{{ $brand->slug }}</td>
                            <td class="px-5 py-3 align-middle text-center">
                                <span class="inline-flex items-center justify-center min-w-[26px] h-6 px-1.5 rounded-full text-xs font-semibold"
                                      style="background-color: {{ $isRoot ? 'rgb(238 242 255)' : 'rgb(243 244 246)' }}; color: {{ $isRoot ? 'rgb(67 56 202)' : 'rgb(75 85 99)' }}">
                                    {{ $brand->products_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3 align-middle text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.brands.edit', $brand) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-600 hover:bg-indigo-50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    @if ($brand->products_count === 0)
                                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST"
                                              onsubmit="return confirm('Delete {{ $brand->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
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
                            <td colspan="5" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <p class="text-sm text-gray-500">No brands found.</p>
                                <a href="{{ route('admin.brands.create') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors mt-1 inline-block">
                                    Create your first brand
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        function brandTree() {
            return {
                selected: [],
                bulkAction: '',
                get bulkActionUrl() {
                    return this.bulkAction === 'delete' ? '{{ route("admin.brands.bulk-delete") }}' : '#';
                },
                toggle(id) {
                    const idx = this.selected.indexOf(id);
                    if (idx === -1) { this.selected.push(id); } else { this.selected.splice(idx, 1); }
                },
                toggleAll(checked) {
                    const ids = JSON.parse('{{ $brands->pluck("id")->toJson() }}');
                    this.selected = checked ? ids : [];
                },
                submitBulkDelete(e) {
                    if (this.bulkAction === 'delete') {
                        if (!confirm('Delete ' + this.selected.length + ' selected brand(s)?')) {
                            e.preventDefault();
                            return;
                        }
                        e.target.submit();
                    }
                }
            };
        }
    </script>
    @endpush
</x-admin-layout>