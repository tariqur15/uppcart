<x-admin-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Product
        </a>
    </div>

    {{-- Search & Filters Form --}}
    <form method="GET" x-data="adminProductSearch()" @submit="if (searchQuery.length < 2) return true; $event.preventDefault();"
          class="mb-6 space-y-4">
        <div class="flex flex-wrap items-center gap-3">
            {{-- AJAX Search --}}
            <div class="flex-1 min-w-[220px] relative" @click.away="resultsOpen = false">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" x-model="searchQuery" @input.debounce="search()" @focus="search()"
                           autocomplete="off"
                           placeholder="Search by name, slug, or SKU..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                </div>

                {{-- AJAX Results Dropdown --}}
                <div x-show="resultsOpen && results.length > 0" x-cloak
                     class="absolute top-full mt-1.5 left-0 right-0 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50">
                    <template x-for="product in results" :key="product.id">
                        <a :href="product.edit_url"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                                <p class="text-xs text-gray-400" x-text="product.sku || '—'"></p>
                            </div>
                            <span class="text-xs font-medium" :class="product.status === 'active' ? 'text-emerald-600' : 'text-amber-600'"
                                  x-text="product.status"></span>
                            <span class="text-xs text-gray-400" x-text="'$' + parseFloat(product.sale_price || product.price).toFixed(2)"></span>
                        </a>
                    </template>
                </div>
            </div>

            {{-- Per Page --}}
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-500 whitespace-nowrap">Show</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()"
                        class="rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                    @foreach ([10, 20, 50, 100] as $option)
                        <option value="{{ $option }}" @selected(($perPage ?? 20) == $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="bg-indigo-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                Search
            </button>
        </div>

        {{-- Advanced Filters --}}
        <div x-data="{ open: {{ request()->anyFilled(['status', 'type', 'category_id', 'stock_status', 'stock_from', 'stock_to']) ? 'true' : 'false' }} }"
             class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <button @click="open = !open" type="button"
                    class="flex items-center gap-2 w-full px-5 py-3 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                <span>Advanced Filters</span>
                @if (request()->anyFilled(['status', 'type', 'category_id', 'stock_status', 'stock_from', 'stock_to']))
                    <span class="ml-auto text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-semibold">Active</span>
                @endif
            </button>

            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="border-t border-gray-100 px-5 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                        <select name="status"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Type</label>
                        <select name="type"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="physical" @selected(request('type') === 'physical')>Physical</option>
                            <option value="digital" @selected(request('type') === 'digital')>Digital</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Category</label>
                        <select name="category_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Stock Status</label>
                        <select name="stock_status"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="in_stock" @selected(request('stock_status') === 'in_stock')>In Stock</option>
                            <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>Out of Stock</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Stock Range</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="stock_from" placeholder="Min" value="{{ request('stock_from') }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="text-gray-400 shrink-0">—</span>
                            <input type="number" name="stock_to" placeholder="Max" value="{{ request('stock_to') }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.products.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear Filters
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- Product Count, Settings & Bulk Actions --}}
    <div x-data="bulkActions()">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-500">
                Showing <span class="font-medium text-gray-700">{{ $products->firstItem() }}–{{ $products->lastItem() }}</span>
                of <span class="font-medium text-gray-700">{{ $products->total() }}</span> products
            </p>
            <div class="relative">
                <button type="button" @click="showSettings = !showSettings"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Table settings">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>

                {{-- Settings Panel --}}
                <div x-show="showSettings" x-cloak
                     @click.outside="showSettings = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="absolute right-0 top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl p-6 z-50"
                     style="width: 700px; max-width: 90vw;">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900">Columns</h3>
                        <button type="button" @click="showSettings = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach ([
                            'image' => 'Product Image',
                            'name' => 'Product Name',
                            'sku' => 'SKU',
                            'stock' => 'Stock',
                            'price' => 'Price',
                            'cost_price' => 'Cost Price',
                            'profit' => 'Profit',
                            'margin' => 'Margin',
                            'brand' => 'Brand',
                            'category' => 'Category',
                            'collections' => 'Collections',
                            'tags' => 'Tags',
                            'type' => 'Product Type',
                            'rating' => 'Rating',
                            'total_sales' => 'Orders',
                            'views' => 'Views (7d)',
                            'featured' => 'Featured',
                            'status' => 'Status',
                            'created_at' => 'Created Date',
                            'actions' => 'Actions',
                        ] as $key => $label)
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox"
                                       :checked="columns.{{ $key }}"
                                       @change="toggleColumn('{{ $key }}')"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    {{-- Bulk Action Toolbar --}}
        <div x-show="selected.length > 0" x-cloak
             class="mb-4 p-3 bg-indigo-50/80 border border-indigo-200 rounded-xl flex flex-wrap items-center gap-3 shadow-sm">
            <span class="text-sm font-medium text-indigo-700" x-text="`${selected.length} product(s) selected`"></span>
            <div class="flex flex-wrap gap-2 ml-auto">
                <form method="POST" :action="bulkActionUrl('toggle-status', 'active')" class="inline">
                    @csrf
                    @method('PATCH')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <input type="hidden" name="status" value="active">
                    <button type="submit"
                            class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm">
                        Activate
                    </button>
                </form>
                <form method="POST" :action="bulkActionUrl('toggle-status', 'draft')" class="inline">
                    @csrf
                    @method('PATCH')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <input type="hidden" name="status" value="draft">
                    <button type="submit"
                            class="px-3 py-1.5 bg-amber-600 text-white text-xs font-medium rounded-lg hover:bg-amber-700 transition shadow-sm">
                        Set Draft
                    </button>
                </form>
                <button @click="openBulkEdit = true"
                        class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                    Edit
                </button>
                <form method="POST" :action="bulkActionUrl('delete')" class="inline"
                      @submit="return confirm(`Delete ${selected.length} product(s)?`)">
                    @csrf
                    @method('POST')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit"
                            class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="w-12 px-4 py-3.5">
                                <input type="checkbox" @click="toggleAll($event.target.checked)"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th x-show="columns.image" class="w-14 px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Image</th>
                            <th x-show="columns.name" class="text-left px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Product</th>
                            <th x-show="columns.sku" class="text-center px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">SKU</th>
                            @php
                                $sortParams = request()->except(['sort', 'direction', 'page']);
                                $sortIcons = [
                                    'stock' => ['icon' => 'stock', 'label' => 'Stock'],
                                    'price' => ['icon' => 'price', 'label' => 'Price'],
                                    'total_sales' => ['icon' => 'orders', 'label' => 'Orders'],
                                    'views' => ['icon' => 'views', 'label' => 'Views'],
                                ];
                                $sortableColumns = ['stock', 'price', 'total_sales' => 'orders', 'views' => 'views'];
                            @endphp
                            <th x-show="columns.stock" class="text-center px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                                <a href="{{ route('admin.products.index', array_merge($sortParams, ['sort' => 'stock', 'direction' => $sort === 'stock' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="inline-flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Stock
                                    @if ($sort === 'stock')
                                        <svg class="w-3.5 h-3.5 {{ $direction === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th x-show="columns.price" class="text-center px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                                <a href="{{ route('admin.products.index', array_merge($sortParams, ['sort' => 'price', 'direction' => $sort === 'price' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="inline-flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Price
                                    @if ($sort === 'price')
                                        <svg class="w-3.5 h-3.5 {{ $direction === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th x-show="columns.cost_price" class="text-right px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Cost</th>
                            <th x-show="columns.profit" class="text-right px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Profit</th>
                            <th x-show="columns.margin" class="text-right px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Margin</th>
                            <th x-show="columns.brand" class="text-left px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Brand</th>
                            <th x-show="columns.category" class="text-center px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Category</th>
                            <th x-show="columns.collections" class="text-left px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Collections</th>
                            <th x-show="columns.tags" class="text-left px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tags</th>
                            <th x-show="columns.type" class="text-left px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Type</th>
                            <th x-show="columns.rating" class="text-center px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Rating</th>
                            <th x-show="columns.total_sales" class="text-right px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                                <a href="{{ route('admin.products.index', array_merge($sortParams, ['sort' => 'orders', 'direction' => $sort === 'orders' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="inline-flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Orders
                                    @if ($sort === 'orders')
                                        <svg class="w-3.5 h-3.5 {{ $direction === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th x-show="columns.views" class="text-right px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                                <a href="{{ route('admin.products.index', array_merge($sortParams, ['sort' => 'views', 'direction' => $sort === 'views' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                   class="inline-flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Views
                                    @if ($sort === 'views')
                                        <svg class="w-3.5 h-3.5 {{ $direction === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th x-show="columns.featured" class="text-center px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Featured</th>
                            <th x-show="columns.status" class="text-center px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Status</th>
                            <th x-show="columns.created_at" class="text-left px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Created</th>
                            <th x-show="columns.actions" class="text-right px-4 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-b border-gray-100 hover:bg-gray-50/70 transition-colors"
                                :class="{ 'bg-indigo-50/40': selected.includes({{ $product->id }}) }">
                                <td class="px-4 py-3.5">
                                    <input type="checkbox" :checked="selected.includes({{ $product->id }})"
                                           @click="toggle({{ $product->id }})"
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td x-show="columns.image" class="px-4 py-3.5">
                                    @if ($product->image)
                                        <img src="{{ Storage::disk('public')->url($product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="w-10 h-10 object-cover rounded-lg border border-gray-200">
                                    @else
                                        <div class="w-10 h-10 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td x-show="columns.name" class="px-4 py-3.5">
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">/{{ $product->slug }}</p>
                                </td>
                                <td x-show="columns.sku" class="px-4 py-3.5 text-center">
                                    <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md">{{ $product->sku ?? '—' }}</span>
                                </td>
                                <td x-show="columns.stock" class="px-4 py-3.5 text-center">
                                    @if ($product->stock > 0 || $product->type === 'digital')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $product->stock > 0 ? $product->stock . ' · ' : '' }}In Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                            Out of Stock
                                        </span>
                                    @endif
                                </td>
                                <td x-show="columns.price" class="px-4 py-3.5 text-center font-medium text-gray-900">
                                    @if ($product->sale_price)
                                        <span class="text-red-600">{{ $currencySymbol }}{{ number_format($product->sale_price, 2) }}</span>
                                        <span class="text-gray-400 text-xs line-through ml-1">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                                    @else
                                        {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                                    @endif
                                </td>
                                <td x-show="columns.cost_price" class="px-4 py-3.5 text-right text-gray-600">
                                    {{ $product->cost_price ? '$' . number_format($product->cost_price, 2) : '—' }}
                                </td>
                                @php
                                    $effectivePrice = $product->sale_price ?: $product->price;
                                    $profit = $product->cost_price ? $effectivePrice - $product->cost_price : null;
                                    $margin = $product->cost_price && $effectivePrice > 0 ? round(($profit / $effectivePrice) * 100, 1) : null;
                                @endphp
                                <td x-show="columns.profit" class="px-4 py-3.5 text-right">
                                    @if ($profit !== null)
                                        <span class="font-medium {{ $profit >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                            {{ $currencySymbol }}{{ number_format($profit, 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td x-show="columns.margin" class="px-4 py-3.5 text-right">
                                    @if ($margin !== null)
                                        <span class="font-medium {{ $margin >= 50 ? 'text-emerald-600' : ($margin >= 20 ? 'text-amber-500' : 'text-red-500') }}">
                                            {{ $margin }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td x-show="columns.brand" class="px-4 py-3.5">
                                    @if ($product->brand)
                                        <span class="inline-block px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium">
                                            {{ $product->brand->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td x-show="columns.category" class="px-4 py-3.5 text-center">
                                    <span class="inline-block px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td x-show="columns.collections" class="px-4 py-3.5">
                                    @if ($product->collections->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($product->collections->take(2) as $collection)
                                                <span class="inline-block px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-xs font-medium">
                                                    {{ $collection->name }}
                                                </span>
                                            @endforeach
                                            @if ($product->collections->count() > 2)
                                                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-xs">
                                                    +{{ $product->collections->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td x-show="columns.tags" class="px-4 py-3.5">
                                    @if ($product->tags->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($product->tags->take(2) as $tag)
                                                <span class="inline-block px-2 py-0.5 bg-amber-50 text-amber-600 rounded text-xs font-medium">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                            @if ($product->tags->count() > 2)
                                                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-xs">
                                                    +{{ $product->tags->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td x-show="columns.type" class="px-4 py-3.5">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ match($product->type) { 'digital' => 'bg-violet-50 text-violet-700', 'variable' => 'bg-blue-50 text-blue-700', default => 'bg-gray-100 text-gray-600' } }}">
                                        {{ ucfirst($product->type) }}
                                    </span>
                                </td>
                                @php
                                    $avgRating = $stats['ratings'][$product->id]->avg_rating ?? null;
                                    $reviewCount = $stats['ratings'][$product->id]->review_count ?? 0;
                                    $totalOrders = $product->total_orders_count ?? $stats['orders'][$product->id]->total_orders ?? 0;
                                    $views7d = $product->views_7d_count ?? $stats['views'][$product->id]->views_7d ?? 0;
                                @endphp
                                <td x-show="columns.rating" class="px-4 py-3.5 text-center">
                                    @if ($avgRating)
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">{{ number_format($avgRating, 1) }}</span>
                                            <span class="text-xs text-gray-400">({{ $reviewCount }})</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">No reviews</span>
                                    @endif
                                </td>
                                <td x-show="columns.total_sales" class="px-4 py-3.5 text-right">
                                    <span class="font-medium {{ $totalOrders > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $totalOrders }}
                                    </span>
                                </td>
                                <td x-show="columns.views" class="px-4 py-3.5 text-right">
                                    <button type="button"
                                            @click="openAnalytics({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                            class="inline-flex items-center gap-1 font-medium {{ $views7d > 0 ? 'text-gray-700 hover:text-indigo-600' : 'text-gray-400 hover:text-gray-600' }} cursor-pointer transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ $views7d }}
                                    </button>
                                </td>
                                <td x-show="columns.featured" class="px-4 py-3.5 text-center">
                                    <button type="button"
                                            @click="toggleFeatured({{ $product->id }}, $event)"
                                            class="p-1 rounded-lg hover:bg-amber-50 transition-colors cursor-pointer"
                                            title="Toggle featured">
                                        @if ($product->featured)
                                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300 hover:text-amber-300 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                            </svg>
                                        @endif
                                    </button>
                                </td>
                                <td x-show="columns.status" class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $product->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $product->status === 'active' ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                                        {{ $product->status }}
                                    </span>
                                </td>
                                <td x-show="columns.created_at" class="px-4 py-3.5 text-sm text-gray-500">
                                    {{ $product->created_at->format('M d, Y') }}
                                </td>
                                <td x-show="columns.actions" class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('products.show', $product) }}" target="_blank"
                                           class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                           title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                              onsubmit="return confirm('Delete {{ $product->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="px-4 py-16 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="text-gray-500 text-sm">No products found.</p>
                                    <a href="{{ route('admin.products.index') }}"
                                       class="text-indigo-600 text-sm font-medium hover:text-indigo-800 mt-1 inline-block">Clear filters</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-600">
                    Showing
                    <span class="font-medium">{{ $products->firstItem() ?: 0 }}</span>
                    to
                    <span class="font-medium">{{ $products->lastItem() ?: 0 }}</span>
                    of
                    <span class="font-medium">{{ $products->total() }}</span>
                    results
                </p>

                @if ($products->hasPages())
                    <div class="flex items-center gap-1">
                        {{-- Previous --}}
                        @if ($products->onFirstPage())
                            <span class="px-3 py-1.5 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-default">&laquo; Previous</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">&laquo; Previous</a>
                        @endif

                        {{-- Pages --}}
                        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            @if ($page == $products->currentPage())
                                <span class="px-3 py-1.5 text-sm font-semibold text-white bg-indigo-600 border border-indigo-600 rounded-lg cursor-default">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Next &raquo;</a>
                        @else
                            <span class="px-3 py-1.5 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-default">Next &raquo;</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Analytics Modal --}}
        <div x-show="openAnalyticsModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
             style="display: none;">
            <div x-show="openAnalyticsModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="openAnalyticsModal = false" class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm"></div>
            <div x-show="openAnalyticsModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Product Analytics</h3>
                        <p class="text-sm text-gray-500 mt-0.5" x-text="analyticsProduct.name"></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex bg-gray-100 rounded-lg p-0.5">
                            <template x-for="p in ['24h', '7d', '30d', '90d']" :key="p">
                                <button type="button"
                                        @click="analyticsPeriod = p; loadAnalytics()"
                                        :class="analyticsPeriod === p ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
                                        x-text="p"></button>
                            </template>
                        </div>
                        <button @click="openAnalyticsModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- Content --}}
                <div class="overflow-y-auto p-6" x-ref="analyticsContent">
                    <template x-if="analyticsLoading">
                        <div class="flex items-center justify-center py-16">
                            <svg class="animate-spin h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </template>
                    <template x-if="!analyticsLoading && analyticsData">
                        <div>
                            {{-- Summary Cards --}}
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-indigo-50 rounded-xl p-4">
                                    <p class="text-sm font-medium text-indigo-600">Total Views</p>
                                    <p class="text-2xl font-bold text-indigo-900 mt-1" x-text="analyticsData.total_views.toLocaleString()"></p>
                                </div>
                                <div class="bg-violet-50 rounded-xl p-4">
                                    <p class="text-sm font-medium text-violet-600">Unique Visitors</p>
                                    <p class="text-2xl font-bold text-violet-900 mt-1" x-text="analyticsData.unique_visitors.toLocaleString()"></p>
                                </div>
                            </div>

                            {{-- Chart Placeholder: Views by Day --}}
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Views Over Time</h4>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex items-end gap-1 h-32" x-show="analyticsData.views_by_day.length > 0">
                                        <template x-for="(day, idx) in analyticsData.views_by_day" :key="idx">
                                            <div class="flex-1 flex flex-col items-center justify-end h-full group relative">
                                                <div class="w-full bg-indigo-400 rounded-t hover:bg-indigo-500 transition-colors"
                                                     :style="`height: ${Math.max((day.views / Math.max(...analyticsData.views_by_day.map(d => d.views))) * 100, 4)}%`"></div>
                                                <span class="text-[10px] text-gray-400 mt-1 hidden group-hover:block absolute -top-6 bg-gray-800 text-white px-1.5 py-0.5 rounded text-xs whitespace-nowrap" x-text="day.date + ': ' + day.views"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <p x-show="analyticsData.views_by_day.length === 0" class="text-sm text-gray-400 text-center py-8">No data for this period</p>
                                </div>
                            </div>

                            {{-- Breakdown Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- By Device --}}
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">By Device</h4>
                                    <div class="space-y-2">
                                        <template x-for="item in analyticsData.by_device" :key="item.device_type">
                                            <div class="flex items-center gap-3">
                                                <div class="w-20 text-xs text-gray-500 truncate capitalize" x-text="item.device_type || 'Unknown'"></div>
                                                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                                    <div class="bg-indigo-400 h-full rounded-full transition-all"
                                                         :style="`width: ${analyticsData.total_views > 0 ? (item.count / analyticsData.total_views * 100) : 0}%`"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 w-10 text-right" x-text="item.count"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- By Browser --}}
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">By Browser</h4>
                                    <div class="space-y-2">
                                        <template x-for="item in analyticsData.by_browser" :key="item.browser">
                                            <div class="flex items-center gap-3">
                                                <div class="w-20 text-xs text-gray-500 truncate" x-text="item.browser || 'Unknown'"></div>
                                                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                                    <div class="bg-emerald-400 h-full rounded-full transition-all"
                                                         :style="`width: ${analyticsData.total_views > 0 ? (item.count / analyticsData.total_views * 100) : 0}%`"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 w-10 text-right" x-text="item.count"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- By OS --}}
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">By OS</h4>
                                    <div class="space-y-2">
                                        <template x-for="item in analyticsData.by_os" :key="item.os">
                                            <div class="flex items-center gap-3">
                                                <div class="w-20 text-xs text-gray-500 truncate" x-text="item.os || 'Unknown'"></div>
                                                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                                    <div class="bg-amber-400 h-full rounded-full transition-all"
                                                         :style="`width: ${analyticsData.total_views > 0 ? (item.count / analyticsData.total_views * 100) : 0}%`"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 w-10 text-right" x-text="item.count"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- By Traffic Source --}}
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Traffic Source</h4>
                                    <div class="space-y-2">
                                        <template x-for="item in analyticsData.by_traffic_source" :key="item.traffic_source">
                                            <div class="flex items-center gap-3">
                                                <div class="w-20 text-xs text-gray-500 truncate capitalize" x-text="item.traffic_source || 'Unknown'"></div>
                                                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                                    <div class="bg-violet-400 h-full rounded-full transition-all"
                                                         :style="`width: ${analyticsData.total_views > 0 ? (item.count / analyticsData.total_views * 100) : 0}%`"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 w-10 text-right" x-text="item.count"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- By Country --}}
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Top Countries</h4>
                                    <div class="space-y-2">
                                        <template x-for="item in analyticsData.by_country" :key="item.country">
                                            <div class="flex items-center gap-3">
                                                <div class="w-20 text-xs text-gray-500 truncate uppercase" x-text="item.country"></div>
                                                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                                    <div class="bg-blue-400 h-full rounded-full transition-all"
                                                         :style="`width: ${analyticsData.total_views > 0 ? (item.count / analyticsData.total_views * 100) : 0}%`"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 w-10 text-right" x-text="item.count"></span>
                                            </div>
                                        </template>
                                        <p x-show="analyticsData.by_country.length === 0" class="text-xs text-gray-400">No location data yet</p>
                                    </div>
                                </div>

                                {{-- By City --}}
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Top Cities</h4>
                                    <div class="space-y-2">
                                        <template x-for="item in analyticsData.by_city" :key="item.city">
                                            <div class="flex items-center gap-3">
                                                <div class="w-20 text-xs text-gray-500 truncate" x-text="item.city"></div>
                                                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                                    <div class="bg-pink-400 h-full rounded-full transition-all"
                                                         :style="`width: ${analyticsData.total_views > 0 ? (item.count / analyticsData.total_views * 100) : 0}%`"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 w-10 text-right" x-text="item.count"></span>
                                            </div>
                                        </template>
                                        <p x-show="analyticsData.by_city.length === 0" class="text-xs text-gray-400">No location data yet</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Bulk Edit Modal --}}
        <div x-show="openBulkEdit" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
             style="display: none;">
            <div x-show="openBulkEdit" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="openBulkEdit = false" class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm"></div>
            <div x-show="openBulkEdit" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Bulk Edit Products</h3>
                <p class="text-sm text-gray-500 mb-6" x-text="`Editing ${selected.length} product(s). Fields left blank won't be changed.`"></p>

                <form method="POST" :action="bulkActionUrl('update')">
                    @csrf
                    @method('PATCH')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">— Keep current —</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">— Keep current —</option>
                                <option value="active">Active</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="stock" min="0"
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Leave blank to keep current">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price</label>
                            <input type="number" step="0.01" name="sale_price" min="0"
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Leave blank to keep current">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="openBulkEdit = false"
                                class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
    <script>
        function bulkActions() {
            return {
                selected: [],
                openBulkEdit: false,
                showSettings: false,
                openAnalyticsModal: false,
                analyticsLoading: false,
                analyticsPeriod: '7d',
                analyticsProduct: { id: null, name: '' },
                analyticsData: null,
                columns: {
                    image: true,
                    name: true,
                    sku: true,
                    stock: true,
                    price: true,
                    cost_price: false,
                    profit: false,
                    margin: false,
                    brand: false,
                    category: true,
                    collections: false,
                    tags: false,
                    type: false,
                    rating: false,
                    total_sales: false,
                    views: false,
                    featured: false,
                    status: true,
                    created_at: false,
                    actions: true,
                },
                init() {
                    this.selected = [];
                    const saved = localStorage.getItem('admin_product_columns');
                    if (saved) {
                        try { Object.assign(this.columns, JSON.parse(saved)); } catch (e) {}
                    }
                    this.$watch('columns', (val) => {
                        localStorage.setItem('admin_product_columns', JSON.stringify(val));
                    }, { deep: true });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') this.showSettings = false;
                    });
                },
                toggleColumn(key) {
                    this.columns[key] = !this.columns[key];
                },
                toggle(id) {
                    const idx = this.selected.indexOf(id);
                    if (idx === -1) {
                        this.selected.push(id);
                    } else {
                        this.selected.splice(idx, 1);
                    }
                },
                toggleAll(checked) {
                    const ids = JSON.parse('{{ $products->pluck("id")->toJson() }}');
                    this.selected = checked ? ids : [];
                },
                bulkActionUrl(action, param) {
                    const urls = {
                        'toggle-status': '{{ route("admin.products.bulk-toggle-status") }}',
                        'delete': '{{ route("admin.products.bulk-delete") }}',
                        'update': '{{ route("admin.products.bulk-update") }}',
                    };
                    return urls[action] || '#';
                },
                toggleFeatured(productId, event) {
                    event.preventDefault();
                    const btn = event.currentTarget;
                    btn.disabled = true;
                    fetch(`{{ url('/admin/products') }}/${productId}/toggle-featured`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(r => {
                        if (!r.ok) throw new Error(r.status);
                        return r.json();
                    })
                    .then(data => {
                        btn.disabled = false;
                        const svg = btn.querySelector('svg');
                        if (data.featured) {
                            svg.setAttribute('fill', 'currentColor');
                            svg.setAttribute('stroke', 'none');
                            svg.setAttribute('stroke-width', '0');
                            svg.classList.remove('text-gray-300', 'hover:text-amber-300');
                            svg.classList.add('text-amber-400');
                            svg.innerHTML = '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>';
                        } else {
                            svg.setAttribute('fill', 'none');
                            svg.setAttribute('stroke', 'currentColor');
                            svg.setAttribute('stroke-width', '1.5');
                            svg.classList.remove('text-amber-400');
                            svg.classList.add('text-gray-300', 'hover:text-amber-300');
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>';
                        }
                    })
                    .catch(() => {
                        btn.disabled = false;
                    });
                },
                openAnalytics(productId, productName) {
                    this.analyticsProduct = { id: productId, name: productName };
                    this.analyticsData = null;
                    this.analyticsLoading = true;
                    this.openAnalyticsModal = true;
                    this.loadAnalytics();
                },
                loadAnalytics() {
                    this.analyticsLoading = true;
                    fetch(`{{ url('/admin/products') }}/${this.analyticsProduct.id}/analytics?period=${this.analyticsPeriod}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.analyticsData = data;
                        this.analyticsLoading = false;
                    })
                    .catch(() => {
                        this.analyticsLoading = false;
                    });
                }
            };
        }

        function adminProductSearch() {
            return {
                searchQuery: '{{ request('search') ?? '' }}',
                results: [],
                resultsOpen: false,
                search() {
                    if (this.searchQuery.length < 2) {
                        this.results = [];
                        this.resultsOpen = false;
                        return;
                    }
                    fetch(`{{ route('admin.products.search-ajax') }}?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(r => r.json())
                        .then(data => {
                            this.results = data.products;
                            this.resultsOpen = true;
                        })
                        .catch(() => {
                            this.results = [];
                            this.resultsOpen = false;
                        });
                }
            };
        }
    </script>
    @endPushOnce
</x-admin-layout>
