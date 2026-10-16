<x-admin-layout>
    @php
        $sale = $flashSale ?? null;
        $isEdit = $sale !== null;
        $statuses = [
            'draft' => 'Draft',
            'scheduled' => 'Scheduled (starts automatically)',
            'active' => 'Active now',
            'ended' => 'Ended',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Edit Flash Sale' : 'New Flash Sale' }}</h1>
            <p class="mt-1 text-sm text-gray-500">Configure the promotion, products and pricing.</p>
        </div>
        <a href="{{ route('admin.flash-sales.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Flash Sales
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <h3 class="text-sm font-semibold text-red-700 mb-1">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside space-y-1 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ $isEdit ? route('admin.flash-sales.update', $sale) : route('admin.flash-sales.store') }}"
          x-data="flashSaleForm({{ $isEdit ? $initialProductsJs : '[]' }})"
          x-init="init()"
          class="space-y-6">
        @csrf
        @if ($isEdit)
            @method('PATCH')
        @endif
        <input type="hidden" name="products" :value="JSON.stringify(rows)">

        {{-- Basic info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sale name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $sale?->name ?? '') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white"
                           placeholder="e.g. Mega Monday Flash Sale">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white"
                              placeholder="Optional description shown to customers">{{ old('description', $sale?->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Starts at <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $sale?->starts_at?->format('Y-m-d\TH:i') ?? '') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ends at <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $sale?->ends_at?->format('Y-m-d\TH:i') ?? '') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    @if ($isEdit && $sale?->hasOrders()
                        && in_array($sale?->status, [\App\Models\FlashSale::STATUS_ENDED, \App\Models\FlashSale::STATUS_ACTIVE], true))
                        <p class="text-sm text-amber-600 mb-2">This sale has order history; its status can no longer be changed.</p>
                    @else
                        <div class="flex flex-wrap gap-3">
                            @foreach ($statuses as $key => $label)
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="status" value="{{ $key }}"
                                           {{ (old('status', $sale?->status ?? 'draft') === $key) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-semibold text-gray-900">Products &amp; pricing</h2>
                <span class="text-xs text-gray-500" x-text="`${rows.length} product(s) added`"></span>
            </div>
            <p class="text-sm text-gray-500 mb-4">Search and add products. For variant products you can discount the whole product or specific variants.</p>

            {{-- Search --}}
            <div class="relative mb-5">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text"
                               x-model="search"
                               x-on:keyup.debounce.300ms="searchProducts()"
                               placeholder="Search products by name or SKU..."
                               class="w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                    </div>
                </div>
                <div x-show="results.length > 0" x-cloak
                     class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-72 overflow-y-auto">
                    <template x-for="product in results" :key="product.id">
                        <button type="button" x-on:click="addRow(product)"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 border-b border-gray-50 last:border-0">
                            <template x-if="product.image">
                                <img :src="product.image" class="w-10 h-10 rounded-lg object-cover bg-gray-100">
                            </template>
                            <template x-if="!product.image">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                                <p class="text-xs text-gray-400" x-text="product.variants_enabled ? `${product.variants.length} variants` : ''"></p>
                            </div>
                            <span class="text-sm font-medium text-gray-700" x-text="`{{ $currencySymbol }}` + product.price.toFixed(2)"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Added rows --}}
            <div x-show="rows.length > 0" x-cloak class="space-y-4">
                <template x-for="(row, index) in rows" :key="row.key">
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-3 p-4 bg-gray-50 border-b border-gray-200">
                            <template x-if="row.image">
                                <img :src="row.image" class="w-12 h-12 rounded-lg object-cover bg-white">
                            </template>
                            <template x-if="!row.image">
                                <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="row.product_name"></p>
                                <p class="text-xs text-gray-400" x-text="`Regular: {{ $currencySymbol }}` + row.regular_price.toFixed(2)"></p>
                            </div>
                            <button type="button" x-on:click="removeRow(index)"
                                    class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                        {{-- Whole vs variants --}}
                        <div class="px-4 pt-4">
                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" :name="`scope_${row.key}`" value="whole" x-model="row.scope"
                                           :disabled="!row.variants"
                                           class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    Whole product
                                </label>
                                <label x-show="row.variants" class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" :name="`scope_${row.key}`" value="variants" x-model="row.scope"
                                           class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    Specific variants
                                </label>
                            </div>
                        </div>

                        {{-- Whole product pricing --}}
                        <div x-show="row.scope === 'whole'" class="px-4 py-4 grid grid-cols-1 md:grid-cols-4 gap-4" x-cloak>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Discount type</label>
                                <select x-model="row.discount_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed_amount">Fixed amount off</option>
                                    <option value="fixed_price">Set flash price</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1" x-text="row.discount_type === 'percentage' ? 'Discount %' : row.discount_type === 'fixed_amount' ? 'Amount off' : 'Flash price'"></label>
                                <input type="number" min="0" step="0.01" x-model.number="row.discount_value"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Flash price</label>
                                <p class="text-sm font-semibold text-indigo-600 py-2" x-text="`{{ $currencySymbol }}` + flashPrice(row).toFixed(2)"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Sale stock</label>
                                    <input type="number" min="0" x-model="row.sale_stock" placeholder="Unlimited"
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Max/customer</label>
                                    <input type="number" min="1" x-model="row.max_per_customer" placeholder="Unlimited"
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                </div>
                            </div>
                        </div>

                        {{-- Variant pricing --}}
                        <div x-show="row.scope === 'variants'" class="px-4 py-4" x-cloak>
                            <p class="text-xs font-medium text-gray-500 mb-3">Configure a discount for each variant below:</p>
                            <div class="space-y-3">
                                <template x-for="(v, vi) in row.variants" :key="v.name">
                                    <div class="flex flex-wrap items-center gap-3 p-3 border border-gray-100 rounded-lg bg-gray-50/50">
                                        <span class="text-sm font-medium text-gray-800 w-28 truncate" x-text="v.name"></span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Discount type</label>
                                            <select x-model="v.discount_type" class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                <option value="percentage">%</option>
                                                <option value="fixed_amount">Off</option>
                                                <option value="fixed_price">Price</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Value</label>
                                            <input type="number" min="0" step="0.01" x-model.number="v.discount_value"
                                                   class="w-28 rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Flash price</label>
                                            <p class="text-sm font-semibold text-indigo-600 py-1.5" x-text="`{{ $currencySymbol }}` + variantFlashPrice(row, v).toFixed(2)"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Sale stock</label>
                                            <input type="number" min="0" x-model="v.sale_stock" placeholder="Unlim"
                                                   class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Max/cust</label>
                                            <input type="number" min="1" x-model="v.max_per_customer" placeholder="Unlim"
                                                   class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <p x-show="rows.length === 0" class="text-center text-sm text-gray-400 py-8">
                No products added yet. Search above to add products to this sale.
            </p>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.flash-sales.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg transition">Cancel</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ $isEdit ? 'Update Flash Sale' : 'Create Flash Sale' }}
            </button>
        </div>
    </form>

    <script>
        function flashSaleForm(initialProducts) {
            return {
                search: '',
                results: [],
                rows: [],
                init() {
                    this.rows = initialProducts;
                },
                async searchProducts() {
                    if (!this.search.trim()) {
                        this.results = [];
                        return;
                    }
                    try {
                        const res = await fetch(`/admin/flash-sales/search-products/ajax?q=${encodeURIComponent(this.search)}`);
                        const data = await res.json();
                        this.results = data.map(p => ({ ...p, added: this.rows.some(r => r.product_id === p.id) }));
                    } catch (e) {
                        this.results = [];
                    }
                },
                addRow(product) {
                    if (this.rows.some(r => r.product_id === product.id)) {
                        this.search = '';
                        this.results = [];
                        return;
                    }
                    const variants = (product.variants || []).map(v => ({
                        name: v.name,
                        regular_price: v.price,
                        discount_type: 'percentage',
                        discount_value: 0,
                        sale_stock: '',
                        max_per_customer: '',
                    }));
                    this.rows.push({
                        key: `r${Date.now()}_${product.id}`,
                        product_id: product.id,
                        product_name: product.name,
                        image: product.image,
                        regular_price: product.price,
                        variants: variants,
                        scope: (variants.length > 0 ? 'whole' : 'whole'),
                        discount_type: 'percentage',
                        discount_value: 0,
                        sale_stock: '',
                        max_per_customer: '',
                    });
                    this.search = '';
                    this.results = [];
                },
                removeRow(index) {
                    this.rows.splice(index, 1);
                },
                flashPrice(row) {
                    return this.computeFlashPrice(row.regular_price, row.discount_type, row.discount_value);
                },
                variantFlashPrice(row, v) {
                    return this.computeFlashPrice(v.regular_price, v.discount_type, v.discount_value);
                },
                computeFlashPrice(regular, type, value) {
                    let price = regular;
                    if (type === 'percentage') price = regular * (1 - (value || 0) / 100);
                    else if (type === 'fixed_amount') price = regular - (value || 0);
                    else if (type === 'fixed_price') price = (value || 0);
                    price = Math.max(0, price);
                    price = Math.round(price * 100) / 100;
                    if (price <= 0) return 0;
                    return price;
                },
            };
        }
        window.flashSaleForm = flashSaleForm;
    </script>
</x-admin-layout>
