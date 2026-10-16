<x-admin-layout>
    <div x-data="reviewBulkActions()">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reviews</h1>
                <p class="mt-1 text-sm text-gray-500">Manage customer feedback and product reviews.</p>
            </div>
            <a href="{{ route('admin.reviews.create') }}"
               class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Review
            </a>
        </div>

        {{-- Summary stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-2xl font-bold text-gray-900">{{ $reviews->total() }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total reviews</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-2xl font-bold text-emerald-600">{{ $reviews->where('is_approved', true)->count() }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Approved</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-2xl font-bold text-amber-600">{{ $reviews->where('is_approved', false)->count() }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Pending</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-2xl font-bold text-indigo-600">{{ number_format($reviews->avg('rating'), 1) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Avg rating</p>
            </div>
        </div>

        {{-- Bulk Action Toolbar --}}
        <div x-show="selected.length > 0" x-cloak
             class="mb-4 p-3 bg-indigo-50/80 border border-indigo-200 rounded-xl flex flex-wrap items-center gap-3 shadow-sm">
            <span class="text-sm font-medium text-indigo-700" x-text="`${selected.length} review(s) selected`"></span>
            <div class="flex flex-wrap gap-2 ml-auto">
                <form method="POST" action="{{ route('admin.reviews.bulk-approve') }}" class="inline">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit"
                            class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm">
                        Approve selected
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.bulk-delete') }}" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete the selected reviews?')">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit"
                            class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                        Delete selected
                    </button>
                </form>
                <button type="button" @click="clearAll()"
                        class="px-3 py-1.5 border border-gray-300 bg-white text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
                    Cancel
                </button>
            </div>
        </div>

        {{-- Filters + Table card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            {{-- Search & Filters --}}
            <div class="p-4 border-b border-gray-100">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search reviews, products, customers..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                    </div>
                    <select name="rating"
                            class="rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                        <option value="">All ratings</option>
                        @foreach ([5, 4, 3, 2, 1] as $r)
                            <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} ★</option>
                        @endforeach
                    </select>
                    <select name="approved"
                            class="rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                        <option value="">All statuses</option>
                        <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>Approved</option>
                        <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>Pending</option>
                    </select>
                    <button type="submit"
                            class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                        Filter
                    </button>
                    @if (request()->anyFilled(['search', 'rating', 'approved']))
                        <a href="{{ route('admin.reviews.index') }}"
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
                                       :checked="selected.length === @js($reviews->count()) && selected.length > 0"
                                       @change="toggleAll($event.target.checked)"
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rating</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Review</th>
                            <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($reviews as $review)
                            <tr class="hover:bg-gray-50/50 transition-colors"
                                :class="{ 'bg-indigo-50/30': selected.includes({{ $review->id }}) }">
                                <td class="w-12 px-5 py-4">
                                    <input type="checkbox"
                                           :checked="selected.includes({{ $review->id }})"
                                           @change="toggle({{ $review->id }})"
                                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                <td class="px-5 py-4 max-w-[200px]">
                                    <a href="{{ route('admin.reviews.show', $review) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors line-clamp-1">
                                        {{ $review->product->name ?? 'Deleted product' }}
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-sm text-gray-700">{{ $review->user?->name ?? 'Guest' }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-amber-500 text-sm font-medium">
                                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 max-w-[250px]">
                                    <div class="space-y-0.5">
                                        @if ($review->title)
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $review->title }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 line-clamp-2">{{ $review->body }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if ($review->is_approved)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right text-xs text-gray-400 whitespace-nowrap">
                                    {{ $review->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.reviews.show', $review) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>
                                        <a href="{{ route('admin.reviews.edit', $review) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-indigo-600 hover:bg-indigo-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                              onsubmit="return confirm('Delete this review?')" class="inline">
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-14 h-14 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center mb-4">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 mb-1">No reviews found</p>
                                        <p class="text-sm text-gray-500 mb-4">
                                            {{ request()->anyFilled(['search', 'rating', 'approved']) ? 'Try adjusting your filters.' : 'No reviews have been submitted yet.' }}
                                        </p>
                                        @unless (request()->anyFilled(['search', 'rating', 'approved']))
                                            <a href="{{ route('admin.reviews.create') }}"
                                               class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                New Review
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

        @if ($reviews->hasPages())
            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Showing <span class="font-medium text-gray-700">{{ $reviews->firstItem() }}</span>
                    to <span class="font-medium text-gray-700">{{ $reviews->lastItem() }}</span>
                    of <span class="font-medium text-gray-700">{{ $reviews->total() }}</span> results
                </p>
                <div class="flex items-center gap-1.5">
                    @if ($reviews->onFirstPage())
                        <span class="px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $reviews->previousPageUrl() }}"
                           class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Previous</a>
                    @endif

                    @foreach ($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                        @if ($page == $reviews->currentPage())
                            <span class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 border border-indigo-600 rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}"
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
        function reviewBulkActions() {
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
                    const ids = JSON.parse('{{ $reviews->pluck("id")->toJson() }}');
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