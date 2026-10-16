<x-admin-layout>
    @php
        $mediaConfig = [
            'featuredUrl' => $category->image ? asset('storage/' . $category->image) : null,
        ];
    @endphp
    {{-- Sticky header bar --}}
    <div class="sticky top-0 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-white/90 backdrop-blur-md border-b border-gray-200 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <nav class="text-xs text-gray-400 mb-0.5">
                    <a href="{{ route('admin.categories.index') }}" class="hover:text-gray-600 transition-colors">Categories</a>
                    <span class="mx-1">/</span>
                    <span class="text-gray-700">{{ $category->name }}</span>
                </nav>
                <h1 class="text-lg font-semibold text-gray-900 leading-tight">Edit category</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.categories.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" form="category-form"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                Update
            </button>
        </div>
    </div>

    <form id="category-form" action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <input type="hidden" name="remove_image" x-model="removeImage ? '1' : ''">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Name --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                           class="w-full text-lg border-0 border-b-2 border-gray-200 px-0 py-2 focus:border-indigo-500 focus:ring-0 transition-colors bg-transparent"
                           placeholder="e.g. Clothing">
                    @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1.5">The name is how it appears on your site.</p>
                </div>

                {{-- Slug --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1.5">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Auto-generated from name">
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1.5">The &ldquo;slug&rdquo; is the URL-friendly version of the name.</p>
                </div>

                {{-- Description --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" id="description" rows="5"
                              class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"
                              placeholder="Brief description of this category...">{{ old('description', $category->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1.5">The description is not prominent by default; however, some themes may show it.</p>
                </div>

                {{-- Featured Image --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ removeImage: false }">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Featured Image</h3>
                    <div class="flex items-start gap-6">
                        <div class="w-28 h-28 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0" id="image-preview">
                            @if ($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" class="w-full h-full object-cover rounded-xl">
                            @else
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="image"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 border border-indigo-300 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-50 cursor-pointer transition self-start">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                {{ $category->image ? 'Replace image' : 'Upload image' }}
                            </label>
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                            @if ($category->image)
                                <button type="button" @click="removeImage = !removeImage"
                                        class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 transition-colors self-start"
                                        x-text="removeImage ? 'Undo remove' : 'Remove image'">
                                </button>
                            @endif
                            <p class="text-xs text-gray-400">JPEG, PNG, or WebP. Max 2MB.</p>
                        </div>
                    </div>
                    @error('image') <p class="text-red-500 text-xs mt-3">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Parent Category --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Parent Category</h3>
                    <div class="relative">
                        <select name="parent_id" id="parent_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 appearance-none bg-white">
                            <option value="">None &mdash; top level</option>
                            @foreach ($parentCategories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('parent_id', $category->parent_id) == $cat->id)>{{ $cat->hierarchy_prefix }}{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    @error('parent_id') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1.5">Assign a parent to create a hierarchy.</p>
                </div>

                {{-- Status & Order --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Featured</span>
                            <p class="text-xs text-gray-400 mt-0.5">Show on homepage</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="pt-4">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="0">
                        @error('sort_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1.5">Lower numbers appear first.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        document.getElementById('image')?.addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.innerHTML = '<img src="' + ev.target.result + '" class="w-full h-full object-cover rounded-xl">';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    @endpush
</x-admin-layout>
