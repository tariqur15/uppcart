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
                        <span class="text-gray-700">Edit #{{ $review->id }}</span>
                    </nav>
                    <h1 class="text-lg font-semibold text-gray-900 leading-tight">Edit Review</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reviews.show', $review) }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" form="review-form"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    Save
                </button>
            </div>
        </div>

        <div class="max-w-2xl">
            <form id="review-form" method="POST" action="{{ route('admin.reviews.update', $review) }}">
                @csrf
                @method('PATCH')

                {{-- Product & Customer info --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Review Information</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Product</span>
                            <p class="mt-0.5 text-sm text-gray-900">{{ $review->product->name ?? 'Deleted product' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</span>
                            <p class="mt-0.5 text-sm text-gray-900">{{ $review->user?->name ?? 'Guest' }}</p>
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-4">
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-1.5">Rating</label>
                        <select name="rating" id="rating" required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ old('rating', $review->rating) == $i ? 'selected' : '' }}>{{ $i }} {{ str_repeat('★', $i) }}</option>
                            @endfor
                        </select>
                        @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Title --}}
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $review->title) }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Optional review title">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Body --}}
                    <div class="mb-4">
                        <label for="body" class="block text-sm font-medium text-gray-700 mb-1.5">Review</label>
                        <textarea name="body" id="body" rows="6" required
                                  class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"
                                  placeholder="The customer's review...">{{ old('body', $review->body) }}</textarea>
                        @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Moderation --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Moderation</h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_approved" value="1"
                               {{ old('is_approved', $review->is_approved) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Approved</span>
                    </label>
                    <p class="text-xs text-gray-400 mt-1.5">Approved reviews are visible on the product page.</p>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>