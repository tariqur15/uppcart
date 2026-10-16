<x-admin-layout>
<div x-data="collectionForm()" class="max-w-4xl mx-auto">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.collections.index') }}" class="hover:text-indigo-600 transition-colors">Collections</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">{{ $collection->name }}</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Edit collection</h1>

    <form action="{{ route('admin.collections.update', $collection) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Basic information</h2>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $collection->name) }}" required
                           placeholder="e.g. Summer Collection"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">URL handle</label>
                    <div class="text-xs text-gray-400 mb-1">{{ url('/') }}/collections/<span class="text-indigo-600">{{ $collection->slug }}</span></div>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $collection->slug) }}"
                           placeholder="summer-collection"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              placeholder="Describe your collection..."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $collection->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Collection image</label>
                    <div class="flex items-start gap-5">
                        <div class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0" id="imagePreview">
                            @if ($collection->image)
                                <img src="{{ Storage::url($collection->image) }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label for="image" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 cursor-pointer transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                @if ($collection->image) Replace image @else Upload image @endif
                            </label>
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="hidden" @change="previewImage">
                            @if ($collection->image)
                                <label class="inline-flex items-center gap-2 mt-2 text-sm text-red-600 hover:underline cursor-pointer">
                                    <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    Remove image
                                </label>
                            @endif
                            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" @selected(old('status', $collection->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $collection->status) === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $collection->sort_order) }}" min="0"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Collection type</h2>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="relative block cursor-pointer rounded-lg border-2 p-4 transition-colors {{ $collection->collection_type === 'manual' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="collection_type" value="manual" @checked($collection->collection_type === 'manual') class="sr-only">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 {{ $collection->collection_type === 'manual' ? 'border-indigo-500' : 'border-gray-300' }}">
                                <div class="w-2.5 h-2.5 rounded-full bg-indigo-500" @if($collection->collection_type !== 'manual') style="display:none" @endif></div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Manual</p>
                                <p class="text-xs text-gray-500 mt-0.5">Select products individually to include in this collection.</p>
                            </div>
                        </div>
                    </label>
                    <label class="relative block cursor-pointer rounded-lg border-2 p-4 transition-colors {{ $collection->collection_type === 'automatic' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="collection_type" value="automatic" @checked($collection->collection_type === 'automatic') class="sr-only">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 {{ $collection->collection_type === 'automatic' ? 'border-indigo-500' : 'border-gray-300' }}">
                                <div class="w-2.5 h-2.5 rounded-full bg-indigo-500" @if($collection->collection_type !== 'automatic') style="display:none" @endif></div>
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

        @if ($collection->collection_type === 'manual')
        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100"><h2 class="text-base font-semibold text-gray-800">Products</h2></div>
            <div class="px-6 py-5">
                <div class="flex gap-3 mb-4">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="productSearch" placeholder="Search by title, SKU, or brand..."
                               class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div id="searchResults" class="mb-4 border border-gray-200 rounded-lg max-h-60 overflow-y-auto hidden"></div>
                <div id="selectedProducts">
                    <p class="text-sm font-medium text-gray-700 mb-2">Selected products (<span id="selectedCount">{{ $collection->collectionProducts->count() }}</span>)</p>
                    @if ($collection->collectionProducts->isEmpty())
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="text-sm text-gray-400">Search and select products above.</p>
                        </div>
                    @else
                        <ul class="border border-gray-200 rounded-lg divide-y divide-gray-100" id="productList">
                            @foreach ($collection->collectionProducts as $cp)
                            <li class="flex items-center gap-3 px-4 py-2.5" data-product-id="{{ $cp->product_id }}">
                                <span class="cursor-grab text-gray-300 hover:text-gray-500 handle">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                                </span>
                                <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                    @if ($cp->product->image)
                                        <img src="{{ Storage::url($cp->product->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $cp->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $cp->product->sku }}</p>
                                </div>
                                <input type="hidden" name="products[]" value="{{ $cp->product_id }}">
                                <button type="button" class="remove-product text-gray-300 hover:text-red-500 transition-colors" data-product-id="{{ $cp->product_id }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if ($collection->collection_type === 'automatic')
        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100"><h2 class="text-base font-semibold text-gray-800">Match type</h2></div>
            <div class="px-6 py-5">
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="match_type" value="all" @checked(old('match_type', $collection->match_type) === 'all') class="text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Match all conditions (AND)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="match_type" value="any" @checked(old('match_type', $collection->match_type) === 'any') class="text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Match any condition (OR)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800">Conditions</h2>
                <button type="button" id="addConditionBtn" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Add condition</button>
            </div>
            <div class="px-6 py-5 space-y-3" id="conditionsContainer">
                @forelse ($collection->conditions as $idx => $condition)
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg condition-row">
                    <div class="flex-1 grid grid-cols-3 gap-3">
                        <select name="conditions[{{ $idx }}][field]" class="condition-field rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select field</option>
                            @foreach ($service::conditionFields() as $key => $label)
                                <option value="{{ $key }}" @selected($condition->condition_field === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="conditions[{{ $idx }}][operator]" class="condition-operator rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select operator</option>
                        </select>
                        <input type="text" name="conditions[{{ $idx }}][value]" value="{{ $condition->condition_value }}" placeholder="Value"
                               class="condition-value rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="button" class="remove-condition text-gray-300 hover:text-red-500 transition-colors shrink-0 mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @empty
                <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg" id="emptyConditions">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm text-gray-400 mt-1">Add conditions to automatically include products.</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif

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
document.addEventListener('DOMContentLoaded', function() {
    // Search products
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const q = this.value;
                if (q.length < 1) { document.getElementById('searchResults').classList.add('hidden'); return; }
                fetch('{{ route("admin.collections.search-products") }}?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        const container = document.getElementById('searchResults');
                        container.innerHTML = '';
                        container.classList.remove('hidden');
                        const selectedIds = new Set([...document.querySelectorAll('#productList li')].map(li => li.dataset.productId));
                        data.products.forEach(p => {
                            if (selectedIds.has(String(p.id))) return;
                            const div = document.createElement('div');
                            div.className = 'flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 border-b border-gray-100 last:border-0 cursor-pointer';
                            div.innerHTML = `
                                <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                    ${p.image ? '<img src="' + p.image + '" class="w-full h-full object-cover">' : '<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'}
                                </div>
                                <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">${p.name}</p><p class="text-xs text-gray-500">${p.sku}</p></div>
                            `;
                            div.addEventListener('click', () => addProductToList(p));
                            container.appendChild(div);
                        });
                        if (container.children.length === 0) container.innerHTML = '<div class="px-4 py-3 text-sm text-gray-400">No more products to add.</div>';
                    });
            }, 300);
        });
    }

    window.addProductToList = function(product) {
        const list = document.getElementById('productList');
        const empty = document.querySelector('#selectedProducts .text-center.py-8');
        if (empty) empty.remove();

        const li = document.createElement('li');
        li.className = 'flex items-center gap-3 px-4 py-2.5 border-b border-gray-100';
        li.dataset.productId = product.id;
        li.innerHTML = `
            <span class="cursor-grab text-gray-300 hover:text-gray-500 handle"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg></span>
            <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                ${product.image ? '<img src="' + product.image + '" class="w-full h-full object-cover">' : '<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'}
            </div>
            <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">${product.name}</p><p class="text-xs text-gray-500">${product.sku}</p></div>
            <input type="hidden" name="products[]" value="${product.id}">
            <button type="button" class="remove-product text-gray-300 hover:text-red-500 transition-colors" data-product-id="${product.id}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        `;
        if (list) list.appendChild(li);
        else {
            const container = document.getElementById('selectedProducts');
            const ul = document.createElement('ul');
            ul.className = 'border border-gray-200 rounded-lg divide-y divide-gray-100';
            ul.id = 'productList';
            ul.appendChild(li);
            container.appendChild(ul);
        }
        document.getElementById('searchResults').classList.add('hidden');
        document.getElementById('selectedCount').textContent = document.querySelectorAll('#productList li').length;
        attachRemoveHandlers();
    };

    function attachRemoveHandlers() {
        document.querySelectorAll('.remove-product').forEach(btn => {
            btn.removeEventListener('click', handleRemove);
            btn.addEventListener('click', handleRemove);
        });
    }

    function handleRemove(e) {
        const li = e.currentTarget.closest('li');
        li.remove();
        const count = document.querySelectorAll('#productList li, #productList > li').length;
        const counter = document.getElementById('selectedCount');
        if (counter) counter.textContent = count;
        if (count === 0) {
            const container = document.getElementById('selectedProducts');
            const ul = document.getElementById('productList');
            if (ul) ul.remove();
            container.innerHTML += '<div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg"><svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><p class="text-sm text-gray-400">Search and select products above.</p></div>';
        }
    }

    attachRemoveHandlers();
});
</script>
@endpush
</x-admin-layout>
