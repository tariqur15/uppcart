<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.banners.index') }}" class="hover:text-indigo-600 transition-colors">Banners</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">Edit banner</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Edit banner</h1>

        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Banner details</h2>
                </div>
                <div class="px-6 py-5 space-y-5">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-gray-400 font-normal">(internal — not visible on frontend)</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $banner->title) }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="link" class="block text-sm font-medium text-gray-700 mb-1">Link URL <span class="text-gray-400 font-normal">(entire banner will be clickable)</span></label>
                        <input type="text" name="link" id="link" value="{{ old('link', $banner->link) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('link') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image</label>
                        <div class="flex items-start gap-5">
                            <div class="w-40 h-24 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0" id="imagePreview">
                                @if ($banner->image)
                                    <img src="{{ Storage::url($banner->image) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label for="image" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 cursor-pointer transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Change image
                                </label>
                                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewBannerImage(this)">
                                @if ($banner->image)
                                    <label class="inline-flex items-center gap-1.5 mt-2 text-sm text-red-600 cursor-pointer">
                                        <input type="checkbox" name="remove_image" value="1">
                                        Remove current image
                                    </label>
                                @endif
                                <p class="mt-1.5 text-xs text-gray-400">Recommended size: 1920 x 600px. JPG, PNG, or WebP. Max 2MB.</p>
                            </div>
                        </div>
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                        </div>
                        <div>
                            <label for="sort_order" class="text-sm font-medium text-gray-700 mr-2">Sort order</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0"
                                   class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                    Update banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="text-sm text-gray-600 hover:text-gray-800 transition-colors">Cancel</a>
            </div>
        </form>
    </div>

    <script>
    function previewBannerImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</x-admin-layout>
