@props(['product', 'flashEndsAt' => null])

@php
    $avgRating = $product->reviews ? $product->reviews->avg('rating') : 0;
    $rating = $avgRating ? round($avgRating) : 0;
    $reviewCount = $product->reviews ? $product->reviews->count() : 0;
    $discount = ($product->sale_price && $product->price > 0) ? round((1 - $product->sale_price / $product->price) * 100) : 0;
    $hasVariants = $product->variants_enabled && !empty($product->variant_options) && count($product->variant_options) > 0;
    $filteredVariantOptions = $hasVariants ? collect($product->variant_options)->filter(fn($o) => !empty($o['values']) && is_array($o['values']))->values()->toArray() : [];
    $galleryImages = $product->gallery_images ? (is_string($product->gallery_images) ? json_decode($product->gallery_images, true) : $product->gallery_images) : [];
    $galleryUrls = collect($galleryImages)->map(fn($img) => asset('storage/' . $img))->toArray();
    $flash = $flashProductIndex[$product->id] ?? $flashProductIndex[$product->id.'|'] ?? null;
    $onFlash = $flash && ! $product->variants_enabled;
    $flashRemaining = $flash && $flash->sale_stock ? max(0, (int) $flash->sale_stock - (int) $flash->sold) : null;
    $flashPercent = $flashRemaining !== null && $flash->sale_stock > 0 ? (int) round($flashRemaining / (int) $flash->sale_stock * 100) : null;
@endphp

<div class="group relative bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
    {{-- Image --}}
    <div class="relative overflow-hidden aspect-[5/5] bg-gray-100"
         x-data="{
             hovering: false,
             current: 0,
             prevSrc: '',
             showPrev: false,
             timer: null,
             images: {{ json_encode(array_merge([$product->image ? asset('storage/' . $product->image) : ''], $galleryUrls)) }},
             hasGallery() { return this.images.length > 1; },
             nextIndex() {
                 let idx = (this.current + 1) % this.images.length;
                 return idx === 0 ? 1 : idx;
             },
             cycle() {
                 this.prevSrc = this.images[this.current];
                 this.showPrev = true;
                 this.current = this.nextIndex();
                 setTimeout(() => { this.showPrev = false; }, 500);
             },
             start() {
                 if (!this.hasGallery()) return;
                 this.hovering = true;
                 this.current = 1;
                 this.timer = setInterval(() => this.cycle(), 3000);
             },
             stop() {
                 this.hovering = false;
                 clearInterval(this.timer);
                 this.timer = null;
                 this.showPrev = false;
                 this.current = 0;
             }
         }"
         @mouseenter="start()" @mouseleave="stop()">
        <a href="{{ route('products.show', $product) }}" class="w-full h-full flex items-center justify-center relative z-10">
            @if ($product->image)
                {{-- Current image --}}
                <img :src="images[current]"
                     class="w-full h-full object-cover absolute inset-0 transition-all duration-500 ease-out"
                     :class="hovering ? 'opacity-100 scale-105' : 'opacity-100 scale-100'">
                {{-- Previous image fading out --}}
                <img x-show="showPrev" x-cloak :src="prevSrc"
                     x-transition:enter="transition-opacity duration-500 ease-in-out"
                     x-transition:leave="transition-opacity duration-500 ease-in-out"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="w-full h-full object-cover absolute inset-0">
            @else
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            @endif
        </a>

        {{-- Badges --}}
        <div class="absolute top-2 left-2 flex flex-col gap-1.5 z-10">
            @if ($flashEndsAt)
                <span class="bg-violet-900/90 text-white px-2 py-1 text-[10px] font-semibold rounded flex items-center gap-1 backdrop-blur-sm leading-none" x-data="flashCountdown('{{ \Illuminate\Support\Carbon::parse($flashEndsAt)->toIso8601String() }}')" x-cloak>
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span x-show="done" class="truncate">Ended</span>
                    <template x-if="!done">
                        <span class="tabular-nums truncate"><span x-text="padded.d"></span>d <span x-text="padded.h"></span>:<span x-text="padded.m"></span>:<span x-text="padded.s"></span></span>
                    </template>
                </span>
            @endif
            @if ($onFlash)
                <span class="bg-violet-500 text-white px-2.5 py-1 text-xs font-semibold rounded">
                    FLASH
                </span>
            @elseif ($discount > 0)
                <span class="bg-red-500 text-white px-2.5 py-1 text-xs font-semibold rounded">
                    -{{ $discount }}%
                </span>
            @endif
            @if (! $product->isInStock())
                <span class="bg-gray-600 text-white px-2.5 py-1 rounded text-xs font-medium">
                    Out of Stock
                </span>
            @endif
        </div>

    </div>

    {{-- Info --}}
    <div class="p-4 flex flex-col flex-1">
        <a href="{{ route('products.show', $product) }}">
            <h3 class="font-medium text-gray-900 mb-1.5 line-clamp-2 text-sm leading-tight hover:text-indigo-600 transition-colors" data-product-name>
                {{ $product->name }}
            </h3>
        </a>

        @if ($reviewCount > 0 || $rating > 0)
            <div class="flex items-center gap-1 mb-2">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-3 h-3 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
                @if ($reviewCount > 0)
                    <span class="text-xs text-gray-500">({{ $reviewCount }})</span>
            @endif
        @endif

        <div class="flex items-baseline gap-2 mt-auto pt-2">
            @if ($onFlash)
                <span class="text-lg font-bold text-violet-600">{{ $currencySymbol }}{{ number_format($flash->flash_price, 2) }}</span>
                <span class="text-sm text-gray-400 line-through">{{ $currencySymbol }}{{ number_format($flash->regular_price, 2) }}</span>
            @elseif ($product->sale_price)
                <span class="text-lg font-semibold text-gray-900">{{ $currencySymbol }}{{ number_format($product->sale_price, 2) }}</span>
                <span class="text-sm text-gray-400 line-through">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
            @else
                <span class="text-lg font-semibold text-gray-900">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
            @endif
            <button type="button"
                    x-data="{ liked: false }"
                    @click="liked = !liked"
                    class="ml-auto p-1 text-gray-400 hover:text-red-400 transition-colors">
                <svg x-show="!liked" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <svg x-show="liked" class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>

        @if ($onFlash && $flashRemaining !== null)
            <div class="mt-2">
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-violet-100">
                    <div class="h-full rounded-full bg-violet-500 transition-all" style="width: {{ $flashPercent }}%;"></div>
                </div>
                @if ($product->show_stock)
                    <span class="mt-1 block text-xs font-semibold text-violet-700">{{ $flashRemaining }} left in stock</span>
                @endif
            </div>
        @endif
    </div>

    {{-- Actions --}}
    @if ($product->isInStock())
        <div class="px-4 pb-4 flex gap-2">
            @if ($hasVariants)
                <button type="button"
                        x-data
                        @click="$dispatch('open-variant-modal', { id: {{ $product->id }}, name: @js($product->name), image: @js($product->image ? asset('storage/' . $product->image) : ''), variantData: @js($product->variant_data ?? []), variantOptions: @js($filteredVariantOptions), defaultPrice: @js((float)($product->sale_price ?: $product->price)), defaultStock: @js((int)$product->stock) })"
                        class="flex-1 py-2 rounded font-medium text-sm bg-emerald-600 text-white hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Buy Now
                </button>
                <button type="button"
                        x-data
                        @click="$dispatch('open-variant-modal', { id: {{ $product->id }}, name: @js($product->name), image: @js($product->image ? asset('storage/' . $product->image) : ''), variantData: @js($product->variant_data ?? []), variantOptions: @js($filteredVariantOptions), defaultPrice: @js((float)($product->sale_price ?: $product->price)), defaultStock: @js((int)$product->stock) })"
                        class="flex-shrink-0 py-2 px-3 rounded font-medium text-sm transition-colors bg-gray-900 text-white hover:bg-gray-800 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-1"
                        title="Add to Cart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span>Cart</span>
                </button>
            @else
                <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="_redirect" value="checkout">
                    <button type="submit"
                            class="w-full py-2 rounded font-medium text-sm bg-emerald-600 text-white hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Buy Now
                    </button>
                </form>
                <button type="button"
                        x-data="{ loading: false }"
                        @click="
                            flyToCart($el, @js($product->image ? asset('storage/' . $product->image) : ''));
                            loading = true;
                            fetch('{{ route('cart.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    product_id: {{ $product->id }},
                                    quantity: 1
                                })
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                                    window.dispatchEvent(new CustomEvent('cart-added', { detail: { count: data.cart_count } }));
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ addslashes($product->name) }} added to cart!', type: 'success' } }));
                                }
                            })
                            .finally(() => { loading = false; })
                        "
                        :disabled="loading"
                        class="flex-shrink-0 py-2 px-3 rounded font-medium text-sm transition-colors bg-gray-900 text-white hover:bg-gray-800 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-1"
                        title="Add to Cart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span x-text="loading ? '...' : 'Cart'"></span>
                </button>
            @endif
        </div>
    @else
        <div class="px-4 pb-4">
            <span class="block w-full text-center text-sm text-gray-400 py-2 bg-gray-50 rounded font-medium">Out of Stock</span>
        </div>
    @endif
</div>
