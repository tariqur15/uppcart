<x-admin-layout>
<div x-data="collectionForm()" class="max-w-4xl mx-auto">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.collections.index') }}" class="hover:text-indigo-600 transition-colors">Collections</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">Create collection</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Create collection</h1>

    <form action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data" @submit="submitForm">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Basic information</h2>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="name" x-model="form.name" @input="generateSlug" required
                           placeholder="e.g. Summer Collection"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">URL handle</label>
                    <div class="text-xs text-gray-400 mb-1">{{ url('/') }}/collections/<span x-text="form.slug" class="text-indigo-600"></span></div>
                    <input type="text" name="slug" id="slug" x-model="form.slug"
                           placeholder="summer-collection"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              placeholder="Describe your collection..."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Collection image</label>
                    <div class="flex items-start gap-5">
                        <div class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0" id="imagePreview">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label for="image" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 cursor-pointer transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Upload image
                            </label>
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="hidden" @change="previewImage">
                            <p class="mt-1.5 text-xs text-gray-400">Recommended size: 1200 x 900px. PNG or JPG.</p>
                            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" x-model="form.status"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Collection Type --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Collection type</h2>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="relative block cursor-pointer rounded-lg border-2 p-4 transition-colors"
                           :class="form.collection_type === 'manual' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="collection_type" value="manual" x-model="form.collection_type" class="sr-only">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5"
                                 :class="form.collection_type === 'manual' ? 'border-indigo-500' : 'border-gray-300'">
                                <div x-show="form.collection_type === 'manual'" class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Manual</p>
                                <p class="text-xs text-gray-500 mt-0.5">Select products individually to include in this collection.</p>
                            </div>
                        </div>
                    </label>

                    <label class="relative block cursor-pointer rounded-lg border-2 p-4 transition-colors"
                           :class="form.collection_type === 'automatic' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="collection_type" value="automatic" x-model="form.collection_type" class="sr-only">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5"
                                 :class="form.collection_type === 'automatic' ? 'border-indigo-500' : 'border-gray-300'">
                                <div x-show="form.collection_type === 'automatic'" class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Automatic</p>
                                <p class="text-xs text-gray-500 mt-0.5">Products are dynamically added based on conditions you set.</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Manual: Product Picker --}}
        <div x-show="form.collection_type === 'manual'" class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Products</h2>
                <p class="text-xs text-gray-500 mt-0.5">Search and select products to include in this collection.</p>
            </div>
            <div class="px-6 py-5">
                {{-- Search --}}
                <div class="flex gap-3 mb-4">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="searchQuery" @input.debounce="searchProducts" placeholder="Search by title, SKU, or brand..."
                               class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Search Results --}}
                <div x-show="searchResults.length > 0" class="mb-4 border border-gray-200 rounded-lg max-h-60 overflow-y-auto">
                    <template x-for="product in searchResults" :key="product.id">
                        <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 border-b border-gray-100 last:border-0 cursor-pointer"
                             @click="addProduct(product)">
                            <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                <img x-show="product.image" :src="product.image" class="w-full h-full object-cover">
                                <svg x-show="!product.image" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                                <p class="text-xs text-gray-500" x-text="product.sku"></p>
                            </div>
                            <p class="text-sm font-medium text-gray-900 shrink-0" x-text="product.price"></p>
                        </div>
                    </template>
                </div>

                {{-- Selected Products --}}
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Selected products (<span x-text="selectedProducts.length"></span>)</p>
                    <template x-if="selectedProducts.length === 0">
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="text-sm text-gray-400">Search and select products above.</p>
                        </div>
                    </template>
                    <template x-if="selectedProducts.length > 0">
                        <ul class="border border-gray-200 rounded-lg divide-y divide-gray-100" x-sort="selectedProducts">
                            <template x-for="(product, idx) in selectedProducts" :key="product.id">
                                <li class="flex items-center gap-3 px-4 py-2.5" :data-id="product.id">
                                    <span class="cursor-grab text-gray-300 hover:text-gray-500" x-sort:handle>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                                    </span>
                                    <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                        <img x-show="product.image" :src="product.image" class="w-full h-full object-cover">
                                        <svg x-show="!product.image" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                                        <p class="text-xs text-gray-500" x-text="product.sku"></p>
                                    </div>
                                    <input type="hidden" :name="'products[]'" :value="product.id">
                                    <button type="button" @click="removeProduct(idx)" class="text-gray-300 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </template>
                </div>
            </div>
        </div>

        {{-- Automatic: Condition Builder --}}
        <div x-show="form.collection_type === 'automatic'" class="space-y-6">
            {{-- Match Type --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Match type</h2>
                </div>
                <div class="px-6 py-5">
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="match_type" value="all" x-model="form.match_type" @change="previewAutomatic" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Match all conditions (AND)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="match_type" value="any" x-model="form.match_type" @change="previewAutomatic" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Match any condition (OR)</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Conditions --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800">Conditions</h2>
                    <button type="button" @click="addCondition" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Add condition</button>
                </div>
                <div class="px-6 py-5 space-y-3">
                    <template x-if="conditions.length === 0">
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-sm text-gray-400 mt-1">Add conditions to automatically include products.</p>
                        </div>
                    </template>
                    <template x-for="(condition, idx) in conditions" :key="idx">
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1 grid grid-cols-3 gap-3">
                                <select x-model="condition.field" @change="condition.operator = ''; condition.value = ''; previewAutomatic"
                                        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select field</option>
                                    @foreach ($service::conditionFields() as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <select x-model="condition.operator" @change="previewAutomatic"
                                        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select operator</option>
                                    <template x-for="(opLabel, opKey) in getOperators(condition.field)" :key="opKey">
                                        <option :value="opKey" x-text="opLabel"></option>
                                    </template>
                                </select>
                                <template x-if="isTextField(condition.field)">
                                    <input type="text" x-model="condition.value" @input.debounce="previewAutomatic" placeholder="Value"
                                           class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </template>
                                <template x-if="condition.field === 'brand'">
                                    <select x-model="condition.value" @change="previewAutomatic"
                                            class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </template>
                                <template x-if="condition.field === 'category'">
                                    <select x-model="condition.value" @change="previewAutomatic"
                                            class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </template>
                                <template x-if="condition.field === 'tag'">
                                    <select x-model="condition.value" @change="previewAutomatic"
                                            class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select tag</option>
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </template>
                                <template x-if="isNumericField(condition.field)">
                                    <input type="number" step="0.01" x-model="condition.value" @input.debounce="previewAutomatic" placeholder="Value"
                                           class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </template>
                                <template x-if="condition.field === 'status'">
                                    <select x-model="condition.value" @change="previewAutomatic"
                                            class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </template>
                            </div>
                            <button type="button" @click="removeCondition(idx)" class="text-gray-300 hover:text-red-500 transition-colors shrink-0 mt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <input type="hidden" :name="'conditions['+idx+'][field]'" :value="condition.field">
                            <input type="hidden" :name="'conditions['+idx+'][operator]'" :value="condition.operator">
                            <input type="hidden" :name="'conditions['+idx+'][value]'" :value="condition.value">
                        </div>
                    </template>
                </div>
            </div>

            {{-- Preview --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Matching products</h2>
                    <p class="text-xs text-gray-500 mt-0.5" x-show="previewTotal > 0" x-text="`${previewTotal} products match these conditions`"></p>
                </div>
                <div class="px-6 py-5">
                    <template x-if="previewProducts.length === 0">
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="text-sm text-gray-400">No products match the current conditions.</p>
                        </div>
                    </template>
                    <template x-if="previewProducts.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="product in previewProducts" :key="product.id">
                                <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg">
                                    <div class="w-14 h-14 rounded bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                        <img x-show="product.image" :src="product.image" class="w-full h-full object-cover">
                                        <svg x-show="!product.image" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                                        <p class="text-xs text-gray-500" x-text="product.brand"></p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 shrink-0" x-text="product.formatted_price"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3 pb-8">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                Save collection
            </button>
            <a href="{{ route('admin.collections.index') }}"
               class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition border border-gray-300">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function collectionForm() {
    return {
        form: {
            name: '',
            slug: '',
            collection_type: 'manual',
            status: 'active',
            match_type: 'all',
        },
        selectedProducts: [],
        searchQuery: '',
        searchResults: [],
        conditions: [],
        previewProducts: [],
        previewTotal: 0,

        conditionFields: {
            'title': 'text',
            'product_type': 'text',
            'brand': 'fk',
            'category': 'fk',
            'tag': 'tag',
            'price': 'number',
            'sale_price': 'number',
            'stock': 'number',
            'status': 'select',
            'sku': 'text',
        },
        operatorMap: {
            'title': ['equals','not_equals','contains','starts_with','ends_with'],
            'product_type': ['equals','not_equals','contains','starts_with','ends_with'],
            'brand': ['equals','not_equals','contains_any','contains_all'],
            'category': ['equals','not_equals','contains_any','contains_all'],
            'tag': ['equals','not_equals','contains_any','contains_all'],
            'price': ['eq','neq','gt','lt','gte','lte'],
            'sale_price': ['eq','neq','gt','lt','gte','lte'],
            'stock': ['eq','neq','gt','lt','gte','lte'],
            'status': ['equals','not_equals'],
            'sku': ['equals','not_equals','contains','starts_with','ends_with'],
        },
        operatorLabels: {
            'equals': 'Equals', 'not_equals': 'Not equals', 'contains': 'Contains',
            'starts_with': 'Starts with', 'ends_with': 'Ends with',
            'eq': '=', 'neq': '!=', 'gt': '>', 'lt': '<', 'gte': '>=', 'lte': '<=',
            'contains_any': 'Contains any', 'contains_all': 'Contains all',
        },

        generateSlug() {
            if (!this.form.slug) {
                this.form.slug = this.form.name.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        },

        previewImage(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    document.getElementById('imagePreview').innerHTML = '<img src="' + ev.target.result + '" class="w-full h-full object-cover rounded-lg">';
                };
                reader.readAsDataURL(file);
            }
        },

        async searchProducts() {
            if (!this.searchQuery || this.searchQuery.length < 1) {
                this.searchResults = [];
                return;
            }
            try {
                const res = await fetch('{{ route("admin.collections.search-products") }}?q=' + encodeURIComponent(this.searchQuery) + '&page=1');
                const data = await res.json();
                const selectedIds = new Set(this.selectedProducts.map(p => p.id));
                this.searchResults = data.products.filter(p => !selectedIds.has(p.id));
            } catch (e) {
                this.searchResults = [];
            }
        },

        addProduct(product) {
            const selectedIds = new Set(this.selectedProducts.map(p => p.id));
            if (!selectedIds.has(product.id)) {
                this.selectedProducts.push({...product});
                this.searchResults = this.searchResults.filter(p => p.id !== product.id);
            }
        },

        removeProduct(idx) {
            this.selectedProducts.splice(idx, 1);
        },

        addCondition() {
            this.conditions.push({ field: '', operator: '', value: '' });
        },

        removeCondition(idx) {
            this.conditions.splice(idx, 1);
            this.previewAutomatic();
        },

        getOperators(field) {
            if (!field || !this.operatorMap[field]) return {};
            const ops = this.operatorMap[field];
            const result = {};
            ops.forEach(key => { result[key] = this.operatorLabels[key] || key; });
            return result;
        },

        isTextField(field) {
            return this.conditionFields[field] === 'text' || this.conditionFields[field] === 'select';
        },

        isNumericField(field) {
            return this.conditionFields[field] === 'number';
        },

        async previewAutomatic() {
            const validConditions = this.conditions.filter(c => c.field && c.operator && c.value);
            if (validConditions.length === 0) {
                this.previewProducts = [];
                this.previewTotal = 0;
                return;
            }
            try {
                const res = await fetch('{{ route("admin.collections.preview") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        conditions: validConditions,
                        match_type: this.form.match_type,
                    }),
                });
                const data = await res.json();
                this.previewProducts = data.products;
                this.previewTotal = data.total;
            } catch (e) {
                this.previewProducts = [];
                this.previewTotal = 0;
            }
        },
    };
}
</script>
@endpush
</x-admin-layout>
