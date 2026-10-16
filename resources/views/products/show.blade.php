@php
    $galleryImages = $product->gallery_images ?: [];
    $avgRating = $product->reviews ? $product->reviews->avg('rating') : 0;
    $rating = $avgRating ? round($avgRating) : 0;
    $reviewCount = $product->reviews ? $product->reviews->count() : 0;
    $discount = ($product->sale_price && $product->price > 0) ? round((1 - $product->sale_price / $product->price) * 100) : 0;
    $flashWhole = $flashProductIndex[$product->id] ?? $flashProductIndex[$product->id.'|'] ?? null;
    $flashVariants = collect($flashProductIndex)
        ->filter(fn ($v, $k) => $v->variant && str_starts_with($k, $product->id.'|'))
        ->values();
    $hasFlash = $flashWhole || $flashVariants->isNotEmpty();
    $flashSaleForCountdown = $flashWhole?->flashSale ?? $flashVariants->first()?->flashSale;
@endphp

<x-app-layout>
    <div class="flex-grow relative z-0 pb-20 md:pb-0">
        <div class="container mx-auto px-4 py-8">
            {{-- Breadcrumb --}}
            <div class="mb-4 text-sm text-gray-600 flex flex-wrap items-center gap-x-2 gap-y-1">
                <a href="{{ route('products.index') }}" class="hover:text-[var(--color-accent)]">Products</a>
                <span>/</span>
                <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}" class="hover:text-[var(--color-accent)]">{{ $product->category?->name }}</a>
                <span>/</span>
                <span class="text-gray-900">{{ $product->name }}</span>
            </div>

            {{-- Main Content: Image + Info --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                {{-- Image Gallery --}}
                <div>
                    @php
                        $allImages = collect([$product->image])
                            ->filter()
                            ->merge($galleryImages)
                            ->values();
                    @endphp
                    <div x-data="{ activeImage: '{{ $allImages->first() ?? '' }}' }"
                         @variant-image.window="activeImage = $event.detail">
                        <div class="relative aspect-square bg-gray-100 rounded-xl overflow-hidden mb-3 flex items-center justify-center">
                            <template x-if="activeImage">
                                <img :src="'{{ asset('storage') }}/' + activeImage" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            </template>
                            <template x-if="!activeImage">
                                <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                                    <span class="text-sm">No image</span>
                                </div>
                            </template>
                        </div>

                        @if ($allImages->count() > 1)
                            <div class="overflow-x-auto pb-1">
                                <div class="flex gap-2 min-w-max">
                                    @foreach ($allImages as $img)
                                        <button @click="activeImage = '{{ $img }}'"
                                                class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden border-2 transition"
                                                :class="activeImage === '{{ $img }}' ? 'border-[var(--color-accent)]' : 'border-transparent hover:border-gray-300'">
                                            <img src="{{ asset('storage/' . $img) }}" alt=""
                                                 class="w-full h-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Product Info --}}
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-extrabold mb-3 text-gray-900 tracking-tight leading-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mb-5 text-sm text-gray-600">
                        @if ($product->sku)
                            <span><span class="font-semibold text-gray-700">SKU:</span> {{ $product->sku }}</span>
                        @endif
                        <span><span class="font-semibold text-gray-700">Category:</span>
                            @if ($product->category)
                                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                                   class="text-[var(--color-accent)] hover:underline">{{ $product->category->name }}</a>
                            @else
                                <span class="text-gray-500">N/A</span>
                            @endif
                        </span>
                        @if ($product->brand)
                            <span><span class="font-semibold text-gray-700">Brand:</span>
                                <a href="{{ route('products.index', ['brand' => $product->brand->slug]) }}"
                                   class="text-[var(--color-accent)] hover:underline">{{ $product->brand->name }}</a>
                            </span>
                        @endif
                    </div>

                    {{-- Rating --}}
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if ($reviewCount > 0)
                            <span class="text-gray-600 text-sm">{{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }}</span>
                        @endif
                    </div>

                    @php
                        $variantOptions = collect($product->variant_options ?? [])
                            ->filter(fn($o) => !empty($o['values']) && is_array($o['values']))
                            ->values();
                    @endphp
                    <div x-data="{
                        qty: 1,
                        loading: false,
                        customFieldValues: {},
                        variantsEnabled: @js($product->variants_enabled),
                        variantOptions: @js($variantOptions),
                        variantData: @js($product->variant_data ?? (object)[]),
                        defaultPrice: @js((float)($product->sale_price ?: $product->price)),
                        defaultStock: @js((int)$product->stock),
                        showStockCount: @js((bool) $product->show_stock),
                        flashWhole: @js($flashWhole ? ['flash_price' => (float) $flashWhole->flash_price, 'regular_price' => (float) $flashWhole->regular_price, 'sale_stock' => $flashWhole->sale_stock, 'sold' => (int) $flashWhole->sold, 'max_per_customer' => $flashWhole->max_per_customer, 'discount_type' => $flashWhole->discount_type, 'discount_value' => (float) $flashWhole->discount_value] : null),
                        flashVariants: @js($flashVariants->map(fn ($v) => [
                            'variant' => $v->variant,
                            'flash_price' => (float) $v->flash_price,
                            'regular_price' => (float) $v->regular_price,
                            'sale_stock' => $v->sale_stock,
                            'sold' => (int) $v->sold,
                            'max_per_customer' => $v->max_per_customer,
                            'discount_type' => $v->discount_type,
                            'discount_value' => (float) $v->discount_value,
                        ])->all()),
                        selectedOptions: {},
                        init() {
                            this.$watch('qty', (val) => {
                                if (val === '' || val === null || isNaN(val)) { this.qty = 1; return; }
                                if (val < 1) this.qty = 1;
                                else if (val > this.activeStock()) this.qty = this.activeStock();
                            });
                        },
                        selectOption(name, value) {
                            this.selectedOptions[name] = value;
                            const variantName = this.selectedVariantName();
                            if (variantName) {
                                const v = this.variantData[variantName];
                                if (v) {
                                    const img = this.extractVariantImage(v);
                                    if (img) {
                                        window.dispatchEvent(new CustomEvent('variant-image', { detail: img }));
                                    }
                                }
                            }
                        },
                        isSelected(name, value) {
                            return this.selectedOptions[name] === value;
                        },
                        selectedVariantName() {
                            const names = this.variantOptions.map(o => this.selectedOptions[o.name]).filter(Boolean);
                            return names.length === this.variantOptions.length ? names.join(' / ') : '';
                        },
                        extractVariantImage(variant) {
                            if (!variant?.image) return null;
                            const match = variant.image.match(/\/storage\/(.+)/);
                            if (match) return match[1];
                            if (!variant.image.startsWith('http')) return variant.image;
                            return null;
                        },
                        getValueImage(optionIndex, value) {
                            for (const key in this.variantData) {
                                const parts = key.split(' / ');
                                if (parts[optionIndex] === value && this.variantData[key]?.image) {
                                    const img = this.variantData[key].image;
                                    const match = img.match(/\/storage\/(.+)/);
                                    return match ? '/storage/' + match[1] : null;
                                }
                            }
                            return null;
                        },
                        selectedVariant() {
                            const name = this.selectedVariantName();
                            return name ? (this.variantData[name] || null) : null;
                        },
                        selectedFlashVariant() {
                            const name = this.selectedVariantName();
                            return name ? (this.flashVariants.find(f => f.variant === name) || null) : null;
                        },
                        applyFlashPrice(price, f) {
                            if (!f) return price;
                            if (f.discount_type === 'percentage') return price * (1 - f.discount_value / 100);
                            if (f.discount_type === 'fixed_amount') return price - f.discount_value;
                            return f.flash_price;
                        },
                        activePrice() {
                            const v = this.selectedVariant();
                            const base = v && v.price ? v.price : this.defaultPrice;
                            const fv = this.selectedFlashVariant();
                            if (fv) return Math.max(0, this.applyFlashPrice(base, fv));
                            if (this.flashWhole) return Math.max(0, this.applyFlashPrice(base, this.flashWhole));
                            return base;
                        },
                        activeStock() {
                            const v = this.selectedVariant();
                            const base = v && v.stock != null ? parseInt(v.stock) : this.defaultStock;
                            const fv = this.selectedFlashVariant();
                            const f = fv || this.flashWhole;
                            if (f && f.sale_stock != null) return Math.max(0, f.sale_stock - f.sold);
                            return base;
                        },
                        buildCartFormData() {
                            const fd = new FormData();
                            fd.append('product_id', {{ $product->id }});
                            fd.append('quantity', this.qty);
                            if (this.variantsEnabled && this.selectedVariantName()) {
                                fd.append('variant', this.selectedVariantName());
                            }
                            @foreach($product->custom_fields ?? [] as $idx => $field)
                                @if(($field['type'] ?? 'text') === 'file')
                                    if (this.customFieldValues[{{ $idx }}]) {
                                        fd.append('custom_fields[{{ $idx }}][value]', this.customFieldValues[{{ $idx }}]);
                                        fd.append('custom_fields[{{ $idx }}][label]', @js($field['label']));
                                    }
                                @else
                                    if (this.customFieldValues[{{ $idx }}]) {
                                        fd.append('custom_fields[{{ $idx }}][value]', this.customFieldValues[{{ $idx }}]);
                                        fd.append('custom_fields[{{ $idx }}][label]', @js($field['label']));
                                    }
                                @endif
                            @endforeach
                            return fd;
                        }
                    }">

                        {{-- Flash sale banner --}}
                        @if ($hasFlash)
                            <div class="mb-6 rounded-xl border-2 border-violet-400 bg-violet-50 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-violet-500" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22l8.5-11.5H12L13 2z"/></svg>
                                        <span class="font-bold text-violet-700">FLASH SALE</span>
                                    </div>
                                    @if ($flashSaleForCountdown?->ends_at)
                                        <x-flash-countdown :ends-at="$flashSaleForCountdown->ends_at" />
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-violet-800">
                                    Limited time offer on this product
                                    @if ($flashWhole || $flashVariants->isNotEmpty())
                                        {{-- Per-variant stock note --}}
                                    @endif
                                    — grab it before it's gone!
                                </p>
                            </div>
                        @endif

                        {{-- Pricing --}}
                        <div class="flex items-center gap-4 mb-6">
                            <template x-if="!variantsEnabled || !selectedVariantName()">
                                <div class="flex items-center gap-4">
                                    @if ($flashWhole && !$product->variants_enabled)
                                        <span class="text-4xl font-bold text-violet-600">{{ $currencySymbol }}{{ number_format($flashWhole->flash_price, 2) }}</span>
                                        <span class="text-2xl text-gray-500 line-through">{{ $currencySymbol }}{{ number_format($flashWhole->regular_price, 2) }}</span>
                                        @php $flashPct = $flashWhole->regular_price > 0 ? round((1 - $flashWhole->flash_price / $flashWhole->regular_price) * 100) : 0; @endphp
                                        <span class="bg-violet-500 text-white px-3 py-1 rounded-full font-bold text-sm">-{{ $flashPct }}%</span>
                                    @elseif ($product->sale_price)
                                        <span class="text-4xl font-bold text-[var(--color-accent)]">{{ $currencySymbol }}{{ number_format($product->sale_price, 2) }}</span>
                                        <span class="text-2xl text-gray-500 line-through">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                                        @if ($discount > 0)
                                            <span class="bg-red-500 text-white px-3 py-1 rounded-full font-bold text-sm">-{{ $discount }}% discount</span>
                                        @endif
                                    @else
                                        <span class="text-4xl font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                            </template>
                            <template x-if="variantsEnabled && selectedVariantName()">
                                <div class="flex items-center gap-4">
                                    <span class="text-4xl font-bold text-[var(--color-accent)]" x-text="'$' + parseFloat(activePrice()).toFixed(2)"></span>
                                    <span class="text-sm" x-text="selectedVariant()?.sku ? 'SKU: ' + selectedVariant().sku : ''"></span>
                                </div>
                            </template>
                        </div>

                    {{-- Short Description --}}
                    @if ($product->short_description)
                        <div class="prose-description text-gray-800 mb-4 leading-relaxed text-base [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1 [&_strong]:font-semibold [&_a]:text-[var(--color-accent)] [&_a]:underline">
                            {!! \App\Helpers\QuillCleaner::clean($product->short_description) !!}
                        </div>
                    @elseif ($product->description)
                        <p class="text-gray-800 mb-4 leading-relaxed text-base">{{ Str::limit($product->description, 200) }}</p>
                    @endif

                        {{-- Variant Options --}}
                        <template x-if="variantsEnabled && variantOptions.length > 0">
                            <div class="mb-6 space-y-5">
                                <template x-for="(option, oi) in variantOptions" :key="oi">
                                    <div>
                                        <label class="block font-semibold text-gray-900 mb-2" x-text="option.name"></label>
                                        <div class="flex flex-wrap gap-3">
                                            <template x-for="(value, vi) in option.values" :key="vi">
                                                <button type="button"
                                                        @click="selectOption(option.name, value)"
                                                        :class="oi === 0 && getValueImage(oi, value)
                                                            ? 'flex flex-col items-center gap-1.5 group transition-all duration-200'
                                                            : 'px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 ' + (isSelected(option.name, value) ? 'bg-[var(--color-accent)] text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-300 hover:border-gray-400 hover:bg-gray-50')">
                                                    <template x-if="oi === 0 && getValueImage(oi, value)">
                                                        <span class="flex flex-col items-center gap-1.5">
                                                            <span class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 transition-all duration-200"
                                                                  :class="isSelected(option.name, value) ? 'ring-2 ring-[var(--color-accent)] ring-offset-2' : 'ring-1 ring-gray-300 group-hover:ring-gray-400'">
                                                                <img :src="getValueImage(oi, value)" class="w-full h-full object-cover" />
                                                            </span>
                                                            <span class="text-xs font-medium transition-colors duration-200"
                                                                  :class="isSelected(option.name, value) ? 'text-[var(--color-accent)]' : 'text-gray-600 group-hover:text-gray-900'"
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
                        </template>

                        {{-- Custom Fields --}}
                        @if($product->custom_fields_enabled && count($product->custom_fields ?? []) > 0)
                            <div class="mb-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($product->custom_fields as $field)
                                    <div>
                                        <label class="block font-semibold text-gray-900 mb-1">{{ $field['label'] }}</label>
                                        @if(($field['type'] ?? 'text') === 'file')
                                            <input type="file" name="custom_fields[{{ $loop->index }}][value]"
                                                   @change="customFieldValues[{{ $loop->index }}] = $event.target.files[0] || null"
                                                   class="w-full border-2 border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[var(--color-accent)] focus:ring-0 transition file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[var(--color-accent)] file:text-white hover:file:opacity-80">
                                        @else
                                            <input type="text" name="custom_fields[{{ $loop->index }}][value]"
                                                   x-model="customFieldValues[{{ $loop->index }}]"
                                                   placeholder="Enter {{ $field['label'] }}"
                                                   class="w-full border-2 border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[var(--color-accent)] focus:ring-0 transition">
                                        @endif
                                        <input type="hidden" name="custom_fields[{{ $loop->index }}][label]" value="{{ $field['label'] }}">
                                        @if(!empty($field['file']))
                                            <p class="text-xs text-gray-400 mt-1">Reference: {{ $field['file'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Tiered Pricing: Buy More Save More --}}
                        @if($product->tierPrices->isNotEmpty())
                            <div class="mb-6 rounded-xl border border-[#E5E7EB] bg-white p-5 shadow-sm"
                                 x-data="{
                                     tierData: @js($product->tierPrices->map(fn($t) => ['id' => $t->id, 'min' => $t->minimum_quantity, 'max' => $t->maximum_quantity, 'type' => $t->discount_type, 'value' => (float) $t->discount_value, 'price' => (float) $t->fixed_price])->toArray()),
                                     getUnitPrice() {
                                         const price = this.activePrice();
                                         for (const tier of this.tierData) {
                                             if (this.qty >= tier.min && (tier.max === null || this.qty <= tier.max)) {
                                                 switch (tier.type) {
                                                     case 'percentage': return Math.max(0, price - (price * tier.value / 100));
                                                     case 'fixed_amount': return Math.max(0, price - tier.value);
                                                     case 'fixed_price': return tier.value;
                                                 }
                                             }
                                         }
                                         return price;
                                     },
                                     isActiveTier(tier) {
                                         return this.qty >= tier.min && (tier.max === null || this.qty <= tier.max);
                                     },
                                     formatDiscount(tier) {
                                         if (tier.type === 'percentage') return '-' + tier.value + '%';
                                         if (tier.type === 'fixed_amount') return '-$' + tier.value.toFixed(2);
                                         return tier.price.toFixed(2) + ' ea';
                                     },
                                     tierSummary() {
                                         const price = this.getUnitPrice();
                                         const total = price * this.qty;
                                         const origTotal = this.activePrice() * this.qty;
                                         const savings = origTotal - total;
                                         return { price, total, savings, hasDiscount: savings > 0 };
                                     }
                                 }">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Buy more save more
                                </h3>
                                <div class="rounded-lg border border-gray-200 overflow-hidden">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-200">
                                                <th class="text-left px-4 py-2.5 font-medium text-gray-600">Qty</th>
                                                <th class="text-left px-4 py-2.5 font-medium text-gray-600">Discount</th>
                                                <th class="text-right px-4 py-2.5 font-medium text-gray-600">Price per unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(tier, ti) in tierData" :key="ti">
                                                <tr class="border-b border-gray-100 last:border-b-0 transition-all duration-200"
                                                    :class="isActiveTier(tier) ? 'bg-[#D8B4FE] text-white' : 'bg-white'">
                                                    <td class="px-4 py-2.5" :class="isActiveTier(tier) ? 'text-white' : 'text-gray-700'">
                                                        <span x-text="tier.min + (tier.max ? '-' + tier.max : '+')"></span>
                                                    </td>
                                                    <td class="px-4 py-2.5" :class="isActiveTier(tier) ? 'text-white' : 'text-gray-700'">
                                                        <span x-text="formatDiscount(tier)"></span>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-right font-semibold"
                                                        :class="isActiveTier(tier) ? 'text-white' : 'text-gray-900'"
                                                        x-text="'$' + tier.price.toFixed(2)"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Summary Box --}}
                                <div class="mt-4 bg-gray-50 rounded-lg p-4 border border-gray-200"
                                     x-show="qty > 1">
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600" x-text="qty + ' × {{ addslashes($product->name) }}'"></span>
                                            <span class="text-gray-900 font-medium" x-text="'$' + getUnitPrice().toFixed(2) + ' each'"></span>
                                        </div>
                                        <div class="flex justify-between text-sm" x-show="tierSummary().hasDiscount">
                                            <span class="text-green-600 font-medium">You save</span>
                                            <span class="text-green-600 font-medium" x-text="'-$' + tierSummary().savings.toFixed(2)"></span>
                                        </div>
                                        <div class="border-t border-gray-200 pt-2 flex justify-between">
                                            <span class="font-semibold text-gray-900">Total</span>
                                            <span class="font-bold text-lg text-gray-900" x-text="'$' + tierSummary().total.toFixed(2)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Quantity Selector --}}
                        <div class="mb-6">
                            <label class="block font-semibold text-gray-900 mb-2">Quantity</label>
                            <div class="flex items-center gap-4">
                                <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-10 h-10 border-2 border-gray-300 rounded-lg hover:bg-gray-100 flex items-center justify-center text-lg font-semibold text-gray-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <input type="number" min="1" :max="activeStock()" x-model.number="qty"
                                       class="w-16 h-10 text-center text-xl font-semibold border-2 border-gray-300 rounded-lg focus:border-[var(--color-accent)] focus:ring-0 focus:outline-none transition">
                                <button type="button" @click="qty = Math.min(qty + 1, activeStock())" class="w-10 h-10 border-2 border-gray-300 rounded-lg hover:bg-gray-100 flex items-center justify-center text-lg font-semibold text-gray-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Stock Status --}}
                        <div class="text-sm mb-6">
                            <template x-if="!variantsEnabled || !selectedVariantName()">
                                @if ($product->isInStock())
                                    <span class="text-green-600 font-semibold">✓ In stock @if($product->show_stock && $product->type !== 'digital')({{ $product->stock }} available)@endif</span>
                                @else
                                    <span class="text-red-600 font-semibold">✗ Out of stock</span>
                                @endif
                            </template>
                            <template x-if="variantsEnabled && selectedVariantName()">
                                <div>
                                    <span x-show="activeStock() > 0" class="text-green-600 font-semibold" x-text="showStockCount ? '✓ In stock (' + activeStock() + ' available)' : '✓ In stock'"></span>
                                    <span x-show="selectedVariant()?.stock != null && activeStock() <= 0" class="text-red-600 font-semibold">✗ Out of stock</span>
                                </div>
                            </template>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="product-card-actions">
                            <template x-if="variantsEnabled && variantOptions.length > 0 && selectedVariantName() && activeStock() > 0">
                                <div class="product-card-actions">
                                    <button type="button"
                                            @click="
                                                flyToCart($el, @js($product->image ? asset('storage/' . $product->image) : ''));
                                                loading = true;
                                                const fd = buildCartFormData();
                                                fd.append('_token', document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'));
                                                fetch('{{ route('cart.store') }}', {
                                                    method: 'POST',
                                                    headers: { 'Accept': 'application/json' },
                                                    body: fd
                                                })
                                                .then(r => r.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                                                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ addslashes($product->name) }} added to cart!', type: 'success' } }));
                                                    } else if (data.errors) {
                                                        alert(Object.values(data.errors).flat().join('\\n'));
                                                    }
                                                })
                                                .catch(() => alert('Something went wrong. Please try again.'))
                                                .finally(() => { loading = false; })
                                            "
                                            :disabled="loading"
                                            class="btn-add-cart">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                        </svg>
                                        <span x-text="loading ? 'Adding...' : 'Add to Cart'"></span>
                                    </button>

                                    <form action="{{ route('cart.store') }}" method="POST" @submit.prevent="
                                        const fd = buildCartFormData();
                                        fd.append('_redirect', 'checkout');
                                        fd.append('_token', document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'));
                                        loading = true;
                                        fetch('{{ route('cart.store') }}', {
                                            method: 'POST',
                                            headers: { 'Accept': 'application/json' },
                                            body: fd
                                        }).then(r => r.json()).then(data => {
                                            loading = false;
                                            if (data.success) {
                                                window.location.href = '{{ route('checkout.loading') }}';
                                            } else if (data.errors) {
                                                alert(Object.values(data.errors).flat().join('\\n'));
                                            }
                                        }).catch(err => {
                                            loading = false;
                                            alert('Something went wrong. Please try again.');
                                        });
                                    ">
                                        <button type="submit" class="btn-buy-now" :disabled="loading">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                            </svg>
                                            <span x-text="loading ? 'Processing...' : 'Buy Now'"></span>
                                        </button>
                                    </form>
                                </div>
                            </template>
                            <template x-if="variantsEnabled && variantOptions.length > 0 && (!selectedVariantName() || (selectedVariant()?.stock != null && activeStock() <= 0))">
                                <div class="w-full">
                                    <span x-show="!selectedVariantName()" class="block w-full text-center py-3 rounded-xl font-bold text-sm bg-gray-200 text-gray-500 cursor-not-allowed">Select all options</span>
                                    <span x-show="selectedVariantName() && selectedVariant()?.stock != null && activeStock() <= 0" class="block w-full text-center py-3 rounded-xl font-bold text-sm bg-gray-200 text-gray-500 cursor-not-allowed">Out of Stock</span>
                                </div>
                            </template>
                            <template x-if="!variantsEnabled || !variantOptions.length">
                                <div class="product-card-actions">
                                    @if ($product->isInStock())
                                        <button type="button"
                                                @click="
                                                    flyToCart($el, @js($product->image ? asset('storage/' . $product->image) : ''));
                                                    loading = true;
                                                    const fd = buildCartFormData();
                                                    fd.append('_token', document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'));
                                                    fetch('{{ route('cart.store') }}', {
                                                        method: 'POST',
                                                        headers: { 'Accept': 'application/json' },
                                                        body: fd
                                                    })
                                                    .then(r => r.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                                                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ addslashes($product->name) }} added to cart!', type: 'success' } }));
                                                        } else if (data.errors) {
                                                            alert(Object.values(data.errors).flat().join('\\n'));
                                                        }
                                                    })
                                                    .catch(() => alert('Something went wrong. Please try again.'))
                                                    .finally(() => { loading = false; })
                                                "
                                                :disabled="loading"
                                                class="btn-add-cart">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                            </svg>
                                            <span x-text="loading ? 'Adding...' : 'Add to Cart'"></span>
                                        </button>

                                        <form action="{{ route('cart.store') }}" method="POST" @submit.prevent="
                                            const fd = buildCartFormData();
                                            fd.append('_redirect', 'checkout');
                                            fd.append('_token', document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'));
                                            loading = true;
                                            fetch('{{ route('cart.store') }}', {
                                                method: 'POST',
                                                headers: { 'Accept': 'application/json' },
                                                body: fd
                                            }).then(r => r.json()).then(data => {
                                                loading = false;
                                                if (data.success) {
                                                    window.location.href = '{{ route('checkout.loading') }}';
                                                } else if (data.errors) {
                                                    alert(Object.values(data.errors).flat().join('\\n'));
                                                }
                                            }).catch(err => {
                                                loading = false;
                                                alert('Something went wrong. Please try again.');
                                            });
                                        ">
                                            <button type="submit" class="btn-buy-now" :disabled="loading">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                                </svg>
                                                <span x-text="loading ? 'Processing...' : 'Buy Now'"></span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="w-full text-center py-3 rounded-xl font-bold text-sm bg-gray-200 text-gray-500 cursor-not-allowed">Out of Stock</span>
                                    @endif
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description & Specification Tabs --}}
            @php
                $hasDescription = $product->description || $product->long_description;
                $hasAttributes = $product->attributeOptions->isNotEmpty();
            @endphp
            @if ($hasDescription || $hasAttributes)
                @php
                    $groupedAttributes = $product->attributeOptions
                        ->groupBy(fn($o) => $o->attribute->name)
                        ->map(fn($options) => $options->pluck('display_name')->implode(', '));
                @endphp
                <div class="mb-12">
                    <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
                        <div x-data="{ tab: 'description' }">
                            <div class="flex border-b border-gray-200">
                                @if ($hasDescription)
                                    <button @click="tab = 'description'"
                                            :class="tab === 'description' ? 'border-b-2 border-[var(--color-accent)] text-[var(--color-accent)]' : 'text-gray-500 hover:text-gray-700'"
                                            class="px-6 py-4 font-semibold text-base transition border-b-2 border-transparent">
                                        Description
                                    </button>
                                @endif
                                @if ($hasAttributes)
                                    <button @click="tab = 'specification'"
                                            :class="tab === 'specification' ? 'border-b-2 border-[var(--color-accent)] text-[var(--color-accent)]' : 'text-gray-500 hover:text-gray-700'"
                                            class="px-6 py-4 font-semibold text-base transition border-b-2 border-transparent">
                                        Specification
                                    </button>
                                @endif
                            </div>
                            <div class="p-6 lg:p-8">
                                @if ($hasDescription)
                                    <div x-show="tab === 'description'">
                                        <div class="prose-description text-gray-800 leading-relaxed text-base md:text-lg [&_p]:mb-4 [&_p]:leading-relaxed [&_h1]:text-2xl [&_h2]:text-xl [&_h3]:text-lg [&_h1,h2,h3,h4]:font-bold [&_h1,h2,h3,h4]:text-gray-900 [&_h1,h2,h3,h4]:mb-3 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_li]:mb-2 [&_strong]:font-semibold [&_strong]:text-gray-900 [&_a]:text-[var(--color-accent)] [&_a]:underline [&_img]:rounded-lg [&_img]:my-4 [&_blockquote]:border-l-4 [&_blockquote]:border-[var(--color-accent)] [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-gray-600">
                                            {!! \App\Helpers\QuillCleaner::clean($product->long_description ?: $product->description) !!}
                                        </div>
                                        @if ($product->tags->isNotEmpty())
                                            <div class="flex flex-wrap gap-2 mt-6 pt-6 border-t border-gray-200">
                                                @foreach ($product->tags as $tag)
                                                    <a href="{{ route('products.index', ['tag' => $tag->slug]) }}"
                                                       class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-full hover:bg-[var(--color-accent)] hover:text-white transition-colors">
                                                        {{ $tag->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if ($hasAttributes)
                                    <div x-show="tab === 'specification'">
                                        <table class="w-full">
                                            <tbody>
                                                @foreach ($groupedAttributes as $name => $values)
                                                    <tr class="border-b border-gray-100 last:border-0">
                                                        <td class="py-3 pr-4 font-semibold text-gray-900 w-1/4 align-top">{{ $name }}</td>
                                                        <td class="py-3 text-gray-700">{{ $values }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Reviews --}}
            <div class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-6 lg:p-8 border border-gray-100">
                    <div x-data="{ open: false, rating: 0, body: '', submitted: false }">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Customer Reviews</h2>
                            @auth
                                <button type="button"
                                        @click="open = !open"
                                        x-text="open ? 'Close' : 'Write a Review'"
                                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition hover:scale-105 font-semibold text-sm">
                                </button>
                            @endauth
                        </div>

                        {{-- Review Form --}}
                        @auth
                            <div x-show="open"
                                 x-collapse
                                 class="mb-8 p-6 bg-gray-50 rounded-lg border-2 border-gray-200">
                                <h3 class="text-xl font-bold mb-4">Write Your Review</h3>
                                <form @submit.prevent="
                                    fetch('{{ route('reviews.store') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            product_id: {{ $product->id }},
                                            rating: rating,
                                            body: body
                                        })
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.success) {
                                            submitted = true;
                                            body = '';
                                            rating = 0;
                                            open = false;
                                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Review submitted!', type: 'success' } }));
                                        }
                                    })
                                ">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block font-semibold text-gray-700 mb-2">Rating</label>
                                            <div class="flex gap-2">
                                                <template x-for="star in 5" :key="star">
                                                    <button type="button" @click="rating = star"
                                                            class="text-3xl transition"
                                                            :class="star <= rating ? 'text-yellow-400 hover:scale-110' : 'text-gray-300 hover:text-yellow-300'">
                                                        ★
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-gray-700 mb-2">Your Review</label>
                                            <textarea x-model="body" rows="4"
                                                      placeholder="Write your review..."
                                                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                                        </div>
                                        <button type="submit"
                                                :disabled="!rating || !body"
                                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition hover:scale-105 font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                            Submit Review
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endauth

                        {{-- Review Cards --}}
                        @if ($product->reviews->isNotEmpty())
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Others reviews</h3>
                            <div class="space-y-6">
                                @foreach ($product->reviews as $review)
                                    <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center font-bold text-indigo-600 text-lg">
                                                    {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900">{{ $review->user?->name ?? 'Anonymous' }}</p>
                                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                                        <div class="flex">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                        <span>{{ $review->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($review->body)
                                            <p class="text-gray-700 leading-relaxed">{{ $review->body }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-600 mb-4">No reviews yet for this product. Be the first to review!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related Products --}}
            @if ($relatedProducts->isNotEmpty())
                <div class="mb-12">
                    <h2 class="text-3xl md:text-4xl font-extrabold mb-6 text-gray-900 tracking-tight">Related Products</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($relatedProducts as $related)
                            <x-product-card :product="$related" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        :root {
            --color-primary: #db1a1a;
            --color-primary-hover: #b31515;
            --color-primary-light: #fff6f6;
            --color-primary-dark: #8f1212;
            --color-secondary: #d24848;
            --color-secondary-hover: #d24848cc;
            --color-secondary-light: #dff1f0;
            --color-secondary-dark: #431717;
            --color-accent: #5c00c6;
            --color-accent-hover: #5c00c68f;
            --color-accent-light: #eef9f8;
            --color-accent-dark: #220147;
            --color-success: #007a00;
            --color-success-hover: #007a00cc;
            --color-success-light: #eaf5f5;
            --color-success-dark: #003d00;
        }

        .product-card-actions {
            display: flex;
            gap: 12px;
            width: 100%;
            margin-top: 16px;
            align-items: stretch;
        }

        .product-card-actions > form {
            flex: 1;
            min-width: 0;
        }

        .btn-add-cart,
        .btn-buy-now {
            flex: 1;
            height: 48px;
            min-height: 48px;
            padding: 0 18px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.25s ease;
            width: 100%;
        }

        .btn-add-cart {
            background: linear-gradient(90deg, #6D0BD1 0%, #4B0082 100%);
            color: #ffffff;
        }

        .btn-add-cart:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(109, 11, 209, 0.25);
            filter: brightness(1.03);
        }

        .btn-add-cart:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-add-cart:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-buy-now {
            background: linear-gradient(90deg, #0A9F00 0%, #006400 100%);
            color: #ffffff;
        }

        .btn-add-cart:hover:not(:disabled),
        .btn-buy-now:hover:not(:disabled) {
            transform: translateY(-1px);
            filter: brightness(1.03);
        }

        .btn-add-cart:hover:not(:disabled) {
            box-shadow: 0 6px 14px rgba(109, 11, 209, 0.25);
        }

        .btn-buy-now:hover:not(:disabled) {
            box-shadow: 0 6px 14px rgba(10, 159, 0, 0.25);
        }

        .btn-add-cart:active:not(:disabled),
        .btn-buy-now:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-add-cart:disabled,
        .btn-buy-now:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-add-cart svg,
        .btn-buy-now svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .btn-add-cart,
            .btn-buy-now {
                height: 44px;
                min-height: 44px;
                border-radius: 10px;
                font-size: 14px;
            }
        }

        .prose-description table {
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .prose-description td,
        .prose-description th {
            border-width: 1px;
            border-style: solid;
            border-color: #e5e7eb;
            padding: 6px 12px;
            vertical-align: middle;
            text-align: left;
        }

        .prose-description td p,
        .prose-description th p {
            margin: 0;
            line-height: 1.4;
        }

        .prose-description th {
            background-color: #f9fafb;
            font-weight: 600;
        }
    </style>
</x-app-layout>