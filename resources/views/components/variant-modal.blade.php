@props(['product'])

@php
    $variantOptions = collect($product->variant_options ?? [])
        ->filter(fn($o) => !empty($o['values']) && is_array($o['values']))
        ->values();
    $hasVariants = $product->variants_enabled && $variantOptions->isNotEmpty();
@endphp

@if ($hasVariants)
<div
    x-data="{
        showModal: false,
        loading: false,
        qty: 1,
        selectedOptions: {},
        variantData: @js($product->variant_data ?? (object)[]),
        variantOptions: @js($variantOptions),
        defaultPrice: @js((float)($product->sale_price ?: $product->price)),
        defaultStock: @js((int)$product->stock),
        productImage: @js($product->image ? asset('storage/' . $product->image) : ''),
        productName: @js($product->name),
        productId: @js($product->id),
        selectOption(name, value) {
            this.selectedOptions[name] = value;
            this.updateImage();
        },
        isSelected(name, value) {
            return this.selectedOptions[name] === value;
        },
        getValueImage(optionIndex, value) {
            for (const key in this.variantData) {
                const parts = key.split(' / ');
                if (parts[optionIndex] === value && this.variantData[key]?.image) {
                    return this.variantData[key].image;
                }
            }
            return null;
        },
        selectedVariantName() {
            const names = this.variantOptions.map(o => this.selectedOptions[o.name]).filter(Boolean);
            return names.length === this.variantOptions.length ? names.join(' / ') : '';
        },
        selectedVariant() {
            const name = this.selectedVariantName();
            return name ? (this.variantData[name] || null) : null;
        },
        activePrice() {
            const v = this.selectedVariant();
            return v && v.price ? v.price : this.defaultPrice;
        },
        activeStock() {
            const v = this.selectedVariant();
            return v && v.stock != null ? parseInt(v.stock) : this.defaultStock;
        },
        activeImage() {
            const v = this.selectedVariant();
            if (v && v.image) return v.image;
            return this.productImage;
        },
        updateImage() {
            const img = this.activeImage();
            if (img) {
                const el = this.$refs.modalImage;
                if (el) el.src = img;
            }
        },
        canAddToCart() {
            return this.selectedVariantName() !== '' && this.activeStock() > 0;
        },
        addToCart() {
            if (!this.canAddToCart()) return;
            this.loading = true;
            fetch('{{ route('cart.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: this.productId,
                    quantity: this.qty,
                    variant: this.selectedVariantName()
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const imgEl = this.$refs.modalImage;
                    if (imgEl) flyToCart(imgEl, imgEl.src);
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.productName + ' added to cart!', type: 'success' } }));
                    this.showModal = false;
                    this.resetSelection();
                }
            })
            .finally(() => { this.loading = false; });
        },
        buyNow() {
            if (!this.canAddToCart()) return;
            this.loading = true;
            fetch('{{ route('cart.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: this.productId,
                    quantity: this.qty,
                    variant: this.selectedVariantName()
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route('checkout.loading') }}';
                }
            })
            .finally(() => { this.loading = false; });
        },
        resetSelection() {
            this.selectedOptions = {};
            this.qty = 1;
        },
        openModal() {
            this.resetSelection();
            this.showModal = true;
        }
    }"
    x-on:open-variant-modal-{{ $product->id }}.window="openModal()"
    x-on:keydown.escape.window="showModal = false"
>
    {{-- Backdrop --}}
    <div
        x-show="showModal"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 z-50"
        x-on:click="showModal = false"
        style="display: none;"
    ></div>

    {{-- Modal --}}
    <div
        x-show="showModal"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" x-on:click.stop>
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Select Options</h3>
                <button x-on:click="showModal = false" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="px-5 py-5 max-h-[60vh] overflow-y-auto">
                {{-- Product Info --}}
                <div class="flex gap-4 mb-5">
                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                        <img x-ref="modalImage" :src="activeImage()" class="w-full h-full object-cover" alt="">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 line-clamp-2" x-text="productName"></h4>
                        <div class="mt-1">
                            <span class="text-xl font-bold text-[#5c00c6]" x-text="'৳' + parseFloat(activePrice()).toFixed(2)"></span>
                        </div>
                        <div class="mt-1">
                            <span x-show="activeStock() > 0" class="text-sm text-green-600 font-medium">In Stock (<span x-text="activeStock()"></span> available)</span>
                            <span x-show="activeStock() <= 0" class="text-sm text-red-500 font-medium">Out of Stock</span>
                        </div>
                    </div>
                </div>

                {{-- Variant Options --}}
                <div class="space-y-4">
                    <template x-for="(option, oi) in variantOptions" :key="oi">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" x-text="option.name"></label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(value, vi) in option.values" :key="vi">
                                    <button type="button"
                                            x-on:click="selectOption(option.name, value)"
                                            :class="oi === 0 && getValueImage(oi, value)
                                                ? 'flex flex-col items-center gap-1.5 transition-all duration-200'
                                                : 'px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 ' + (isSelected(option.name, value) ? 'bg-[#5c00c6] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200')">
                                        <template x-if="oi === 0 && getValueImage(oi, value)">
                                            <span class="flex flex-col items-center gap-1.5">
                                                <span class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 transition-all duration-200"
                                                      :class="isSelected(option.name, value) ? 'ring-2 ring-[#5c00c6] ring-offset-2' : 'ring-1 ring-gray-200'">
                                                    <img :src="getValueImage(oi, value)" class="w-full h-full object-cover" />
                                                </span>
                                                <span class="text-xs font-medium transition-colors duration-200"
                                                      :class="isSelected(option.name, value) ? 'text-[#5c00c6]' : 'text-gray-600'"
                                                      x-text="value"></span>
                                            </span>
                                        </template>
                                        <template x-if="!(oi === 0 && getValueImage(oi, value))">
                                            <span x-text="value"></span>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Quantity --}}
                <div class="mt-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                    <div class="flex items-center gap-3">
                        <button type="button" x-on:click="qty = Math.max(1, qty - 1)" class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <input type="number" x-model.number="qty" min="1" :max="activeStock()" class="w-16 text-center border border-gray-200 rounded-lg py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#5c00c6]">
                        <button type="button" x-on:click="qty = Math.min(activeStock(), qty + 1)" class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex gap-3">
                <button
                    x-on:click="addToCart()"
                    :disabled="!canAddToCart() || loading"
                    class="flex-1 py-3 rounded-xl font-semibold text-sm transition-colors bg-gray-900 text-white hover:bg-gray-800 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span x-text="loading ? 'Adding...' : 'Add to Cart'"></span>
                </button>
                <button
                    x-on:click="buyNow()"
                    :disabled="!canAddToCart() || loading"
                    class="flex-1 py-3 rounded-xl font-semibold text-sm transition-colors bg-emerald-600 text-white hover:bg-emerald-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span x-text="loading ? 'Processing...' : 'Buy Now'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
