<x-admin-layout>
    <div>
        {{-- Breadcrumb header --}}
        <div class="sticky top-0 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-white/90 backdrop-blur-md border-b border-gray-200 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reviews.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <nav class="text-xs text-gray-400 mb-0.5">
                        <a href="{{ route('admin.reviews.index') }}" class="hover:text-gray-600 transition-colors">Reviews</a>
                        <span class="mx-1">/</span>
                        <span class="text-gray-700">Review #{{ $review->id }}</span>
                    </nav>
                    <h1 class="text-lg font-semibold text-gray-900 leading-tight">Review Details</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reviews.edit', $review) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    Edit Review
                </a>
                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                      onsubmit="return confirm('Delete this review permanently?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 border border-red-300 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="max-w-3xl">
            {{-- Review Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl text-amber-500 font-medium">
                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            </span>
                            <span class="text-sm text-gray-400">({{ $review->rating }}/5)</span>
                        </div>
                        @if ($review->title)
                            <h2 class="text-xl font-semibold text-gray-900">{{ $review->title }}</h2>
                        @endif
                    </div>
                    @if ($review->is_approved)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Approved
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pending
                        </span>
                    @endif
                </div>

                {{-- Review body --}}
                <div class="prose prose-sm max-w-none text-gray-700 mb-6">
                    {{ $review->body }}
                </div>

                {{-- Meta --}}
                <div class="border-t border-gray-100 pt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Product</span>
                        <p class="mt-0.5 text-gray-900 font-medium">
                            {{ $review->product->name ?? 'Deleted product' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</span>
                        <p class="mt-0.5 text-gray-900">{{ $review->user?->name ?? 'Guest' }}</p>
                        @if ($review->user?->email)
                            <p class="text-xs text-gray-400">{{ $review->user->email }}</p>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</span>
                        <p class="mt-0.5 text-gray-900">{{ $review->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Last updated</span>
                        <p class="mt-0.5 text-gray-900">{{ $review->updated_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Back link --}}
            <div class="mt-6">
                <a href="{{ route('admin.reviews.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to reviews
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>