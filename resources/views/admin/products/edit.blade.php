<x-admin-layout>
    @php
        $mediaConfig = [
            'featuredUrl' => $product->image ? asset('storage/' . $product->image) : null,
            'existingImages' => $product->gallery_images ?? [],
        ];
    @endphp
    <script id="media-config" type="application/json">{!! json_encode($mediaConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_THROW_ON_ERROR) !!}</script>
    <div x-data="mediaManager()"
          x-init="document.getElementById('gallery_order').value = getOrder(); $watch('items', () => document.getElementById('gallery_order').value = getOrder())"
          @click.outside="showAddMenu = false; if (!showLibraryModal) {}">
        {{-- Sticky header bar --}}
        <div class="sticky top-0 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-white/90 backdrop-blur-md border-b border-gray-200 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <nav class="text-xs text-gray-400 mb-0.5">
                        <a href="{{ route('admin.products.index') }}" class="hover:text-gray-600 transition-colors">Products</a>
                        <span class="mx-1">/</span>
                        <span class="text-gray-700">{{ $product->name }}</span>
                    </nav>
                    <h1 class="text-lg font-semibold text-gray-900 leading-tight">Edit product</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" form="product-form"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    Save
                </button>
            </div>
        </div>

        <form id="product-form" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="gallery_order" id="gallery_order" value="">
            <template x-for="path in removedExisting" :key="path">
                <input type="hidden" name="remove_gallery[]" :value="path">
            </template>
            <input type="hidden" name="remove_featured" x-model="removeFeaturedFlag ? '1' : ''">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Product Name --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900">Product name</h2>
                                <p class="text-xs text-gray-400">The first thing customers will see.</p>
                            </div>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                               placeholder="e.g. Organic Cotton T-Shirt">
                        @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Short Description --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <label for="short_description_input" class="block text-sm font-medium text-gray-700 mb-1.5">Short description</label>

                        <input type="hidden" name="short_description" id="short_description_input" value="{{ old('short_description', $product->short_description) }}">

                        <div class="rich-editor-wrapper border border-gray-300 rounded-lg overflow-hidden">
                            <div x-data="richEditor"
                                 data-input="short_description_input"
                                 data-placeholder="A brief summary for product cards and listings..."
                                 data-maxlength="500"
                                 class="min-h-[120px]"
                                 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5"><span class="char-counter">0 / 500</span> characters</p>

                        @error('short_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1.5">Displayed in product cards and search results.</p>
                    </div>

                    {{-- Long Description (Rich Text) --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <label for="long_description_input" class="block text-sm font-medium text-gray-700 mb-1.5">Full description</label>

                        <input type="hidden" name="long_description" id="long_description_input" value="{{ old('long_description', $product->long_description) }}">

                        <div class="rich-editor-wrapper border border-gray-300 rounded-lg overflow-hidden">
                            <div x-data="richEditor"
                                 data-input="long_description_input"
                                 class="min-h-[300px]"
                                 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                            </div>
                        </div>

                        @error('long_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1.5">Detailed product description with rich formatting.</p>
                    </div>

                    {{-- Media - Media Manager Component --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 overflow-hidden">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4" style="font-family: sans-serif;">Media</h3>

                        {{-- ========== STATE 1: Empty State ========== --}}
                        <template x-if="isEmpty">
                            <div class="border-2 border-dashed border-[#999999] rounded-lg p-12 flex flex-col items-center justify-center text-center bg-gray-50/50"
                                 style="border-radius: 8px;"
                                 @dragover.prevent
                                 @drop.prevent="handleDropUpload($event)">
                                <div class="flex items-center gap-3 mb-3">
                                    <button type="button" @click="triggerGalleryUpload()"
                                            class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-800 hover:bg-gray-50 transition-colors cursor-pointer">
                                        Upload new
                                    </button>
                                    <button type="button" @click="openLibraryModal()"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors underline underline-offset-2">
                                        Select existing
                                    </button>
                                </div>
                                <p class="text-sm text-gray-400">Drag and drop images here, or click "Upload new"</p>
                            </div>
                        </template>

                        {{-- ========== STATE 2 & 3: Populated Gallery ========== --}}
                        <template x-if="!isEmpty">
                            <div>
                                {{-- Batch management bar (State 3) --}}
                                <div x-show="isManaging"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="flex items-center justify-between px-4 py-2.5 mb-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox"
                                               :checked="allSelected"
                                               @change="toggleSelectAll()"
                                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                        <span class="text-sm font-medium text-gray-700" x-text="`${selectedIndices.length} file${selectedIndices.length !== 1 ? 's' : ''} selected`"></span>
                                    </div>
                                    <button type="button" @click="removeSelected()"
                                            class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors">
                                        Remove
                                    </button>
                                </div>

                                {{-- Feature image + thumbnail grid (1 featured + 4 thumbnails + 1 overflow + 1 add = 7 slots) --}}
                                <div style="display: grid; grid-template-columns: 180px repeat(3, 120px); gap: 12px; align-content: start;">
                                    {{-- Feature Image Container (col 1, spans 2 rows = 252px) --}}
                                    <div style="grid-column: 1; grid-row: 1 / 3;" class="relative h-[252px] rounded-xl border border-[#D1D5DB] bg-white overflow-hidden"
                                         @dragover.prevent="dragOver($event)"
                                         @drop.prevent="drop(-1)">
                                        <template x-if="featuredPreview">
                                            <div class="relative w-full h-full group">
                                                <img :src="featuredPreview" class="w-full h-full object-contain pointer-events-none p-3">
                                                <span class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-gray-500/80 text-white text-[10px] font-medium px-2 py-0.5 rounded-full whitespace-nowrap">Featured</span>
                                                <button @click="removeFeatured(); document.getElementById('featured-input').value = ''" type="button"
                                                        class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded flex items-center justify-center text-xs hover:bg-red-600 transition-colors shadow-sm opacity-0 group-hover:opacity-100">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none rounded-[11px]">
                                                    <span class="text-white text-xs font-medium bg-black/60 px-2 py-1 rounded">Drop to replace</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!featuredPreview">
                                            <label for="featured-input" class="flex flex-col items-center justify-center w-full h-full cursor-pointer text-gray-300 hover:text-gray-400 transition-colors">
                                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-xs font-medium">Click to set featured</span>
                                            </label>
                                        </template>
                                        <input type="file" name="image" id="featured-input" accept="image/*"
                                               @change="setFeatured($event.target.files[0])"
                                               class="hidden">
                                    </div>

                                    {{-- Standard Thumbnails (auto-place into grid, max 4 items in x-for) --}}
                                    <template x-for="(item, index) in items.slice(0, 4)" :key="item.id">
                                        <div class="relative w-[120px] h-[120px] rounded-xl border border-[#D1D5DB] bg-white overflow-hidden group cursor-pointer"
                                             :class="{ 'ring-2 ring-indigo-500': selectedIndices.includes(index) }"
                                             draggable="true"
                                             @click="swapFeatured(index)"
                                             @dragstart="dragStart(index)"
                                             @dragover.prevent="dragOver($event)"
                                             @drop.prevent="drop(index)">
                                            {{-- Checkbox (always visible) --}}
                                            <div class="absolute top-1 left-1 z-10">
                                                <input type="checkbox"
                                                       :checked="selectedIndices.includes(index)"
                                                       @change="toggleSelect(index)"
                                                       @click.stop
                                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                            </div>

                                            {{-- Selected overlay --}}
                                            <div x-show="selectedIndices.includes(index)"
                                                 class="absolute inset-0 bg-indigo-100/60 rounded-[11px] pointer-events-none"></div>

                                            <img :src="item.url" class="w-full h-full object-contain pointer-events-none p-1">

                                            {{-- Remove button inside border --}}
                                            <button @click.stop="remove(index)" type="button"
                                                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded flex items-center justify-center text-[10px] hover:bg-red-600 transition-colors shadow-sm opacity-0 group-hover:opacity-100"
                                                    :class="{ 'hidden': selectedIndices.includes(index) }">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- 5th slot (col 3, row 2) — second item in second row: shows items[4] or overflow overlay --}}
                                    <template x-if="items.length >= 5">
                                        <div class="relative w-[120px] h-[120px] rounded-xl border border-[#D1D5DB] bg-white overflow-hidden group cursor-pointer"
                                             :class="{ 'ring-2 ring-indigo-500': selectedIndices.includes(4) }"
                                             draggable="true"
                                             @click="items.length > 5 ? overflowExpanded = !overflowExpanded : swapFeatured(4)"
                                             @dragstart="dragStart(4)"
                                             @dragover.prevent="dragOver($event)"
                                             @drop.prevent="drop(4)">
                                            {{-- Checkbox (always visible) --}}
                                            <div class="absolute top-1 left-1 z-10">
                                                <input type="checkbox"
                                                       :checked="selectedIndices.includes(4)"
                                                       @change="toggleSelect(4)"
                                                       @click.stop
                                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                            </div>

                                            {{-- Selected overlay --}}
                                            <div x-show="selectedIndices.includes(4)"
                                                 class="absolute inset-0 bg-indigo-100/60 rounded-[11px] pointer-events-none"></div>

                                            {{-- Show items[4] thumbnail (faded when overflow overlay is visible) --}}
                                            <img :src="items[4].url" class="w-full h-full object-contain pointer-events-none p-1"
                                                 :class="{ 'opacity-40': items.length > 5 && !overflowExpanded }">

                                            {{-- Remove button --}}
                                            <button @click.stop="remove(4)" type="button"
                                                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded flex items-center justify-center text-[10px] hover:bg-red-600 transition-colors shadow-sm opacity-0 group-hover:opacity-100"
                                                    :class="{ 'hidden': selectedIndices.includes(4) }">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>

                                            {{-- Overflow overlay when > 5 items and not expanded --}}
                                            <template x-if="items.length > 5 && !overflowExpanded">
                                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm rounded-xl flex items-center justify-center z-20 cursor-pointer">
                                                    <span class="text-white text-2xl font-bold" x-text="'+' + (items.length - 5)"></span>
                                                </div>
                                            </template>
                                            {{-- Chevron when expanded --}}
                                            <template x-if="overflowExpanded">
                                                <div class="absolute inset-0 bg-black/40 rounded-xl flex items-center justify-center z-20 cursor-pointer">
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Add Media button (col 4, row 2) — always at end of second row --}}
                                    <button type="button" @click="openLibraryModal()"
                                            style="grid-column: 4; grid-row: 2;"
                                            class="w-[120px] h-[120px] rounded-xl border-2 border-dashed border-[#BDBDBD] bg-[#F9F9F9] flex flex-col items-center justify-center hover:bg-gray-100 transition-colors cursor-pointer shrink-0">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span class="text-[11px] text-gray-400 mt-1 font-medium">Add Media</span>
                                    </button>
                                </div>

                                {{-- ===== Expanded overflow rows (items[5+]) ===== --}}
                                <template x-if="overflowExpanded && items.length > 5">
                                    <div class="mt-3">
                                        <div class="flex flex-wrap gap-3">
                                            <template x-for="(item, idx) in items.slice(5)" :key="item.id">
                                                <div class="relative w-[120px] h-[120px] rounded-xl border border-[#D1D5DB] bg-white overflow-hidden group cursor-pointer"
                                                     :class="{ 'ring-2 ring-indigo-500': selectedIndices.includes(5 + idx) }"
                                                     draggable="true"
                                                     @click="swapFeatured(5 + idx)"
                                                     @dragstart="dragStart(5 + idx)"
                                                     @dragover.prevent="dragOver($event)"
                                                     @drop.prevent="drop(5 + idx)">
                                                    <div class="absolute top-1 left-1 z-10">
                                                        <input type="checkbox"
                                                               :checked="selectedIndices.includes(5 + idx)"
                                                               @change="toggleSelect(5 + idx)"
                                                               @click.stop
                                                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                    </div>
                                                    <div x-show="selectedIndices.includes(5 + idx)"
                                                         class="absolute inset-0 bg-indigo-100/60 rounded-[11px] pointer-events-none"></div>
                                                    <img :src="item.url" class="w-full h-full object-contain pointer-events-none p-1">
                                                    <button @click.stop="remove(5 + idx)" type="button"
                                                            class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded flex items-center justify-center text-[10px] hover:bg-red-600 transition-colors shadow-sm opacity-0 group-hover:opacity-100"
                                                            :class="{ 'hidden': selectedIndices.includes(5 + idx) }">
                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                            {{-- Show less button --}}
                                            <button type="button" @click="overflowExpanded = false"
                                                    class="w-[120px] h-[120px] rounded-xl border-2 border-dashed border-[#BDBDBD] bg-[#F9F9F9] flex flex-col items-center justify-center hover:bg-gray-100 transition-colors cursor-pointer shrink-0">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                </svg>
                                                <span class="text-[11px] text-gray-400 mt-1 font-medium">Show less</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Hidden gallery file input (always in DOM so Upload new works from empty state) --}}
                        <input type="file" name="gallery_images[]" id="gallery-input" accept="image/*" multiple
                               @change="addFiles($event.target.files)"
                               class="hidden">

                        @error('image') <p class="text-red-500 text-xs mt-3">{{ $message }}</p> @enderror
                        @error('gallery_images.*') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        @error('gallery_images') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror

                        {{-- ========== STATE 4: Media Library Modal ========== --}}
                        <div x-show="showLibraryModal"
                             x-cloak
                             class="fixed inset-0 z-50 flex items-center justify-center p-4"
                             @keydown.escape.window="closeLibraryModal()">
                            {{-- Backdrop --}}
                            <div class="fixed inset-0 bg-black/50" @click="closeLibraryModal()"></div>

                            {{-- Modal --}}
                            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
                                {{-- Header --}}
                                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
                                    <h2 class="text-lg font-semibold text-gray-900">Select file</h2>
                                    <button @click="closeLibraryModal()" type="button"
                                            class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Search & Controls --}}
                                <div class="px-6 py-3 border-b border-gray-100 space-y-3 shrink-0">
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex-1">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                            <input type="text" x-model="libraryQuery"
                                                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   placeholder="Search media files...">
                                        </div>
                                        <select x-model="librarySort"
                                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="newest">Newest</option>
                                            <option value="oldest">Oldest</option>
                                            <option value="name">Name</option>
                                        </select>
                                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                            <button type="button" @click="libraryView = 'grid'"
                                                    class="p-2 transition-colors"
                                                    :class="libraryView === 'grid' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-400 hover:text-gray-600'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                                </svg>
                                            </button>
                                            <button type="button" @click="libraryView = 'list'"
                                                    class="p-2 transition-colors"
                                                    :class="libraryView === 'list' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-400 hover:text-gray-600'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Filter pills --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-500 rounded-full">All images</span>
                                    </div>
                                </div>

                                {{-- Dropzone --}}
                                <div class="mx-6 mt-4 border-2 border-dashed border-gray-300 rounded-xl p-5 text-center shrink-0"
                                     @dragover.prevent
                                     @drop.prevent="uploadFromLibraryDropzone($event.dataTransfer.files)">
                                    <div class="flex items-center justify-center gap-4 flex-wrap">
                                        <input type="file" multiple accept="image/*"
                                               class="hidden"
                                               x-ref="modalFileInput"
                                               @change="uploadFromLibraryDropzone($event.target.files)">
                                        <button type="button" @click="$refs.modalFileInput.click()"
                                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                                            Add media
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-3">Drag and drop images here</p>
                                    <template x-if="uploadingMedia">
                                        <p class="text-xs text-indigo-500 mt-2">Uploading...</p>
                                    </template>
                                </div>

                                {{-- Media grid / list --}}
                                <div class="flex-1 overflow-y-auto px-6 py-4">
                                    {{-- Loading --}}
                                    <template x-if="loadingLibrary">
                                        <div class="flex items-center justify-center py-16">
                                            <svg class="animate-spin h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            <span class="ml-3 text-sm text-gray-500">Loading media...</span>
                                        </div>
                                    </template>

                                    {{-- Empty --}}
                                    <template x-if="!loadingLibrary && libraryItems.length === 0">
                                        <div class="text-center py-16">
                                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-sm text-gray-500">No media files found.</p>
                                        </div>
                                    </template>

                                    {{-- Grid view --}}
                                    <template x-if="!loadingLibrary && libraryItems.length > 0 && libraryView === 'grid'">
                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                            <template x-for="(img, idx) in filteredLibraryItems" :key="img.url">
                                                <div @click="toggleLibrarySelect(idx)"
                                                     class="rounded-lg border-2 overflow-hidden cursor-pointer transition-all bg-white"
                                                     :class="librarySelectedIndices.includes(idx) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                                    <div class="relative">
                                                        <div class="absolute top-1.5 left-1.5 z-10">
                                                            <input type="checkbox"
                                                                   :checked="librarySelectedIndices.includes(idx)"
                                                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                        </div>
                                                        <div class="aspect-square bg-gray-50">
                                                            <img :src="img.url" class="w-full h-full object-cover">
                                                        </div>
                                                    </div>
                                                    <div class="px-2 py-1.5 text-center border-t border-gray-100">
                                                        <p class="text-[11px] text-gray-700 truncate max-w-full" x-text="img.name"></p>
                                                        <span class="text-[10px] font-medium text-gray-400" x-text="img.extension"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- List view --}}
                                    <template x-if="!loadingLibrary && libraryItems.length > 0 && libraryView === 'list'">
                                        <div class="space-y-1">
                                            <template x-for="(img, idx) in filteredLibraryItems" :key="img.url">
                                                <div @click="toggleLibrarySelect(idx)"
                                                     class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer transition-all"
                                                     :class="librarySelectedIndices.includes(idx) ? 'bg-indigo-50 border border-indigo-200' : 'border border-transparent hover:bg-gray-50'">
                                                    <input type="checkbox"
                                                           :checked="librarySelectedIndices.includes(idx)"
                                                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer shrink-0">
                                                    <div class="w-10 h-10 rounded bg-gray-100 overflow-hidden shrink-0">
                                                        <img :src="img.url" class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm text-gray-700 truncate" x-text="img.name"></p>
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-400 shrink-0" x-text="img.extension"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 shrink-0">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="clearLibrarySelection()"
                                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                            Clear selection
                                        </button>
                                        <button type="button" @click="deleteSelectedLibraryItems()" x-show="librarySelectedIndices.length > 0"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors shadow-sm">
                                            Delete selected
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="closeLibraryModal()"
                                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            Cancel
                                        </button>
                                        <button type="button" @click="confirmLibrarySelection()"
                                                class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                                            Done
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"
                         x-data="{
                             price: '{{ old('price', $product->price ?? '') }}' ? parseFloat('{{ old('price', $product->price ?? '') }}'.replace(/,/g, '')) : '',
                             salePrice: '{{ old('sale_price', $product->sale_price ?? '') }}' ? parseFloat('{{ old('sale_price', $product->sale_price ?? '') }}'.replace(/,/g, '')) : '',
                              cost: '{{ old('cost_price', $product->cost_price ?? '') }}' ? parseFloat('{{ old('cost_price', $product->cost_price ?? '') }}'.replace(/,/g, '')) : '',
                             get profit() {
                                 const c = parseFloat(this.cost) || 0;
                                 const s = parseFloat(this.salePrice) || 0;
                                 const p = parseFloat(this.price) || 0;
                                 return s > 0 ? s - c : p - c;
                             },
                             get margin() {
                                 const p = parseFloat(this.price) || 0;
                                 const s = parseFloat(this.salePrice) || 0;
                                 const base = s > 0 ? s : p;
                                 return base > 0 ? ((this.profit / base) * 100) : 0;
                             }
                         }">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Pricing</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" name="price" id="price"
                                           x-model="price"
                                           required
                                           class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="0.00">
                                </div>
                                @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1">Sale price</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" name="sale_price" id="sale_price"
                                           x-model="salePrice"
                                           class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="0.00">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Original price shown with strikethrough</p>
                                @error('sale_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Cost, Profit, Margin calculation row --}}
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="inline-flex items-start gap-4">
                                {{-- Cost --}}
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-xs font-medium text-gray-500">Cost price</span>
                                    <div class="relative w-32">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                        <input type="number" step="0.01" name="cost_price" x-model="cost"
                                               class="w-full pl-7 pr-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="0.00">
                                    </div>
                                </div>
                                {{-- Profit --}}
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-xs font-medium text-gray-500">Profit</span>
                                    <div class="border border-gray-300 rounded-lg px-4 py-2.5 bg-white min-w-[110px]">
                                        <span class="text-sm font-semibold" style="color: #1e514e;" x-text="(profit > 0 ? '$' : '-$') + Math.abs(profit).toFixed(2)"></span>
                                    </div>
                                </div>
                                {{-- Margin --}}
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-xs font-medium text-gray-500">Margin</span>
                                    <div class="border border-gray-300 rounded-lg px-4 py-2.5 bg-white min-w-[80px]">
                                        <span class="text-sm font-semibold" style="color: #1e514e;" x-text="margin.toFixed(0) + '%'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Inventory --}}
                    <div x-data="{ isPhysical: '{{ old('type', $product->type) }}' !== 'digital' }"
                         @product-type-changed.window="isPhysical = ($event.detail.type !== 'digital')"
                         x-show="isPhysical"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Inventory</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU (Stock Keeping Unit)</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="e.g. PRD-001">
                                @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="0">
                                <p class="text-xs text-gray-400 mt-1">Current quantity in stock.</p>
                                @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                <label for="show_stock" class="flex items-center gap-2 mt-2 text-sm font-medium text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="show_stock" id="show_stock" value="1"
                                           @checked((bool) old('show_stock', $product->show_stock))
                                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    Show stock quantity on storefront
                                </label>
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                                <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight) }}"
                                       step="0.01" min="0"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="0.00">
                                <p class="text-xs text-gray-400 mt-1">Used for weight-based free shipping.</p>
                                @error('weight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    @php
                        $df = old('digital_files', $product->digital_files ?? []);
                        $filesData = is_string($df) ? ($df !== '' ? json_decode($df, true) ?? [] : []) : $df;
                    @endphp
                    {{-- Digital Files --}}
                    <div x-data="{
                            isDigital: @js(old('type', $product->type) === 'digital'),
                            uploadUrl: @js(route('admin.products.upload-digital-file')),
                            csrfToken: document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                            files: @js($filesData),
                            downloadLimit: @js(old('download_limit', $product->download_limit ?? '')),
                            downloadExpiry: @js(old('download_expiry', $product->download_expiry ?? '')),
                            addFile() {
                                this.files.push({ name: '', path: '' });
                            },
                            removeFile(index) {
                                this.files.splice(index, 1);
                            },
                            handleFileUpload(index, event) {
                                const file = event.target.files[0];
                                if (!file) return;
                                this.files[index].name = file.name;
                                this.files[index].path = '';
                                this.files[index].uploading = true;
                                const fd = new FormData();
                                fd.append('file', file);
                                fd.append('_token', this.csrfToken);
                                fetch(this.uploadUrl, {
                                    method: 'POST',
                                    headers: { 'Accept': 'application/json' },
                                    body: fd
                                }).then(r => r.json()).then(data => {
                                    this.files[index].path = data.path || '';
                                    this.files[index].uploading = false;
                                }).catch(() => {
                                    this.files[index].uploading = false;
                                    alert('File upload failed. Please try again.');
                                });
                            }
                         }"
                         @product-type-changed.window="isDigital = ($event.detail.type === 'digital')"
                         x-show="isDigital"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">Downloads</h3>
                        <p class="text-xs text-gray-400 mb-5">Manage files customers receive after purchase.</p>

                        {{-- Downloadable Files --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Downloadable files</label>

                            {{-- File rows --}}
                            <div class="space-y-2">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1">
                                            <input type="text" x-model="file.name"
                                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   placeholder="File name">
                                        </div>
                                        <div class="flex-1">
                                            <input type="text" x-model="file.path" readonly
                                                   class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500 cursor-not-allowed"
                                                   placeholder="File URL">
                                        </div>
                                        <div class="relative">
                                            <label :for="'digital-file-' + index"
                                                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer whitespace-nowrap">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                </svg>
                                                Choose file
                                            </label>
                                            <input type="file" :id="'digital-file-' + index" accept=".zip,.pdf,.mp4,.rar,.7z,.doc,.docx"
                                                   @change="handleFileUpload(index, $event)"
                                                   class="hidden">
                                        </div>
                                        <button type="button" @click="removeFile(index)"
                                                class="p-2 text-gray-400 hover:text-red-500 transition-colors shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            {{-- Empty state --}}
                            <template x-if="files.length === 0">
                                <p class="text-xs text-gray-400 italic">No files added yet.</p>
                            </template>

                            {{-- Add file button --}}
                            <button type="button" @click="addFile()"
                                    class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add file
                            </button>

                            {{-- Hidden input to submit JSON --}}
                            <input type="hidden" name="digital_files" :value="isDigital ? JSON.stringify(files) : ''">
                        </div>

                        {{-- Download Limit & Expiry --}}
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <label for="download_limit" class="block text-sm font-medium text-gray-700 mb-1">Download limit</label>
                                <input type="number" name="download_limit" id="download_limit"
                                       x-model="downloadLimit"
                                       min="0"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Unlimited">
                                <p class="text-xs text-gray-400 mt-1">Leave empty for unlimited downloads.</p>
                                @error('download_limit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="download_expiry" class="block text-sm font-medium text-gray-700 mb-1">Download expiry (days)</label>
                                <input type="number" name="download_expiry" id="download_expiry"
                                       x-model="downloadExpiry"
                                       min="0"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Never">
                                <p class="text-xs text-gray-400 mt-1">Number of days before download link expires.</p>
                                @error('download_expiry') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Variants --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"
                         x-data="variantManager({
                             enabled: @js((bool) $product->variants_enabled),
                             options: @js($product->variant_options ?? []),
                             variantData: @js($product->variant_data ?? (object)[]),
                             productType: '{{ old('type', $product->type) }}',
                         })">
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Product type</label>
                            <select name="type" id="type"
                                    x-model="productType"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="physical">Physical</option>
                                <option value="digital">Digital</option>
                                <option value="variable">Variable</option>
                            </select>
                            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <template x-if="enabled">
                            <div>
                                <template x-if="options.length === 0">
                                    <div class="text-center py-8">
                                        <p class="text-sm text-gray-400 mb-4">No variants configured yet. Click below to add one, or import from selected attributes.</p>
                                        <button type="button" @click="importFromAttributes()"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 border border-indigo-300 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            Import from selected attributes
                                        </button>
                                    </div>
                                </template>

                                {{-- Option blocks: form (not done) or read-only preview (done) --}}
                                <template x-for="(opt, index) in options" :key="opt.id">
                                    <div class="mb-4 last:mb-0">
                                        {{-- Not done — editable form --}}
                                        <template x-if="!opt.done">
                                            <div class="border border-gray-200 rounded-xl bg-white p-5">
                                                <div class="flex gap-4">
                                                    <div @dragstart="handleDragStart($event, 'option', index)"
                                                         @dragover.prevent
                                                         @drop="handleDrop($event, 'option', index)"
                                                         class="flex items-center justify-center shrink-0 pt-1 cursor-grab active:cursor-grabbing"
                                                         draggable="true">
                                                        <svg class="w-3.5 h-3.5 text-gray-300" viewBox="0 0 14 14" fill="currentColor">
                                                            <circle cx="3" cy="2" r="1.2"/><circle cx="11" cy="2" r="1.2"/>
                                                            <circle cx="3" cy="7" r="1.2"/><circle cx="11" cy="7" r="1.2"/>
                                                            <circle cx="3" cy="12" r="1.2"/><circle cx="11" cy="12" r="1.2"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 space-y-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Option name</label>
                                                            <input type="text" x-model="opt.name"
                                                                   @input="opt.errors.name = false; opt.done = false"
                                                                   :class="opt.errors.name ? 'border-red-500 ring-2 ring-blue-400/60' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                                                                   class="w-full rounded-lg border px-4 py-2.5 text-sm transition-shadow variant-name-input"
                                                                   placeholder="e.g. Size">
                                                            <template x-if="opt.errors.name">
                                                                <p class="flex items-center gap-1 text-red-500 text-xs mt-1.5">
                                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span>Option name is required.</span>
                                                                </p>
                                                            </template>
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Option values</label>
                                                            <div class="space-y-2">
                                                                <template x-for="(val, vi) in opt.values" :key="vi">
                                                                    <div @dragstart="handleDragStart($event, 'value', vi, index)"
                                                                         @dragover.prevent="handleDragOver('value', index, vi)"
                                                                         @dragleave="clearDragOver"
                                                                         @drop="handleDrop($event, 'value', vi)"
                                                                         :class="dragOverValue?.optIndex === index && dragOverValue?.valIndex === vi ? 'ring-2 ring-indigo-400 bg-indigo-50/50 rounded-lg -mx-1 px-1' : ''"
                                                                         class="flex items-center gap-1.5 cursor-grab active:cursor-grabbing"
                                                                         draggable="true">
                                                                        <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" viewBox="0 0 14 14" fill="currentColor">
                                                                            <circle cx="3" cy="2" r="1.2"/><circle cx="11" cy="2" r="1.2"/>
                                                                            <circle cx="3" cy="7" r="1.2"/><circle cx="11" cy="7" r="1.2"/>
                                                                            <circle cx="3" cy="12" r="1.2"/><circle cx="11" cy="12" r="1.2"/>
                                                                        </svg>
                                                                        <input type="text" :value="val"
                                                                               @input="handleValueInput(index, vi, $event)"
                                                                               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                               placeholder="e.g. Small">
                                                                        <button type="button" @click="removeValue(index, vi)"
                                                                                x-show="val.trim() !== ''"
                                                                                class="text-gray-400 hover:text-red-500 transition-colors shrink-0">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center justify-between pt-1">
                                                            <button type="button" @click="removeOption(index)"
                                                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                                Delete
                                                            </button>
                                                            <button type="button" @click="validateOption(index)"
                                                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition-colors">
                                                                Done
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Done — read-only preview row --}}
                                        <template x-if="opt.done">
                                            <div class="flex items-center gap-4 py-3 px-1">
                                                <div @dragstart="handleDragStart($event, 'option', index)"
                                                     @dragover.prevent
                                                     @drop="handleDrop($event, 'option', index)"
                                                     class="flex items-center justify-center shrink-0 cursor-grab active:cursor-grabbing"
                                                     draggable="true">
                                                    <svg class="w-3.5 h-3.5 text-gray-300" viewBox="0 0 14 14" fill="currentColor">
                                                        <circle cx="3" cy="2" r="1.2"/><circle cx="11" cy="2" r="1.2"/>
                                                        <circle cx="3" cy="7" r="1.2"/><circle cx="11" cy="7" r="1.2"/>
                                                        <circle cx="3" cy="12" r="1.2"/><circle cx="11" cy="12" r="1.2"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 leading-tight" x-text="opt.name"></p>
                                                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                                                        <template x-for="val in opt.values.filter(v => v.trim())" :key="val">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700" x-text="val"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <button type="button" @click="editOption(index)"
                                                        class="shrink-0 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                                                    Edit
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                {{-- Add another option --}}
                                <button type="button" @click="addOption()"
                                        class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                        <path stroke-linecap="round" stroke-width="2" d="M12 8v8M8 12h8"/>
                                    </svg>
                                    Add another option
                                </button>

                                {{-- Generated variants matrix --}}
                                <template x-if="generatedVariants.length > 0">
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        {{-- Control header --}}
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm font-semibold text-gray-900">Group by</span>
                                                <select x-model="groupBy"
                                                        class="text-sm rounded-lg border border-gray-300 px-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    <template x-for="opt in options.filter(o => o.done && o.name.trim())" :key="opt.name">
                                                        <option :value="opt.name" x-text="opt.name"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="relative">
                                                <input type="text" x-model="searchQuery"
                                                       class="w-44 rounded-lg border border-gray-300 pl-9 pr-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                       placeholder="Search variants...">
                                                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                            </div>
                                        </div>

                                        {{-- Bulk edit toolbar --}}
                                        <template x-if="numSelected > 0">
                                            <div class="mb-4 flex items-center justify-between px-4 py-2.5 bg-gray-50 rounded-lg border border-gray-200">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-sm font-medium text-gray-700" x-text="numSelected + ' variant' + (numSelected > 1 ? 's' : '') + ' selected'"></span>
                                                    <button type="button" @click="openBulkEdit()"
                                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors underline underline-offset-2">
                                                        Bulk edit
                                                    </button>
                                                </div>
                                                <button type="button" @click="toggleAllSelect()"
                                                        class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                                    Clear selection
                                                </button>
                                            </div>
                                        </template>

                                        {{-- Bulk edit panel --}}
                                        <template x-if="bulkEditOpen">
                                            <div class="mb-4 p-4 bg-indigo-50/50 rounded-xl border border-indigo-200 space-y-3">
                                                <p class="text-sm font-semibold text-indigo-900">Bulk edit selected variants</p>
                                                <div class="grid grid-cols-4 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Price</label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm pointer-events-none">$</span>
                                                            <input type="number" step="0.01" x-model="bulkData.price"
                                                                   class="w-full rounded-lg border border-gray-300 pl-7 pr-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                   placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Sale price</label>
                                                        <div class="relative">
                                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm pointer-events-none">$</span>
                                                            <input type="number" step="0.01" x-model="bulkData.salePrice"
                                                                   class="w-full rounded-lg border border-gray-300 pl-7 pr-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                   placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Stock</label>
                                                        <input type="number" x-model="bulkData.stock"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                               placeholder="0">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">SKU</label>
                                                        <input type="text" x-model="bulkData.sku"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                               placeholder="e.g. PRD-">
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-end gap-2 pt-1">
                                                    <button type="button" @click="bulkEditOpen = false"
                                                            class="px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="button" @click="applyBulkEdit()"
                                                            class="px-4 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">
                                                        Apply
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Table --}}
                                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                                            <table class="w-full text-sm">
                                                <thead class="bg-gray-50 border-b border-gray-200">
                                                    <tr>
                                                        <th class="w-10 px-3 py-3">
                                                            <input type="checkbox" :checked="allSelected"
                                                                   @change="toggleAllSelect"
                                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                        </th>
                                                        <th class="text-left px-3 py-3 font-semibold text-gray-600">Variant</th>
                                                        <th class="text-left px-3 py-3 font-semibold text-gray-600">Price</th>
                                                        <th class="text-left px-3 py-3 font-semibold text-gray-600">Available</th>
                                                        <th class="w-10 px-3 py-3"></th>
                                                    </tr>
                                                </thead>
                                                <template x-for="grp in groupedVariants" :key="grp.value">
                                                    <tbody x-data="{ localExpanded: false }">
                                                        {{-- Parent group row --}}
                                                        <template x-if="!searchQuery || grp.value.toLowerCase().includes(searchQuery.toLowerCase()) || grp.children.some(c => c.name.toLowerCase().includes(searchQuery.toLowerCase()))">
                                                            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                                                                <td class="px-3 py-3">
                                                                    <input type="checkbox"
                                                                           :checked="grp.children.every(c => c.selected)"
                                                                           @change="grp.children.forEach(c => { if (!variantData[c.name]) variantData[c.name] = {}; variantData[c.name].selected = $event.target.checked; })"
                                                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                                </td>
                                                                <td class="px-3 py-3">
                                                                    <div class="flex items-center gap-3">
                                                                 <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0 relative overflow-hidden cursor-pointer"
                                                                               @click="openVariantLibraryForGroup(grp)">
                                                                              <template x-if="grp.children[0].image">
                                                                                  <img :src="grp.children[0].image" alt="" class="w-full h-full object-cover rounded-lg">
                                                                              </template>
                                                                              <template x-if="grp.children[0].image">
                                                                                  <button type="button" @click.stop="; grp.children.forEach(c => updateVariantData(c.name, 'image', ''))"
                                                                                          class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 z-10">
                                                                                      <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                                                                      </svg>
                                                                                  </button>
                                                                              </template>
                                                                              <template x-if="!grp.children[0].image">
                                                                                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                                  </svg>
                                                                              </template>
                                                                          </div>
                                                                        <div>
                                                                            <p class="font-medium text-gray-900 leading-tight" x-text="grp.value"></p>
                                                                            <p class="text-xs text-gray-500" x-text="grp.count + ' variant' + (grp.count > 1 ? 's' : '')"></p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="px-3 py-3">
                                                                    <div class="relative w-28">
                                                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm pointer-events-none">$</span>
                                                                        <span class="block w-full rounded-lg border border-gray-200 bg-gray-50 pl-7 pr-3 py-1.5 text-sm text-gray-700"
                                                                              x-text="grp.children[0].price || '0.00'"></span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-3 py-3">
                                                                    <div class="inline-flex items-center gap-2 bg-gray-100 rounded-full px-3 py-1.5">
                                                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                                        </svg>
                                                                        <span class="text-sm text-gray-700 tabular-nums min-w-[2rem] text-center"
                                                                              x-text="grp.children.reduce((sum, c) => sum + (parseInt(c.stock) || 0), 0)"></span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-3 py-3 text-right">
                                                                    <button type="button" @click="localExpanded = !localExpanded"
                                                                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                                                                        <svg class="w-4 h-4 transition-transform duration-200" :class="localExpanded ? 'rotate-180' : ''"
                                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                                        </svg>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </template>

                                                        {{-- Expandable nested sub-variants panel --}}
                                                        <tr :class="localExpanded ? '' : 'hidden'"
                                                            class="border-b border-gray-100 transition-all duration-200">
                                                            <td colspan="5" class="p-0">
                                                                <div class="relative bg-gray-50/40">
                                                                    {{-- Vertical dotted tree-line --}}
                                                                    <div class="absolute left-5 top-0 bottom-0 border-l-2 border-dotted border-gray-300"></div>

                                                                    <template x-for="(child, ci) in grp.children" :key="child.name">
                                                                        <div class="relative flex items-center gap-2 pl-12 pr-3 py-2.5">
                                                                            {{-- Horizontal dotted connector --}}
                                                                            <div class="absolute left-5 top-1/2 w-7 border-t-2 border-dotted border-gray-300"></div>

                                                                            {{-- Checkbox --}}
                                                                            <input type="checkbox"
                                                                                   :checked="child.selected"
                                                                                   @change="updateVariantData(child.name, 'selected', $event.target.checked)"
                                                                                   class="shrink-0 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                                                                             {{-- Image placeholder --}}
                                                                             <div class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0 relative overflow-hidden cursor-pointer"
                                                                                  @click="openVariantLibrary(child.name)">
                                                                                 <template x-if="child.image">
                                                                                     <img :src="child.image" alt="" class="w-full h-full object-cover rounded-lg">
                                                                                 </template>
                                                                                 <template x-if="child.image">
                                                                                     <button type="button" @click.stop="updateVariantData(child.name, 'image', '')"
                                                                                             class="absolute top-0.5 right-0.5 w-3.5 h-3.5 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 z-10">
                                                                                         <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                                                                         </svg>
                                                                                     </button>
                                                                                 </template>
                                                                                 <template x-if="!child.image">
                                                                                     <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                                     </svg>
                                                                                 </template>
                                                                             </div>

                                                                            {{-- Sub-variant name --}}
                                                                            <span class="text-sm font-medium text-gray-700 min-w-0 flex-1 truncate" x-text="getChildLabel(child)"></span>

                                                                            {{-- Individual Price input --}}
                                                                            <div class="relative w-24 shrink-0">
                                                                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-400 text-xs pointer-events-none">$</span>
                                                                                <input type="number" step="0.01"
                                                                                       :value="child.price"
                                                                                       @input="updateVariantData(child.name, 'price', $event.target.value)"
                                                                                       class="w-full rounded-lg border border-gray-300 pl-5 pr-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                                       placeholder="0.00">
                                                                            </div>

                                                                            {{-- Individual Available stock --}}
                                                                            <div class="inline-flex items-center gap-1 bg-white border border-gray-300 rounded-full px-3 py-1.5 shrink-0">
                                                                                <input type="number"
                                                                                       :value="child.stock"
                                                                                       @input="updateVariantData(child.name, 'stock', $event.target.value)"
                                                                                       class="w-12 bg-transparent border-none text-sm text-gray-700 text-center focus:outline-none"
                                                                                       placeholder="0">
                                                                            </div>


                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </template>
                                            </table>
                                        </div>

                                    </div>
                                </template>

                                {{-- Variant library modal (mirrors media section library) --}}
                                <template x-if="showVariantLibrary">
                                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                         @keydown.escape.window="closeVariantLibrary()">
                                        <div class="fixed inset-0 bg-black/50" @click="closeVariantLibrary()"></div>
                                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
                                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
                                                <h2 class="text-lg font-semibold text-gray-900">Select variant image</h2>
                                                <button @click="closeVariantLibrary()" type="button"
                                                        class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="px-6 py-3 border-b border-gray-100 space-y-3 shrink-0">
                                                <div class="flex items-center gap-3">
                                                    <div class="relative flex-1">
                                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                        </svg>
                                                        <input type="text" x-model="variantLibraryQuery"
                                                               class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                               placeholder="Search media files...">
                                                    </div>
                                                    <select x-model="variantLibrarySort"
                                                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="newest">Newest</option>
                                                        <option value="oldest">Oldest</option>
                                                        <option value="name">Name</option>
                                                    </select>
                                                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                                        <button type="button" @click="variantLibraryView = 'grid'"
                                                                class="p-2 transition-colors"
                                                                :class="variantLibraryView === 'grid' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-400 hover:text-gray-600'">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" @click="variantLibraryView = 'list'"
                                                                class="p-2 transition-colors"
                                                                :class="variantLibraryView === 'list' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-400 hover:text-gray-600'">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-500 rounded-full">All images</span>
                                                </div>
                                            </div>

                                            <div class="mx-6 mt-4 border-2 border-dashed border-gray-300 rounded-xl p-5 text-center shrink-0"
                                                 @dragover.prevent
                                                 @drop.prevent="uploadVariantFromDropzone($event.dataTransfer.files)">
                                                <div class="flex items-center justify-center gap-4 flex-wrap">
                                                    <input type="file" multiple accept="image/*"
                                                           class="hidden"
                                                           x-ref="variantModalFileInput"
                                                           @change="uploadVariantFromDropzone($event.target.files)">
                                                    <button type="button" @click="$refs.variantModalFileInput.click()"
                                                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                                                        Add media
                                                    </button>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-3">Drag and drop images here</p>
                                                <template x-if="uploadingVariantMedia">
                                                    <p class="text-xs text-indigo-500 mt-2">Uploading...</p>
                                                </template>
                                            </div>

                                            <div class="flex-1 overflow-y-auto px-6 py-4">
                                                <template x-if="loadingVariantLibrary">
                                                    <div class="flex items-center justify-center py-16">
                                                        <svg class="animate-spin h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                        </svg>
                                                        <span class="ml-3 text-sm text-gray-500">Loading media...</span>
                                                    </div>
                                                </template>

                                                <template x-if="!loadingVariantLibrary && variantLibraryItems.length === 0">
                                                    <div class="text-center py-16">
                                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <p class="text-sm text-gray-500">No media files found.</p>
                                                    </div>
                                                </template>

                                                <template x-if="!loadingVariantLibrary && variantLibraryItems.length > 0 && variantLibraryView === 'grid'">
                                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                                        <template x-for="(img, idx) in filteredVariantLibraryItems" :key="img.url">
                                                            <div @click="toggleVariantLibrarySelect(idx)"
                                                                  class="rounded-lg border-2 overflow-hidden cursor-pointer transition-all bg-white"
                                                                  :class="variantLibrarySelectedIndices.includes(idx) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                                                 <div class="relative">
                                                                     <div class="absolute top-1.5 left-1.5 z-10">
                                                                         <input type="checkbox"
                                                                                :checked="variantLibrarySelectedIndices.includes(idx)"
                                                                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                                     </div>
                                                                     <div class="aspect-square bg-gray-50">
                                                                        <img :src="img.url" class="w-full h-full object-cover">
                                                                    </div>
                                                                </div>
                                                                <div class="px-2 py-1.5 text-center border-t border-gray-100">
                                                                    <p class="text-[11px] text-gray-700 truncate max-w-full" x-text="img.name"></p>
                                                                    <span class="text-[10px] font-medium text-gray-400" x-text="img.extension"></span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>

                                                <template x-if="!loadingVariantLibrary && variantLibraryItems.length > 0 && variantLibraryView === 'list'">
                                                    <div class="space-y-1">
                                                        <template x-for="(img, idx) in filteredVariantLibraryItems" :key="img.url">
                                                            <div @click="toggleVariantLibrarySelect(idx)"
                                                                  class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer transition-all"
                                                                  :class="variantLibrarySelectedIndices.includes(idx) ? 'bg-indigo-50 border border-indigo-200' : 'border border-transparent hover:bg-gray-50'">
                                                                <input type="checkbox"
                                                                       :checked="variantLibrarySelectedIndices.includes(idx)"
                                                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer shrink-0">
                                                                <div class="w-10 h-10 rounded bg-gray-100 overflow-hidden shrink-0">
                                                                    <img :src="img.url" class="w-full h-full object-cover">
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-sm text-gray-700 truncate" x-text="img.name"></p>
                                                                </div>
                                                                 <span class="text-xs font-medium text-gray-400 shrink-0" x-text="img.extension"></span>
                                                             </div>
                                                         </template>
                                                     </div>
                                                 </template>
                                             </div>

                                             <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 shrink-0">
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="variantLibrarySelectedIndices = []"
                                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                                        Clear selection
                                                    </button>
                                                    <button type="button" @click="deleteSelectedVariantLibraryItems()" x-show="variantLibrarySelectedIndices.length > 0"
                                                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors shadow-sm">
                                                        Delete selected
                                                    </button>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="closeVariantLibrary()"
                                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="button" @click="confirmVariantLibrarySelection()"
                                                            class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                                                        Select
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Status --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Status</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Product status</label>
                                <select name="status" id="status"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="active" @selected(old('status', $product->status) === 'active')>Active</option>
                                    <option value="draft" @selected(old('status', $product->status) === 'draft')>Draft</option>
                                </select>
                                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="featured" value="1" @checked(old('featured', $product->featured))
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Featured</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Organization --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Organization</h3>
                        <div class="space-y-4">
                            <div x-data="categoryTree({{ $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'parent_id' => $c->parent_id])->values() }}, @js($selectedCategoryIds))">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <div class="relative" @click.outside="open = false">
                                    <div class="flex flex-wrap gap-1.5 items-center border border-gray-300 bg-white rounded-lg px-3 py-2 cursor-pointer"
                                         @click="open = !open">
                                        <template x-for="chip in chips" :key="chip.id">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-medium rounded-md">
                                                <span x-text="chip.name"></span>
                                                <button type="button" @click.stop="removeChip(chip.id)" class="text-indigo-400 hover:text-red-500 transition-colors" title="Remove">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </span>
                                        </template>
                                        <span class="text-sm text-gray-400" x-show="chips.length === 0">Select categories...</span>
                                        <input type="text" x-model="query"
                                               placeholder="Search categories..."
                                               class="flex-1 min-w-[120px] bg-transparent text-sm outline-none border-0 p-0 focus:ring-0"
                                               @focus="open = true" @click.stop>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-150 shrink-0" :class="open ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>

                                    <div x-show="open" x-cloak @click.stop
                                         class="absolute z-20 mt-1 left-0 right-0 border border-gray-200 bg-white rounded-lg shadow-lg p-2 max-h-96 overflow-y-auto">
                                        <template x-if="filtered.length === 0">
                                            <p class="text-xs text-gray-400 px-1 py-2 text-center">No categories found.</p>
                                        </template>
                                        <template x-for="node in filtered" :key="node.id">
                                            <div class="flex items-center gap-1 py-0.5"
                                                 :style="'padding-left:' + (node.depth * 14) + 'px'">
                                                <span class="w-4 shrink-0"></span>
                                                <label class="flex items-center gap-2 cursor-pointer select-none min-w-0">
                                                    <input type="checkbox" :checked="isChecked(node.id)"
                                                           :indeterminate="isIndeterminate(node.id)"
                                                           @change="toggleNode(node.id)"
                                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0">
                                                    <span class="text-sm truncate"
                                                          :class="node.depth === 0 ? 'font-semibold text-gray-900' : (node.depth === 1 ? 'font-medium text-gray-800' : 'text-gray-600')"
                                                          x-text="node.name"></span>
                                                </label>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <template x-for="id in serialize()" :key="id">
                                    <input type="hidden" name="categories[]" :value="id">
                                </template>
                                <input type="hidden" name="category_id" :value="serialize()[0] || ''">
                                @error('categories') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div x-data="collectionPicker({
                                allCollections: @js($collections->map(fn($c) => ['id' => $c->id, 'name' => $c->name])),
                                selectedCollectionIds: @js($selectedCollectionIds),
                            })" class="relative"
                                 @click.outside="showModal = false">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Collections</label>

                                <div class="w-full border border-gray-300 rounded-lg px-2 py-1.5 flex flex-wrap gap-1 items-center cursor-pointer"
                                     @click="showModal = !showModal">
                                    <template x-for="c in selected" :key="c.id">
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-purple-50 border border-purple-200 text-sm text-purple-700 leading-tight">
                                            <span x-text="c.name"></span>
                                            <button type="button" @click.stop="remove(c.id)" class="text-purple-400 hover:text-purple-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template x-if="selected.length === 0">
                                        <span class="inline-flex items-center gap-1 text-gray-400 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add collections
                                        </span>
                                    </template>
                                </div>

                                <template x-for="c in selected" :key="c.id">
                                    <input type="hidden" name="collections[]" :value="c.id">
                                </template>

                                <div x-show="showModal" x-cloak
                                     class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg flex flex-col overflow-hidden"
                                     style="max-height: 320px;">
                                    <div class="p-3 border-b border-gray-200 shrink-0">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                            <input type="text" x-model="query"
                                                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   placeholder="Search collections...">
                                        </div>
                                    </div>

                                    <div class="overflow-y-auto p-3">
                                        <template x-if="filtered.length > 0">
                                            <div class="space-y-0.5">
                                                <template x-for="c in filtered" :key="c.id">
                                                    <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                        <input type="checkbox"
                                                               :checked="isSelected(c.id)"
                                                               @change="toggle(c)"
                                                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                        <span class="text-sm text-gray-700" x-text="c.name"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="filtered.length === 0 && query.length > 0">
                                            <div class="text-center py-6">
                                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                                <p class="text-sm text-gray-500">No collections found.</p>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="flex items-center justify-between px-3 py-2 border-t border-gray-200 shrink-0">
                                        <span class="text-xs text-gray-400" x-text="selected.length + ' collection' + (selected.length !== 1 ? 's' : '') + ' selected'"></span>
                                        <button type="button" @click="showModal = false"
                                                class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                            Done
                                        </button>
                                    </div>
                                </div>

                                @error('collections') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div x-data="brandTree({{ $brands->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'parent_id' => $b->parent_id])->values() }}, @js($selectedBrandIds))">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                                <div class="relative" @click.outside="open = false">
                                    <div class="flex flex-wrap gap-1.5 items-center border border-gray-300 bg-white rounded-lg px-3 py-2 cursor-pointer"
                                         @click="open = !open">
                                        <template x-for="chip in chips" :key="chip.id">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-medium rounded-md">
                                                <span x-text="chip.name"></span>
                                                <button type="button" @click.stop="removeChip(chip.id)" class="text-indigo-400 hover:text-red-500 transition-colors" title="Remove">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </span>
                                        </template>
                                        <span class="text-sm text-gray-400" x-show="chips.length === 0">Select brands...</span>
                                        <input type="text" x-model="query"
                                               placeholder="Search brands..."
                                               class="flex-1 min-w-[120px] bg-transparent text-sm outline-none border-0 p-0 focus:ring-0"
                                               @focus="open = true" @click.stop>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-150 shrink-0" :class="open ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>

                                    <div x-show="open" x-cloak @click.stop
                                         class="absolute z-20 mt-1 left-0 right-0 border border-gray-200 bg-white rounded-lg shadow-lg p-2 max-h-96 overflow-y-auto">
                                        <template x-if="filtered.length === 0">
                                            <p class="text-xs text-gray-400 px-1 py-2 text-center">No brands found.</p>
                                        </template>
                                        <template x-for="node in filtered" :key="node.id">
                                            <div class="flex items-center gap-1 py-0.5"
                                                 :style="'padding-left:' + (node.depth * 14) + 'px'">
                                                <span class="w-4 shrink-0"></span>
                                                <label class="flex items-center gap-2 cursor-pointer select-none min-w-0">
                                                    <input type="checkbox" :checked="isChecked(node.id)"
                                                           :indeterminate="isIndeterminate(node.id)"
                                                           @change="toggleNode(node.id)"
                                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0">
                                                    <span class="text-sm truncate"
                                                          :class="node.depth === 0 ? 'font-semibold text-gray-900' : (node.depth === 1 ? 'font-medium text-gray-800' : 'text-gray-600')"
                                                          x-text="node.name"></span>
                                                </label>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <template x-for="id in serialize()" :key="id">
                                    <input type="hidden" name="brands[]" :value="id">
                                </template>
                                <input type="hidden" name="brand_id" :value="serialize()[0] || ''">
                                @error('brands') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            @if ($attributes->isNotEmpty())
                                <div class="pt-2">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Attributes</p>
                                    <div class="space-y-2">
                                        @foreach ($attributes as $attribute)
                                            <div x-data="{ 
                                                open: false, 
                                                selected: @js(old('attribute_options', $selectedAttributeOptions)),
                                                options: @js($attribute->options->mapWithKeys(fn($o) => [$o->id => $o->display_name])),
                                                get selectedOptions() {
                                                    return this.selected.filter(id => this.options[id]);
                                                }
                                            }" class="relative">
                                                <button type="button" @click="open = !open"
                                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm hover:bg-gray-50 transition-colors text-left">
                                                    <span class="font-medium text-gray-700">{{ $attribute->name }}<template x-if="selectedOptions.length > 0"><span class="font-normal text-gray-500">:<span class="text-gray-700"> </span></span></template><span class="font-normal text-gray-700" x-text="selectedOptions.map(id => options[id]).join(', ')"></span></span>
                                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.outside="open = false" x-cloak
                                                     class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 max-h-48 overflow-y-auto">
                                                    <div class="space-y-1">
                                                        @foreach ($attribute->options as $option)
                                                            <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 cursor-pointer text-sm">
                                                                <input type="checkbox" name="attribute_options[]" value="{{ $option->id }}"
                                                                       @checked(in_array($option->id, old('attribute_options', $selectedAttributeOptions)))
                                                                       @change="selected = Array.from($el.closest('[x-data]').querySelectorAll('input[name=&quot;attribute_options[]&quot;]:checked')).map(i => i.value)"
                                                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                                <span class="text-gray-700">{{ $option->display_name }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div x-data="tagPicker({
                                allTags: @js($tags->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])),
                                selectedTagIds: @js(old('tags', $product->tags->pluck('id')->toArray())),
                                frequentlyUsedTags: @js($frequentlyUsedTags->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])),
                            })" class="relative"
                                 @click.outside="showModal = false">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>

                                <div class="w-full border border-gray-300 rounded-lg px-2 py-1.5 flex flex-wrap gap-1 items-center cursor-pointer"
                                     @click="showModal = !showModal">
                                    <template x-for="tag in selectedTags" :key="tag.id">
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-purple-50 border border-purple-200 text-sm text-purple-700 leading-tight">
                                            <span x-text="tag.name"></span>
                                            <button type="button" @click.stop="removeTag(tag.id)" class="text-purple-400 hover:text-purple-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template x-if="selectedTags.length === 0">
                                        <span class="inline-flex items-center gap-1 text-gray-400 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add tags
                                        </span>
                                    </template>
                                </div>

                                <template x-for="tag in selectedTags" :key="tag.id">
                                    <input type="hidden" name="tags[]" :value="tag.id">
                                </template>

                                <div x-show="showModal" x-cloak
                                     class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg flex flex-col overflow-hidden"
                                     style="max-height: 320px;">
                                    <div class="p-3 border-b border-gray-200 shrink-0">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                            <input type="text" x-model="searchQuery"
                                                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   placeholder="Search tags...">
                                        </div>
                                        <template x-if="canCreate">
                                            <button type="button" @click="createAndAdd()"
                                                    class="mt-2 w-full flex items-center gap-2 px-3 py-1.5 rounded-lg border border-dashed border-indigo-300 text-sm text-indigo-600 hover:bg-indigo-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Create "<span x-text="searchQuery"></span>"
                                            </button>
                                        </template>
                                    </div>

                                    <div class="overflow-y-auto p-3">
                                        <template x-if="filteredFrequentTags.length > 0">
                                            <div class="mb-3">
                                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Frequently used</p>
                                                <div class="space-y-0.5">
                                                    <template x-for="tag in filteredFrequentTags" :key="tag.id">
                                                        <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                            <input type="checkbox"
                                                                   :checked="isSelected(tag.id)"
                                                                   @change="toggleTag(tag)"
                                                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                            <span class="text-sm text-gray-700" x-text="tag.name"></span>
                                                        </label>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="filteredAllTags.length > 0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">All tags</p>
                                                <div class="space-y-0.5">
                                                    <template x-for="tag in filteredAllTags" :key="tag.id">
                                                        <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                            <input type="checkbox"
                                                                   :checked="isSelected(tag.id)"
                                                                   @change="toggleTag(tag)"
                                                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                            <span class="text-sm text-gray-700" x-text="tag.name"></span>
                                                        </label>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="filteredFrequentTags.length === 0 && filteredAllTags.length === 0 && !searchQuery">
                                            <div class="text-center py-6">
                                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                                <p class="text-sm text-gray-500">No tags yet. Start typing to create one.</p>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="flex items-center justify-between px-3 py-2 border-t border-gray-200 shrink-0">
                                        <span class="text-xs text-gray-400" x-text="selectedTags.length + ' tag' + (selectedTags.length !== 1 ? 's' : '') + ' selected'"></span>
                                        <button type="button" @click="showModal = false"
                                                class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                            Done
                                        </button>
                                    </div>
                                </div>

                                @error('tags') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Custom Fields --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"
                         x-data="customFieldsManager(@js($product->custom_fields ?? []))">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Custom Fields</h3>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="custom_fields_enabled" value="1"
                                       x-model="enabled" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        <template x-if="enabled">
                            <div>
                                <p class="text-xs text-gray-500 mb-3">Add custom input fields for customers (e.g. engraving text, gift message, file upload).</p>
                                <div class="space-y-3">
                                    <template x-for="(field, index) in fields" :key="index">
                                        <div class="border border-gray-200 rounded-lg p-3 space-y-3">
                                            <div class="flex items-center gap-2">
                                                <input type="text" x-model="field.label"
                                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                       placeholder="Field label (e.g. Engraving Text)">
                                                <select x-model="field.type"
                                                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="text">Text</option>
                                                    <option value="file">File upload</option>
                                                </select>
                                                <button type="button" @click="fields.splice(index, 1)"
                                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <template x-if="field.type === 'file'">
                                                <div>
                                                    <div x-show="!field.file"
                                                         @click="$refs['fileInput_' + index].click()"
                                                         class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-colors">
                                                        <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                        </svg>
                                                        <span class="text-xs text-gray-500">Click to upload file</span>
                                                    </div>
                                                    <div x-show="field.file" class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-sm text-gray-700 truncate flex-1" x-text="field.fileName || field.file.split('/').pop()"></span>
                                                        <button type="button" @click="field.file = ''; field.fileName = ''"
                                                                class="text-gray-400 hover:text-red-500 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <input type="file" x-ref="'fileInput_' + index"
                                                           @change="uploadFieldFile(index, $event)"
                                                           class="hidden">
                                                </div>
                                            </template>
                                            <template x-if="field.type === 'text'">
                                                <input type="text" x-model="field.file"
                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                       placeholder="Optional default value or file path">
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" @click="fields.push({ label: '', type: 'text', file: '', fileName: '' })"
                                        class="mt-3 inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add field
                                </button>
                            </div>
                        </template>
                        <input type="hidden" name="custom_fields" :value="enabled ? JSON.stringify(fields.filter(f => f.label)) : ''">
                    </div>

                    {{-- Tiered Pricing --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"
                         x-data="tieredPricingManager(@js($product->tierPrices->map(fn($t) => [
                             'id' => $t->id,
                             'minimum_quantity' => $t->minimum_quantity,
                             'maximum_quantity' => $t->maximum_quantity,
                             'discount_type' => $t->discount_type,
                             'discount_value' => (float) $t->discount_value,
                             'status' => $t->status,
                         ])->values()->toArray()))">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Tiered Pricing</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Offer automatic discounts when customers buy more quantities.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="tiered_pricing_enabled" value="1"
                                       x-model="enabled" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        <template x-if="enabled">
                            <div>
                                <div class="overflow-x-auto -mx-6 px-6">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-2.5 font-medium text-gray-500 w-8"></th>
                                                <th class="text-left py-2.5 font-medium text-gray-500 w-25">Min Qty</th>
                                                <th class="text-left py-2.5 font-medium text-gray-500 w-25">Max Qty</th>
                                                <th class="text-center py-2.5 font-medium text-gray-500 w-44">Type</th>
                                                <th class="text-left py-2.5 font-medium text-gray-500 w-30">Value</th>
                                                <th class="text-left py-2.5 font-medium text-gray-500">Final Price</th>
                                                <th class="text-left py-2.5 font-medium text-gray-500">Status</th>
                                                <th class="text-left py-2.5 font-medium text-gray-500 w-8"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(tier, index) in tiers" :key="index">
                                                <tr class="border-b border-gray-100 group"
                                                    draggable="true"
                                                    @dragstart="dragStart($event, index)"
                                                    @dragover.prevent="dragOver($event, index)"
                                                    @drop.prevent="dragDrop($event, index)"
                                                    @dragend="dragEnd()">
                                                    <td class="py-2 pr-1">
                                                        <span class="cursor-grab text-gray-400 hover:text-gray-600 active:cursor-grabbing">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="5" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg>
                                                        </span>
                                                    </td>
                                                    <td class="py-2 pr-1 w-24">
                                                        <input type="number" x-model.number="tier.minimum_quantity" min="1"
                                                               class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                               placeholder="e.g. 4">
                                                    </td>
                                                    <td class="py-2 pr-1 w-24">
                                                        <input type="number" x-model.number="tier.maximum_quantity" min="1"
                                                               class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                               placeholder="∞ Unlimited">
                                                    </td>
                                                    <td class="py-2 pr-1 text-center">
                                                        <select x-model="tier.discount_type"
                                                                class="w-full text-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <option value="percentage">Percentage</option>
                                                            <option value="fixed_amount">Fixed Amount</option>
                                                            <option value="fixed_price">Fixed Price</option>
                                                        </select>
                                                    </td>
                                                    <td class="py-2 pr-1">
                                                        <div class="relative" x-show="tier.discount_type !== 'fixed_price'">
                                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" x-text="tier.discount_type === 'percentage' ? '%' : '$'"></span>
                                                            <input type="number" x-model.number="tier.discount_value" min="0"
                                                                   :max="tier.discount_type === 'percentage' ? 100 : ''"
                                                                   class="w-full rounded-lg border border-gray-300 pl-7 pr-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                   placeholder="0">
                                                        </div>
                                                        <div x-show="tier.discount_type === 'fixed_price'">
                                                            <input type="number" x-model.number="tier.discount_value" min="0" step="0.01"
                                                                   class="w-full rounded-lg border border-gray-300 pl-7 pr-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                   placeholder="0.00">
                                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 pr-1">
                                                        <span class="text-sm font-semibold text-gray-900" x-text="'$' + calculateFinalPrice(tier).toFixed(2)"></span>
                                                    </td>
                                                    <td class="py-2 pr-1">
                                                        <label class="relative inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" x-model="tier.status" class="sr-only peer">
                                                            <div class="w-8 h-4 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-indigo-600"></div>
                                                        </label>
                                                    </td>
                                                    <td class="py-2">
                                                        <button type="button" @click="removeTier(index)"
                                                                class="p-1 pl-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="flex items-center gap-3 mt-3 px-6">
                                    <button type="button" @click="addTier()"
                                            class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Tier
                                    </button>
                                    <button type="button" @click="duplicateLastTier()"
                                            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Duplicate Last
                                    </button>
                                </div>
                            </div>
                        </template>
                        <input type="hidden" name="tier_prices" :value="enabled ? JSON.stringify(tiers.filter(t => t.minimum_quantity)) : ''">
                    </div>

                    {{-- Search Engine Listing --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"
                         x-data="searchEngineListing({
                             name: '{{ addslashes(old('name', $product->name)) }}',
                             description: '{!! addslashes(strip_tags(old('long_description', $product->long_description ?? ''))) !!}',
                             seoTitle: '{{ addslashes(old('seo_title', $product->seo_title ?? '')) }}',
                             seoDescription: '{{ addslashes(old('seo_description', $product->seo_description ?? '')) }}',
                             seoSlug: '{{ addslashes(old('seo_slug', $product->seo_slug ?? $product->slug)) }}',
                             storeName: '{{ config('app.name', 'Store') }}',
                             baseUrl: '{{ url('/products') }}',
                             productId: {{ $product->id }},
                         })">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Search engine listing</h3>
                            <button type="button" @click="expanded = !expanded"
                                    class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                                    :title="expanded ? 'Collapse' : 'Edit'"
                                    aria-label="Toggle search engine listing editor">
                                <svg x-show="!expanded" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                                <svg x-show="expanded" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Collapsed: Google Preview --}}
                        <div @click="expanded = true" class="cursor-pointer group">
                            <div class="rounded-lg border border-gray-200 p-4 transition-colors group-hover:border-gray-300 group-hover:bg-gray-50">
                                <p class="text-[12px] text-gray-500 leading-snug" x-text="storeName"></p>
                                <p class="text-[13px] text-green-700 leading-snug mt-0.5 truncate"
                                   x-text="baseUrl + '/' + (seoSlug || '...')"></p>
                                <p class="text-[18px] sm:text-[20px] text-blue-700 leading-snug mt-0.5 hover:underline line-clamp-1 font-normal"
                                   x-text="seoTitle || name || 'Product Title'"></p>
                                <p class="text-[13px] text-gray-600 leading-snug mt-0.5 line-clamp-2"
                                   x-text="seoDescription || description.substring(0, 160) || 'Product description will appear here...'"></p>
                            </div>
                        </div>

                        {{-- Expanded: Fields --}}
                        <div x-show="expanded" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="mt-4 space-y-4">

                            {{-- Page Title --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="seo_title" class="block text-sm font-medium text-gray-700">Page title</label>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                          :class="seoTitle.length === 0 ? 'bg-gray-100 text-gray-500' : (seoTitle.length < 30 ? 'bg-orange-100 text-orange-700' : (seoTitle.length <= 60 ? 'bg-green-100 text-green-700' : (seoTitle.length <= 70 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700')))"
                                          x-text="seoTitle.length === 0 ? 'Empty' : (seoTitle.length < 30 ? 'Too short' : (seoTitle.length <= 70 ? 'Good' : 'Too long'))"></span>
                                </div>
                                <input type="text" name="seo_title" id="seo_title" x-model="seoTitle"
                                       maxlength="70" @input="userEditedTitle = true"
                                       class="w-full rounded-lg border px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                       :class="seoTitle.length > 70 ? 'border-red-300' : 'border-gray-300'"
                                       placeholder="Page title">
                                <p class="text-xs text-gray-400 mt-1">Recommended 50–60 characters. <span x-text="seoTitle.length"></span>/70</p>
                                @error('seo_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Meta Description --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="seo_description" class="block text-sm font-medium text-gray-700">Meta description</label>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                          :class="seoDescription.length === 0 ? 'bg-gray-100 text-gray-500' : (seoDescription.length < 70 ? 'bg-orange-100 text-orange-700' : (seoDescription.length <= 160 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'))"
                                          x-text="seoDescription.length === 0 ? 'Empty' : (seoDescription.length < 70 ? 'Too short' : (seoDescription.length <= 160 ? 'Good' : 'Too long'))"></span>
                                </div>
                                <textarea name="seo_description" id="seo_description" x-model="seoDescription"
                                          maxlength="160" rows="3" @input="userEditedDesc = true"
                                          class="w-full rounded-lg border px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                          :class="seoDescription.length > 160 ? 'border-red-300' : 'border-gray-300'"
                                          placeholder="Meta description"></textarea>
                                <p class="text-xs text-gray-400 mt-1">Recommended 140–160 characters. <span x-text="seoDescription.length"></span>/160</p>
                                @error('seo_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- URL Handle --}}
                            <div>
                                <label for="seo_slug" class="block text-sm font-medium text-gray-700 mb-1">URL handle</label>
                                <div class="flex items-center rounded-lg border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-colors">
                                    <span class="bg-gray-50 border-r border-gray-300 px-3 py-2.5 text-sm text-gray-400 whitespace-nowrap select-none"
                                          x-text="baseUrl + '/'"></span>
                                    <input type="text" name="seo_slug" id="seo_slug" x-model="seoSlug"
                                           @input="userEditedSlug = true; validateSlug()"
                                           class="flex-1 px-3 py-2.5 text-sm focus:outline-none border-0 bg-transparent"
                                           placeholder="auto-generated-from-name"
                                           pattern="[a-z0-9\-]*">
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-xs text-gray-400">Only lowercase letters, numbers and hyphens.</p>
                                    <span x-show="slugChecking" x-cloak class="text-xs text-gray-400">Checking...</span>
                                    <span x-show="!slugChecking && slugAvailable === true" x-cloak class="text-xs text-green-600 font-medium">Available</span>
                                    <span x-show="!slugChecking && slugAvailable === false" x-cloak class="text-xs text-red-600 font-medium">Not available</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <span x-text="baseUrl + '/' + (seoSlug || '...')"></span>
                                </p>
                                @error('seo_slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Live Google Preview --}}
                            <div class="rounded-lg border border-gray-200 bg-white p-4 mt-2">
                                <p class="text-[12px] text-gray-500 leading-snug" x-text="storeName"></p>
                                <p class="text-[13px] text-green-700 leading-snug mt-0.5 truncate"
                                   x-text="baseUrl + '/' + (seoSlug || '...')"></p>
                                <p class="text-[18px] sm:text-[20px] text-blue-700 leading-snug mt-0.5 hover:underline line-clamp-1 font-normal"
                                   x-text="seoTitle || name || 'Product Title'"></p>
                                <p class="text-[13px] text-gray-600 leading-snug mt-0.5 line-clamp-2"
                                   x-text="seoDescription || description.substring(0, 160) || 'Product description will appear here...'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Metadata --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Timeline</h3>
                        <div class="text-sm divide-y divide-gray-100">
                            <div class="flex justify-between py-2">
                                <span class="text-gray-400">Created</span>
                                <span class="text-gray-700 font-medium">{{ $product->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-400">Updated</span>
                                <span class="text-gray-700 font-medium">{{ $product->updated_at->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-400">Total Order</span>
                                <span class="flex items-center gap-2">
                                    <span class="text-gray-700 font-medium">{{ $timeline['total_orders'] }} <span class="text-xs text-gray-400">({{ $timeline['total_quantity'] }} qty)</span></span>
                                    <a href="{{ route('admin.orders.index', ['product_id' => $product->id]) }}"
                                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                        View
                                    </a>
                                </span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-400">Total Cancel Order</span>
                                <span class="text-gray-700 font-medium">{{ $timeline['cancelled_orders'] }} <span class="text-xs text-gray-400">({{ $timeline['cancelled_quantity'] }} qty)</span></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-400">Total Return</span>
                                <span class="text-gray-700 font-medium">{{ $timeline['returned_orders'] }} <span class="text-xs text-gray-400">({{ $timeline['returned_quantity'] }} qty)</span></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-400">Initial Stock</span>
                                <span class="text-gray-700 font-medium">{{ $timeline['initial_stock'] }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-400">Current Stock</span>
                                <span class="text-gray-700 font-medium">{{ $timeline['current_stock'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function categoryTree(categories, selectedIds = []) {
                return {
                    categories: categories,
                    selectedIds: (selectedIds || []).map(Number),
                    query: '',
                    open: false,
                    nodeMap: {},
                    treeRoots: [],
                    checked: {},
                    init() {
                        this.buildTree();
                        this.selectedIds.forEach(id => {
                            if (this.nodeMap[id]) this.setCheck(id, true);
                        });
                    },
                    buildTree() {
                        this.nodeMap = {};
                        (this.categories || []).forEach(c => {
                            this.nodeMap[c.id] = {
                                id: c.id,
                                name: c.name,
                                parent_id: c.parent_id,
                                depth: 0,
                                children: [],
                            };
                        });
                        this.treeRoots = [];
                        (this.categories || []).forEach(c => {
                            const node = this.nodeMap[c.id];
                            const parent = c.parent_id ? this.nodeMap[c.parent_id] : null;
                            if (parent) {
                                parent.children.push(node);
                                node.depth = parent.depth + 1;
                            } else {
                                this.treeRoots.push(node);
                            }
                        });
                    },
                    descendantsOf(id) {
                        const stack = [...(this.nodeMap[id]?.children || [])];
                        const out = [];
                        while (stack.length) {
                            const n = stack.pop();
                            out.push(n.id);
                            stack.push(...n.children);
                        }
                        return out;
                    },
                    setCheck(id, val) {
                        this.checked[id] = val;
                        this.descendantsOf(id).forEach(k => { this.checked[k] = val; });
                    },
                    isChecked(id) {
                        return !!this.checked[id];
                    },
                    isIndeterminate(id) {
                        const node = this.nodeMap[id];
                        if (!node || !node.children.length) return false;
                        const kids = node.children.map(c => c.id);
                        const any = kids.some(k => this.isChecked(k));
                        const all = kids.every(k => this.isChecked(k));
                        return any && !all;
                    },
                    toggleNode(id) {
                        this.setCheck(id, !this.isChecked(id));
                    },
                    get filtered() {
                        const q = (this.query || '').trim().toLowerCase();
                        const out = [];
                        const walk = node => {
                            const selfMatch = !q || node.name.toLowerCase().includes(q);
                            const childMatch = node.children.some(c => this.branchMatches(c, q));
                            if (!selfMatch && !childMatch) return;
                            out.push(node);
                            node.children.forEach(walk);
                        };
                        this.treeRoots.forEach(walk);
                        return out;
                    },
                    branchMatches(node, q) {
                        if (!q) return true;
                        if (node.name.toLowerCase().includes(q)) return true;
                        return node.children.some(c => this.branchMatches(c, q));
                    },
                    topChecked() {
                        const out = [];
                        const walk = node => {
                            if (this.isChecked(node.id)) {
                                out.push(node.id);
                            } else {
                                node.children.forEach(walk);
                            }
                        };
                        this.treeRoots.forEach(walk);
                        return out;
                    },
                    get chips() {
                        const names = {};
                        (this.categories || []).forEach(c => { names[c.id] = c.name; });
                        return this.topChecked().map(id => ({ id: id, name: names[id] }));
                    },
                    removeChip(id) {
                        this.setCheck(id, false);
                    },
                    serialize() {
                        return this.topChecked();
                    },
                };
            }
        </script>
        <script>
            function brandTree(brands, selectedIds = []) {
                return {
                    brands: brands,
                    selectedIds: (selectedIds || []).map(Number),
                    query: '',
                    open: false,
                    nodeMap: {},
                    treeRoots: [],
                    checked: {},
                    init() {
                        this.buildTree();
                        this.selectedIds.forEach(id => {
                            if (this.nodeMap[id]) this.setCheck(id, true);
                        });
                    },
                    buildTree() {
                        this.nodeMap = {};
                        (this.brands || []).forEach(c => {
                            this.nodeMap[c.id] = {
                                id: c.id,
                                name: c.name,
                                parent_id: c.parent_id,
                                depth: 0,
                                children: [],
                            };
                        });
                        this.treeRoots = [];
                        (this.brands || []).forEach(c => {
                            const node = this.nodeMap[c.id];
                            const parent = c.parent_id ? this.nodeMap[c.parent_id] : null;
                            if (parent) {
                                parent.children.push(node);
                                node.depth = parent.depth + 1;
                            } else {
                                this.treeRoots.push(node);
                            }
                        });
                    },
                    descendantsOf(id) {
                        const stack = [...(this.nodeMap[id]?.children || [])];
                        const out = [];
                        while (stack.length) {
                            const n = stack.pop();
                            out.push(n.id);
                            stack.push(...n.children);
                        }
                        return out;
                    },
                    setCheck(id, val) {
                        this.checked[id] = val;
                        this.descendantsOf(id).forEach(k => { this.checked[k] = val; });
                    },
                    isChecked(id) {
                        return !!this.checked[id];
                    },
                    isIndeterminate(id) {
                        const node = this.nodeMap[id];
                        if (!node || !node.children.length) return false;
                        const kids = node.children.map(c => c.id);
                        const any = kids.some(k => this.isChecked(k));
                        const all = kids.every(k => this.isChecked(k));
                        return any && !all;
                    },
                    toggleNode(id) {
                        this.setCheck(id, !this.isChecked(id));
                    },
                    get filtered() {
                        const q = (this.query || '').trim().toLowerCase();
                        const out = [];
                        const walk = node => {
                            const selfMatch = !q || node.name.toLowerCase().includes(q);
                            const childMatch = node.children.some(c => this.branchMatches(c, q));
                            if (!selfMatch && !childMatch) return;
                            out.push(node);
                            node.children.forEach(walk);
                        };
                        this.treeRoots.forEach(walk);
                        return out;
                    },
                    branchMatches(node, q) {
                        if (!q) return true;
                        if (node.name.toLowerCase().includes(q)) return true;
                        return node.children.some(c => this.branchMatches(c, q));
                    },
                    topChecked() {
                        const out = [];
                        const walk = node => {
                            if (this.isChecked(node.id)) {
                                out.push(node.id);
                            } else {
                                node.children.forEach(walk);
                            }
                        };
                        this.treeRoots.forEach(walk);
                        return out;
                    },
                    get chips() {
                        const names = {};
                        (this.brands || []).forEach(c => { names[c.id] = c.name; });
                        return this.topChecked().map(id => ({ id: id, name: names[id] }));
                    },
                    removeChip(id) {
                        this.setCheck(id, false);
                    },
                    serialize() {
                        return this.topChecked();
                    },
                };
            }
        </script>
        <script>
            function collectionPicker(data) {
                return {
                    allCollections: data.allCollections,
                    selected: [],
                    showModal: false,
                    query: '',
                    init() {
                        if (data.selectedCollectionIds.length) {
                            this.selected = this.allCollections.filter(c => data.selectedCollectionIds.includes(c.id));
                        }
                    },
                    isSelected(id) {
                        return this.selected.some(s => s.id === id);
                    },
                    get filtered() {
                        if (!this.query) return this.allCollections;
                        const q = this.query.toLowerCase();
                        return this.allCollections.filter(c => c.name.toLowerCase().includes(q));
                    },
                    toggle(c) {
                        if (this.isSelected(c.id)) {
                            this.selected = this.selected.filter(s => s.id !== c.id);
                        } else {
                            this.selected.push(c);
                        }
                    },
                    remove(id) {
                        this.selected = this.selected.filter(s => s.id !== id);
                    },
                };
            }
        </script>
        <script>
            function tieredPricingManager(existing) {
                const existingEnabled = Array.isArray(existing) && existing.length > 0;
                return {
                    enabled: existingEnabled,
                    tiers: existingEnabled ? existing : [],
                    basePrice: {{ old('price', $product->sale_price ?? $product->price) }},
                    dragIndex: null,
                    init() {
                        const priceInput = document.getElementById('price');
                        if (priceInput) {
                            this.basePrice = parseFloat(priceInput.value) || 0;
                            priceInput.addEventListener('input', () => {
                                this.basePrice = parseFloat(priceInput.value) || 0;
                            });
                        }
                    },
                    addTier() {
                        const last = this.tiers[this.tiers.length - 1];
                        this.tiers.push({
                            minimum_quantity: last ? (last.maximum_quantity ? parseInt(last.maximum_quantity) + 1 : parseInt(last.minimum_quantity) + 1) : 1,
                            maximum_quantity: null,
                            discount_type: 'percentage',
                            discount_value: 10,
                            status: true,
                        });
                    },
                    removeTier(index) {
                        this.tiers.splice(index, 1);
                    },
                    duplicateLastTier() {
                        const last = this.tiers[this.tiers.length - 1];
                        if (last) {
                            this.tiers.push({ ...last, id: undefined, minimum_quantity: last.maximum_quantity ? parseInt(last.maximum_quantity) + 1 : parseInt(last.minimum_quantity) + 1 });
                        }
                    },
                    calculateFinalPrice(tier) {
                        const price = this.basePrice || 0;
                        if (!price) return 0;
                        switch (tier.discount_type) {
                            case 'percentage': return Math.max(0, price - (price * (tier.discount_value / 100)));
                            case 'fixed_amount': return Math.max(0, price - (tier.discount_value || 0));
                            case 'fixed_price': return tier.discount_value || 0;
                            default: return price;
                        }
                    },
                    dragStart(event, index) {
                        this.dragIndex = index;
                        event.dataTransfer.effectAllowed = 'move';
                    },
                    dragOver(event, index) {
                        event.dataTransfer.dropEffect = 'move';
                    },
                    dragDrop(event, index) {
                        if (this.dragIndex === null || this.dragIndex === index) return;
                        const item = this.tiers.splice(this.dragIndex, 1)[0];
                        this.tiers.splice(index, 0, item);
                        this.dragIndex = null;
                    },
                    dragEnd() {
                        this.dragIndex = null;
                    },
                };
            }
            function customFieldsManager(existing) {
                const existingEnabled = Array.isArray(existing) && existing.length > 0;
                return {
                    enabled: existingEnabled,
                    fields: existingEnabled ? existing.map(f => ({ label: f.label || '', type: f.type || 'text', file: f.file || '', fileName: '' })) : [{ label: '', type: 'text', file: '', fileName: '' }],
                    uploadFieldFile(index, event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        const formData = new FormData();
                        formData.append('file', file);
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        fetch('{{ route("admin.products.upload-custom-field-file") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: formData,
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.path) {
                                this.fields[index].file = data.path;
                                this.fields[index].fileName = file.name;
                            }
                        });
                        event.target.value = '';
                    },
                };
            }
            function searchEngineListing(data) {
                return {
                    expanded: false,
                    name: data.name || '',
                    description: data.description || '',
                    seoTitle: data.seoTitle || data.name || '',
                    seoDescription: data.seoDescription || '',
                    seoSlug: data.seoSlug || '',
                    storeName: data.storeName || 'Store',
                    baseUrl: data.baseUrl || '',
                    productId: data.productId || null,
                    userEditedTitle: false,
                    userEditedDesc: false,
                    userEditedSlug: !!data.seoSlug,
                    slugChecking: false,
                    slugAvailable: null,
                    slugTimeout: null,
                    stripHtml(html) {
                        const tmp = document.createElement('div');
                        tmp.innerHTML = html || '';
                        return tmp.textContent || tmp.innerText || '';
                    },
                    slugify(text) {
                        return text
                            .toLowerCase()
                            .replace(/[^\w\s-]/g, '')
                            .replace(/[\s_]+/g, '-')
                            .replace(/-+/g, '-')
                            .replace(/^-+|-+$/g, '');
                    },
                    validateSlug() {
                        clearTimeout(this.slugTimeout);
                        this.slugAvailable = null;
                        if (!this.seoSlug) {
                            return;
                        }
                        this.slugChecking = true;
                        this.slugTimeout = setTimeout(() => {
                            fetch('{{ route("admin.products.slug-check") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({
                                    slug: this.seoSlug,
                                    product_id: this.productId,
                                }),
                            })
                            .then(r => r.json())
                            .then(data => {
                                this.slugAvailable = data.available;
                                this.slugChecking = false;
                            })
                            .catch(() => {
                                this.slugChecking = false;
                            });
                        }, 400);
                    },
                    init() {
                        const nameInput = document.getElementById('name');
                        const descInput = document.getElementById('long_description_input');

                        if (!this.userEditedTitle && this.name) {
                            this.seoTitle = this.name;
                        }
                        if (!this.userEditedDesc && this.description) {
                            const stripped = this.stripHtml(this.description);
                            this.seoDescription = stripped.substring(0, 160);
                        }
                        if (!this.userEditedSlug && this.name) {
                            this.seoSlug = this.slugify(this.name);
                        }

                        if (nameInput) {
                            nameInput.addEventListener('input', () => {
                                this.name = nameInput.value;
                                if (!this.userEditedTitle) {
                                    this.seoTitle = nameInput.value;
                                }
                                if (!this.userEditedSlug) {
                                    this.seoSlug = this.slugify(nameInput.value);
                                }
                            });
                        }
                        if (descInput) {
                            descInput.addEventListener('input', () => {
                                this.description = descInput.value;
                                if (!this.userEditedDesc) {
                                    const stripped = this.stripHtml(descInput.value);
                                    this.seoDescription = stripped.substring(0, 160);
                                }
                            });
                        }
                    },
                };
            }
        </script>
    @endpush
</x-admin-layout>
