<x-admin-layout>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.purchase-orders.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <nav class="text-xs text-gray-400 mb-0.5">
                <a href="{{ route('admin.purchase-orders.index') }}" class="hover:text-gray-600 transition-colors">Purchase Orders</a>
                <span class="mx-1">/</span>
                <span class="text-gray-700">New purchase order</span>
            </nav>
            <h1 class="text-lg font-semibold text-gray-900 leading-tight">New purchase order</h1>
        </div>
    </div>

    <div x-data="purchaseOrderForm()" x-init="init()">
        <form action="{{ route('admin.purchase-orders.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    {{-- Items --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-semibold text-gray-900">Items</h2>
                            <span class="text-xs text-gray-400" x-text="`${items.length} item(s)`"></span>
                        </div>

                        <div class="mb-4">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" x-model="searchQuery" @input="searchProducts()"
                                       placeholder="Search products to add..."
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div x-show="results.length > 0" x-cloak
                                 class="mt-2 border border-gray-200 rounded-xl overflow-hidden max-h-64 overflow-y-auto divide-y divide-gray-100">
                                <template x-for="product in results" :key="product.id">
                                    <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors cursor-pointer" @click="addItem(product)">
                                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                                            <img x-show="product.image" :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                                            <svg x-show="!product.image" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                                            <p class="text-xs text-gray-400" x-text="`${product.sku || '—'} · Stock: ${product.stock}`"></p>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-600">৳<span x-text="product.cost_price.toFixed(2)"></span></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Added items --}}
                        <template x-if="items.length === 0">
                            <div class="text-center py-10">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="text-sm text-gray-500">Search and click a product to add it to this purchase order.</p>
                            </div>
                        </template>

                        <div x-show="items.length > 0" x-cloak class="border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
                            <template x-for="(item, index) in items" :key="item.clientId">
                                <div class="flex items-center gap-3 px-4 py-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="item.name"></p>
                                        <p class="text-xs text-gray-400" x-text="`${item.sku || '—'} · Stock: ${item.stock}`"></p>
                                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-gray-500">Qty</label>
                                        <input type="number" min="1" x-model="item.quantity"
                                               :name="`items[${index}][quantity]`"
                                               class="w-20 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-gray-500">Unit Cost</label>
                                        <input type="number" step="0.01" min="0" x-model="item.unit_cost"
                                               :name="`items[${index}][unit_cost]`"
                                               class="w-28 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 w-28 text-right">৳<span x-text="subtotal(item).toFixed(2)"></span></span>
                                    <button type="button" @click="removeItem(index)"
                                            class="p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        @error('items') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Payment terms, delivery instructions, etc.">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- Details --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-base font-semibold text-gray-900 mb-4">Details</h2>

                        <div class="mb-4">
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1.5">Supplier <span class="text-red-500">*</span></label>
                            <select name="supplier_id" id="supplier_id" required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            @if ($suppliers->isEmpty())
                                <p class="text-xs text-amber-600 mt-2">
                                    No active suppliers yet.
                                    <a href="{{ route('admin.suppliers.create') }}" class="font-medium underline">Create one first</a>.
                                </p>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="expected_date" class="block text-sm font-medium text-gray-700 mb-1.5">Expected Delivery Date</label>
                            <input type="date" name="expected_date" id="expected_date" value="{{ old('expected_date') }}"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('expected_date') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-base font-semibold text-gray-900 mb-4">Summary</h2>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-500">Items</span>
                            <span class="text-sm font-medium text-gray-900" x-text="items.length"></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-t border-gray-100">
                            <span class="text-sm font-semibold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-gray-900">৳<span x-text="total().toFixed(2)"></span></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 justify-end xl:justify-start">
                        <a href="{{ route('admin.purchase-orders.index') }}"
                           class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" :disabled="items.length === 0 || !selectedSupplier()"
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Create Purchase Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @pushOnce('scripts')
    <script>
        function purchaseOrderForm() {
            return {
                searchQuery: '',
                results: [],
                items: [],
                supplierId: '',
                clientId: 0,

                init() {
                    this.supplierId = document.getElementById('supplier_id')?.value ?? '';
                    this.searchProducts();
                },

                selectedSupplier() {
                    return document.getElementById('supplier_id')?.value ?? '';
                },

                async searchProducts() {
                    try {
                        const res = await fetch(`{{ route('admin.purchase-orders.search-products') }}?q=${encodeURIComponent(this.searchQuery)}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.results = data.filter(p => !this.items.some(i => i.id === p.id));
                    } catch (e) {
                        this.results = [];
                    }
                },

                addItem(product) {
                    this.items.push({
                        clientId: ++this.clientId,
                        id: product.id,
                        name: product.name,
                        sku: product.sku,
                        image: product.image,
                        stock: product.stock,
                        quantity: 1,
                        unit_cost: product.cost_price > 0 ? product.cost_price : '',
                    });
                    this.results = [];
                    this.searchQuery = '';
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.searchProducts();
                },

                subtotal(item) {
                    return (Number(item.quantity) || 0) * (Number(item.unit_cost) || 0);
                },

                total() {
                    return this.items.reduce((sum, item) => sum + this.subtotal(item), 0);
                },
            };
        }
    </script>
    @endPushOnce
</x-admin-layout>