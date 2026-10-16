<x-app-layout>
    {{-- Hero Slider --}}
    @if ($banners->isNotEmpty())
        <div x-data="bannerSlider()" x-init="init()" class="relative w-screen overflow-hidden -mt-6 -mb-6 sm:-mt-8 sm:-mb-8 lg:-mt-10 lg:-mb-10" style="margin-left: calc(-50vw + 50%);" role="region" aria-label="Banner slider">
            <template x-for="(banner, index) in banners" :key="index">
                <div x-show="current === index"
                     class="relative text-white h-[200px] sm:h-[280px] lg:h-[360px]"
                     :style="'background-color: ' + (banner.bg_color || '#1a1a2e')">
                    <a :href="banner.link || '#'" class="block h-full">
                        <img :src="'/storage/' + banner.image"
                             :alt="banner.title"
                             class="absolute inset-0 w-full h-full object-cover">
                    </a>
                </div>
            </template>

            {{-- Dots --}}
            <div x-show="banners.length > 1"
                 class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
                <template x-for="(banner, index) in banners" :key="'dot-' + index">
                    <button @click="goTo(index)"
                            :class="current === index ? 'bg-white w-8' : 'bg-white/40 w-2.5'"
                            class="h-2.5 rounded-full transition-all duration-300 focus:outline-none">
                    </button>
                </template>
            </div>

            {{-- Prev / Next --}}
            <button x-show="banners.length > 1"
                    @click="prev()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-all z-10 backdrop-blur-sm focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button x-show="banners.length > 1"
                    @click="next()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-all z-10 backdrop-blur-sm focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <script>
        function bannerSlider() {
            return {
                banners: @json($bannersJson),
                current: 0,
                interval: null,
                init() {
                    if (this.banners.length > 1) {
                        this.interval = setInterval(() => this.next(), 5000);
                    }
                },
                goTo(index) {
                    this.current = index;
                    this.resetInterval();
                },
                next() {
                    this.current = (this.current + 1) % this.banners.length;
                    this.resetInterval();
                },
                prev() {
                    this.current = (this.current - 1 + this.banners.length) % this.banners.length;
                    this.resetInterval();
                },
                resetInterval() {
                    clearInterval(this.interval);
                    if (this.banners.length > 1) {
                        this.interval = setInterval(() => this.next(), 5000);
                    }
                },
            };
        }
        </script>
    @else
        <div class="relative w-screen overflow-hidden -mt-6 -mb-6 sm:-mt-8 sm:-mb-8 lg:-mt-10 lg:-mb-10 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-900 text-white" style="margin-left: calc(-50vw + 50%);">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-purple-300 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-300 rounded-full blur-3xl"></div>
            </div>
            <div class="relative max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-36">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center gap-1.5 bg-white/15 text-white text-sm font-medium px-4 py-1.5 rounded-full mb-6">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        New arrivals just landed
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold tracking-tight leading-tight mb-6">
                        Discover
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-200 to-purple-200">Premium</span>
                        Products
                    </h1>
                    <p class="text-lg sm:text-xl text-indigo-200 max-w-2xl mb-10 leading-relaxed">
                        Shop the latest trends with curated collections from top brands.
                        Enjoy free shipping on orders over $50 and hassle-free returns.
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold px-8 py-3.5 rounded-xl hover:bg-indigo-50 transition-all shadow-xl shadow-indigo-900/20">
                            Shop Now
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 border border-white/30 text-white font-medium px-8 py-3.5 rounded-xl hover:bg-white/10 transition-all">
                            Browse Collections
                        </a>
                    </div>
                    <div class="flex flex-wrap items-center gap-8 mt-12 pt-8 border-t border-white/15">
                        <div class="flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-400 border-2 border-white/30 flex items-center justify-center text-xs font-bold">JD</div>
                                <div class="w-8 h-8 rounded-full bg-purple-400 border-2 border-white/30 flex items-center justify-center text-xs font-bold">SK</div>
                                <div class="w-8 h-8 rounded-full bg-pink-400 border-2 border-white/30 flex items-center justify-center text-xs font-bold">AL</div>
                            </div>
                            <span class="text-sm text-indigo-200"><span class="font-semibold text-white">{{ number_format($totalReviews + 500) }}</span> happy customers</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-indigo-200">
                            <div class="flex">
                                @for ($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <span><span class="font-semibold text-white">{{ number_format($avgRating ?: 4.8, 1) }}</span> average rating</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-screen-2xl mx-auto pt-12 sm:pt-16 lg:pt-20">
        {{-- Shop by Category --}}
        <section class="mb-16">
            <div class="text-center mb-8">
                <span class="inline-block bg-indigo-50 text-indigo-600 text-xs font-semibold px-3 py-1 rounded-full mb-3">Browse Categories</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Shop by Category</h2>
                <p class="text-gray-500 mt-1">Explore our curated collections</p>
            </div>
            @if ($categories->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                    @foreach ($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                           class="group relative aspect-[4/3] rounded-xl overflow-hidden bg-gray-100">
                            @if ($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent group-hover:from-black/80 transition-all duration-300"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5 text-center">
                                <h3 class="text-white font-semibold text-sm sm:text-base truncate">{{ $category->name }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-xl border border-gray-200">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <p class="text-gray-500 font-medium">No categories yet</p>
                    <p class="text-sm text-gray-400 mt-1">Categories will appear here once added.</p>
                </div>
            @endif
        </section>

        {{-- Flash Sale --}}
        @if ($activeFlashSales->isNotEmpty())
            @php
                $flashProductsForHome = $activeFlashSales
                    ->flatMap(fn ($sale) => $sale->products)
                    ->filter(fn ($fp) => $fp->scope === 'whole' && $fp->product?->status === 'active' && ! $fp->product->variants_enabled)
                    ->keyBy('product_id');
                $homeFlashSale = $activeFlashSales->sortBy('ends_at')->first();
            @endphp
            @if ($flashProductsForHome->isNotEmpty())
                @php
                    $maxDiscount = $flashProductsForHome
                        ->map(fn ($fp) => $fp->regular_price > 0 ? round((1 - $fp->flash_price / $fp->regular_price) * 100) : 0)
                        ->max();
                @endphp
                <section class="mb-16 overflow-hidden rounded-3xl border border-violet-200/70 bg-gradient-to-br from-violet-50 via-purple-50/60 to-white shadow-[0_24px_70px_-20px_rgba(139,92,246,0.45)]">
                    {{-- Header band --}}
                    <div class="relative overflow-hidden bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 px-6 py-8 sm:px-10">
                        <div class="pointer-events-none absolute -right-10 -top-16 h-64 w-64 rounded-full bg-white/15 blur-2xl"></div>
                        <div class="pointer-events-none absolute -bottom-20 -left-10 h-64 w-64 rounded-full bg-black/10 blur-2xl"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>

                        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 shadow-inner ring-1 ring-white/40 backdrop-blur">
                                    <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22l8.5-11.5H12L13 2z"/></svg>
                                </div>
                                <div>
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Flash Sale</h2>
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-white ring-1 ring-white/40">
                                            <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
                                            Live now
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm font-medium text-amber-50/90">Up to <span class="font-bold text-white">{{ $maxDiscount }}% off</span> — prices drop, but not for long!</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-5">
                                @if ($homeFlashSale?->ends_at)
                                    <div>
                                        <p class="mb-1.5 text-center text-xs font-bold uppercase tracking-widest text-white">Ends in</p>
                                        <x-flash-countdown :ends-at="$homeFlashSale->ends_at" />
                                    </div>
                                @endif
                                <a href="{{ route('flash-sale.index') }}"
                                   class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-violet-600 shadow-lg shadow-purple-900/25 transition-all hover:-translate-y-0.5 hover:bg-violet-50 hover:shadow-xl">
                                    Shop the sale
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Products (carousel) --}}
                    <div class="px-4 py-6 sm:px-6 sm:py-8" x-data="flashCarousel({{ $flashProductsForHome->count() }}, 2)" x-init="measure()" @mouseover="hover()" @mouseleave="leave()" @focusin="hover()" @focusout="leave()" @touchstart.passive="hover()" @touchend.passive="leave()">
                        <div class="relative">
                            <button type="button" x-show="canPrev" class="absolute -left-3 top-1/2 -translate-y-1/2 z-10 h-9 w-9 items-center justify-center rounded-full bg-white/90 text-violet-600 shadow-lg ring-1 ring-violet-200 hover:bg-violet-50 transition-all flex" x-cloak @click="prev()" aria-label="Previous flash products">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12l-5-5-5 5M8 12h-12"/></svg>
                            </button>
                            <div x-ref="track" class="flex gap-4 sm:gap-5 overflow-x-hidden scroll-smooth snap-x snap-mandatory pb-1" @scroll.debounce.150ms="onScroll()">
                                @foreach ($flashProductsForHome as $fp)
                                    <div class="min-w-[13rem] w-[13rem] sm:min-w-[15rem] sm:w-[15rem] lg:min-w-[18rem] lg:w-[18rem] snap-start shrink-0">
                                        <x-product-card :product="$fp->product" />
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" x-show="canNext" class="absolute -right-3 top-1/2 -translate-y-1/2 z-10 h-9 w-9 items-center justify-center rounded-full bg-white/90 text-violet-600 shadow-lg ring-1 ring-violet-200 hover:bg-violet-50 transition-all flex" x-cloak @click="next()" aria-label="Next flash products">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"/></svg>
                            </button>
                        </div>
                        <div class="mt-4 flex items-center justify-center gap-2" x-show="total > 1" x-cloak>
                            <template x-for="(dot, i) in pages" :key="i">
                                <button type="button" class="h-2 w-2 rounded-full transition-all" :class="i === page ? 'bg-violet-600 w-5' : 'bg-violet-300 hover:bg-violet-400'" @click="go(i)"></button>
                            </template>
                        </div>
                    </div>
                </section>
            @endif
        @endif

        {{-- Featured Products --}}
        @if ($featuredProducts->isNotEmpty())
            <section class="mb-16">
                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Featured Products</h2>
                    <p class="text-gray-500 mt-1">Handpicked favorites just for you</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach ($featuredProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Latest Products --}}
        <section class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Latest Products</h2>
                    <p class="text-gray-500 mt-1">Fresh arrivals added daily</p>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors hidden sm:inline-flex items-center gap-1">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ($latestProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>

        {{-- Collections --}}
        @if ($collections->isNotEmpty())
            @foreach ($collections as $collection)
                <section class="mb-16">
                    @if ($collection->image)
                        <a href="{{ route('collections.show', $collection) }}" class="block relative w-full aspect-[21/9] rounded-xl overflow-hidden bg-gray-100 mb-6 group">
                            <img src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="absolute bottom-4 sm:bottom-6 left-4 sm:left-6">
                                <h2 class="text-xl sm:text-2xl font-bold text-white">{{ $collection->name }}</h2>
                                @if ($collection->description)
                                    <p class="text-sm text-white/80 mt-1">{{ $collection->description }}</p>
                                @endif
                            </div>
                        </a>
                    @else
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $collection->name }}</h2>
                                @if ($collection->description)
                                    <p class="text-gray-500 mt-1">{{ $collection->description }}</p>
                                @endif
                            </div>
                            <a href="{{ route('collections.show', $collection) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors hidden sm:inline-flex items-center gap-1">
                                View All
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach ($collection->products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    @if ($collection->image)
                        <div class="mt-4 text-right">
                            <a href="{{ route('collections.show', $collection) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors inline-flex items-center gap-1">
                                View All
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </section>
            @endforeach
        @endif

        {{-- Testimonials --}}
        @if ($testimonials->isNotEmpty())
            <section class="mb-16">
                <div class="text-center mb-10">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">What Our Customers Say</h2>
                    <p class="text-gray-500 mt-1">Real reviews from real customers</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($testimonials as $review)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex gap-1 mb-3">
                                @for ($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 {{ $i < $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3 leading-relaxed">
                                {{ $review->body ?: Str::limit($review->title ?? 'Great product!', 120) }}
                            </p>
                            <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700 shrink-0">
                                    {{ substr($review->user?->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $review->user?->name ?? 'Anonymous' }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $review->product?->name ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Newsletter --}}
        <section class="mb-16 bg-gray-50 rounded-2xl border border-gray-200 px-6 sm:px-12 lg:px-16 py-12 sm:py-16">
            <div class="max-w-2xl mx-auto text-center">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto mb-5">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Stay in the Loop</h2>
                <p class="text-gray-500 mb-8">Subscribe to get special offers, free giveaways, and exclusive deals.</p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    @csrf
                    <input type="email" placeholder="Enter your email" required
                           class="flex-1 px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit"
                            class="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm whitespace-nowrap">
                        Subscribe
                    </button>
                </form>
                <p class="text-xs text-gray-400 mt-4">No spam ever. Unsubscribe anytime.</p>
            </div>
        </section>
    </div>
</x-app-layout>
