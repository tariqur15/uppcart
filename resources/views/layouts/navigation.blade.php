@php
    $storeName = $storeSettings['store_name'] ?? config('app.name', 'Store');
    $storeLogo = $storeSettings['store_logo'] ?? null;
@endphp

{{-- Top bars --}}
@if ($topBars->isNotEmpty() && !request()->routeIs('orders.show'))
    <style>
        .tb-slide { overflow: hidden; white-space: nowrap; }
        .tb-slide-inner { display: inline-flex; will-change: transform; animation: tbSlide 18s linear infinite; }
        @keyframes tbSlide { 0% { transform: translateX(110%); } 100% { transform: translateX(-110%); } }
        .tb-marquee { overflow: hidden; white-space: nowrap; }
        .tb-marquee-track { display: flex; width: max-content; white-space: nowrap; will-change: transform; animation: tbMarquee 24s linear infinite; }
        @keyframes tbMarquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .tb-fade { animation: tbFade 2.5s ease-in-out infinite; }
        @keyframes tbFade { 0%, 100% { opacity: 1; } 50% { opacity: 0.2; } }
        .tb-type-inner {
            display: inline-block;
            white-space: pre;
            overflow: hidden;
            border-right: 2px solid currentColor;
            width: 0;
            animation: tbType 2.8s steps(24, end) forwards;
        }
        @keyframes tbType { to { width: var(--tb-chars); } }
        .tb-coupon {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.875rem;
            background: #ffffff;
            color: #111827;
            border: 1.5px dashed rgba(0, 0, 0, 0.35);
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
            white-space: nowrap;
        }
        .tb-coupon-code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; text-transform: uppercase; }
    </style>
    @foreach ($topBars as $tb)
        @php
            $bar = $tb['bar'];
            $c = $tb['content'];
            $fontSizeMap = ['xs' => 'text-xs', 'sm' => 'text-sm', 'base' => 'text-base', 'lg' => 'text-lg'];
            $fontWeightMap = ['normal' => 'font-normal', 'medium' => 'font-medium', 'semibold' => 'font-semibold', 'bold' => 'font-bold'];
            $alignMap = ['left' => 'text-left', 'center' => 'text-center', 'right' => 'text-right'];
            $animMap = [
                'static' => '',
                'sliding' => 'tb-slide',
                'marquee' => 'tb-marquee',
                'fade' => 'tb-fade',
                'typewriter' => 'tb-typewriter',
            ];
            $fs = $fontSizeMap[$bar['font_size'] ?? ''] ?? 'text-sm';
            $fw = $fontWeightMap[$bar['font_weight'] ?? ''] ?? 'font-semibold';
            $align = $alignMap[$bar['text_align'] ?? ''] ?? 'text-center';
            $animation = $bar['animation'] ?? '';
            $anim = $animMap[$animation] ?? '';
            $isMarquee = $animation === 'marquee';
            $isSliding = $animation === 'sliding';
            $isTypewriter = $animation === 'typewriter';
            $isAnimated = $isMarquee || $isSliding;
            $height = max((int) ($bar['bar_height'] ?? 12), 8);
            $bg = $bar['bg_color'] ?? '#4f46e5';
            $tc = $bar['text_color'] ?? '#ffffff';
            $hc = $bar['highlight_color'] ?? '#fbbf24';
            $bcolor = $bar['button_color'] ?? '#10b981';
            $close = ($bar['show_close'] ?? true) === true;
            $sticky = ($bar['sticky'] ?? false) === true;
        @endphp
        @if ($c['message'] !== '' || $c['button_text'] !== '' || $bar['type'] === 'countdown')
            <div x-data="{ open: true }" x-show="open"
                 style="background-color: {{ $bg }}; color: {{ $tc }}; min-height: {{ $height }}px;"
                 class="relative w-full {{ $sticky ? 'sticky top-0 z-40' : '' }} {{ $anim }} {{ $fs }} {{ $fw }} {{ $align }}">
                <div class="flex items-center px-9 py-2 {{ $isMarquee ? 'tb-marquee-track' : ($isSliding ? 'tb-slide-inner gap-2' : 'justify-center gap-2') }}">
                    @for ($copy = 0; $copy < ($isMarquee ? 2 : 1); $copy++)
                        @include('layouts.partials.top-bar-items', [
                            'bar' => $bar,
                            'c' => $c,
                            'hc' => $hc,
                            'bcolor' => $bcolor,
                            'typewriter' => $isTypewriter,
                        ])
                    @endfor
                </div>
                @if ($close)
                    <button @click="open = false" class="absolute right-2.5 top-1/2 -translate-y-1/2 opacity-70 hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>
        @endif
    @endforeach
@endif

@if ($topBars->isEmpty() && (($storeSettings['top_bar_enabled'] ?? '1') === '1') && ($storeSettings['announcement_bar_text'] ?? null) && !request()->routeIs('orders.show'))
    <div x-data="{ annOpen: true }" x-show="annOpen" class="text-white text-xs sm:text-sm text-center py-2 px-4 relative"
         style="background-color: {{ $storeSettings['top_bar_bg_color'] ?? '#4f46e5' }};">
        <p class="font-medium">{!! $storeSettings['announcement_bar_text'] !!}</p>
        <button @click="annOpen = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
@endif

<nav x-data="{ mobileOpen: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50">
    {{-- Top bar: Logo | Search | Icons --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16 gap-3 lg:gap-6">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-gray-900 shrink-0">
                @if ($storeLogo)
                    @php $logoSize = (int) ($storeSettings['store_logo_size'] ?? 32); @endphp
                    <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" style="height: {{ $logoSize }}px; width: auto;" class="object-contain">
                @else
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>{{ $storeName }}</span>
                @endif
            </a>

            {{-- Search centered --}}
            <div class="flex-1 flex justify-center">
                <div x-data="liveSearch" @click.away="resultsOpen = false" class="relative w-full max-w-lg hidden sm:block">
                    <form @submit.prevent="submitSearch()" role="search">
                        <div class="relative">
                            <input type="search" x-model="query" @input="search()" @focus="search()"
                                   placeholder="Search products..."
                                   autocomplete="off"
                                   class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </form>

                    {{-- Search results dropdown --}}
                    <div x-show="resultsOpen && results.length > 0" x-cloak
                         class="absolute top-full mt-2 left-0 right-0 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <template x-for="product in results" :key="product.id">
                            <a :href="product.url" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                    <img x-show="product.image" :src="product.image" :alt="product.name" class="w-8 h-8 object-contain">
                                    <svg x-show="!product.image" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                                    <p class="text-xs text-gray-500" x-text="product.category"></p>
                                </div>
                                <div class="text-sm font-semibold text-gray-900 shrink-0">
                                    <template x-if="product.sale_price">
                                        <span><span x-text="`{{ $currencySymbol }}${Number(product.sale_price).toFixed(2)}`" class="text-red-600"></span></span>
                                    </template>
                                    <template x-if="!product.sale_price">
                                        <span x-text="`{{ $currencySymbol }}${Number(product.price).toFixed(2)}`"></span>
                                    </template>
                                </div>
                            </a>
                        </template>
                        <a :href="`/products?search=${query}`" class="block px-4 py-3 text-sm font-medium text-indigo-600 hover:bg-indigo-50 text-center transition-colors border-t border-gray-100">
                            View all results for "<span x-text="query"></span>"
                        </a>
                    </div>

                    {{-- No results --}}
                    <div x-show="resultsOpen && searched && results.length === 0" x-cloak
                         class="absolute top-full mt-2 left-0 right-0 bg-white rounded-xl shadow-lg border border-gray-200 p-6 text-center">
                        <p class="text-gray-500 text-sm">No products found for "<span x-text="query"></span>"</p>
                    </div>
                </div>
            </div>

            {{-- Right icons --}}
            <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                {{-- Profile / Account --}}
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-1.5 p-1.5 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors" title="Account">
                                <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                                <span class="hidden lg:inline">{{ Auth::user()->name }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')">Dashboard</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-1.5 p-1.5 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors" title="Log in">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="hidden lg:inline">Login</span>
                    </a>
                @endauth

                {{-- Wishlist --}}
                <button class="p-2 text-gray-500 hover:text-gray-700 transition-colors relative" title="Wishlist">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>

                {{-- Cart --}}
                <div class="relative"
                     x-data
                     x-on:mouseenter="$store.cart.openDrawer()"
                     x-on:mouseleave="$store.cart.scheduleClose()">
                    <a href="{{ route('cart.loading') }}"
                       data-cart-icon
                       class="p-2 text-gray-500 hover:text-gray-700 transition-colors relative block"
                       title="Cart"
                       aria-label="Open shopping cart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span x-show="$store.cart.count > 0"
                          x-text="Math.min($store.cart.count, 99)"
                          class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
                    </span>
                </a>
                </div>
                <button @click="mobileOpen = ! mobileOpen" class="p-2 text-gray-500 hover:text-gray-700 lg:hidden">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileOpen, 'inline-flex': ! mobileOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! mobileOpen, 'inline-flex': mobileOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Categories bar --}}
    <div class="border-t border-gray-100 bg-gray-50 hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-1 h-11 overflow-x-auto">
                <a href="{{ route('products.index') }}"
                   class="whitespace-nowrap px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                          {{ !request('category') && !request('search') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200' }}">
                    All
                </a>
                <a href="{{ route('flash-sale.index') }}"
                   class="whitespace-nowrap px-3 py-1.5 text-sm font-bold rounded-lg transition-colors flex items-center gap-1
                          {{ request()->routeIs('flash-sale.index') ? 'bg-violet-500 text-white' : 'text-violet-600 hover:bg-violet-100' }}">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22l8.5-11.5H12L13 2z"/></svg>
                    Flash Sale
                </a>
                @foreach ($allCategories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug, 'search' => request('search')]) }}"
                       class="whitespace-nowrap px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                              {{ request('category') === $category->slug ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': mobileOpen, 'hidden': ! mobileOpen}" class="hidden lg:hidden border-t border-gray-100">
        {{-- Mobile search --}}
        <div class="px-4 py-3">
            <div x-data="liveSearch" @click.away="resultsOpen = false">
                <form @submit.prevent="submitSearch()">
                    <div class="relative">
                        <input type="search" x-model="query" @input="search()" @focus="search()"
                               placeholder="Search products..." autocomplete="off"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm bg-gray-50">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </form>
                {{-- Mobile results --}}
                <div x-show="resultsOpen && results.length > 0" x-cloak class="mt-2 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <template x-for="product in results" :key="product.id">
                        <a :href="product.url" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                <img x-show="product.image" :src="product.image" :alt="product.name" class="w-8 h-8 object-contain">
                                <svg x-show="!product.image" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                                <p class="text-xs text-gray-500" x-text="product.category"></p>
                            </div>
                            <div class="text-sm font-semibold text-gray-900 shrink-0">
                                <template x-if="product.sale_price">
                                    <span class="text-red-600" x-text="`{{ $currencySymbol }}${Number(product.sale_price).toFixed(2)}`"></span>
                                </template>
                                <template x-if="!product.sale_price">
                                    <span x-text="`{{ $currencySymbol }}${Number(product.price).toFixed(2)}`"></span>
                                </template>
                            </div>
                        </a>
                    </template>
                    <a :href="`/products?search=${query}`" class="block px-4 py-3 text-sm font-medium text-indigo-600 hover:bg-indigo-50 text-center transition-colors border-t border-gray-100">
                        View all results
                    </a>
                </div>
            </div>
        </div>

        <div class="px-4 py-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-responsive-nav-link>

            <div class="pt-2 pb-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Categories</p>
            </div>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.index') && !request('category')">
                All Products
            </x-responsive-nav-link>
            @foreach ($allCategories as $category)
                <x-responsive-nav-link :href="route('products.index', ['category' => $category->slug])"
                    :active="request('category') === $category->slug"
                    class="!ps-8 text-sm">
                    {{ $category->name }}
                </x-responsive-nav-link>
            @endforeach

            @auth
                <div class="border-t border-gray-100 pt-2 mt-2"></div>
                <x-responsive-nav-link :href="route('cart.loading')" :active="request()->routeIs('cart.*')">Cart</x-responsive-nav-link>
            @else
                <div class="border-t border-gray-100 pt-2 mt-2"></div>
                <x-responsive-nav-link :href="route('checkout.loading')" :active="request()->routeIs('checkout.*')">Cart</x-responsive-nav-link>
            @endauth
        </div>

        <div class="border-t border-gray-100 px-4 py-3 space-y-1">
            @auth
                <div class="flex items-center gap-3 px-3 py-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <x-responsive-nav-link :href="route('dashboard')">Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            @else
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 bg-indigo-600 rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Sign up</a>
                </div>
            @endauth
        </div>
    </div>
</nav>

{{-- Cart drawer --}}
<div x-show="$store.cart.drawerOpen" x-cloak class="fixed inset-0 z-[60]"
     x-on:mouseenter="$store.cart.cancelClose()"
     x-on:mouseleave="$store.cart.scheduleClose()">
    {{-- Overlay --}}
    <div x-show="$store.cart.drawerOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 bg-black/40"
         x-on:click="$store.cart.drawerOpen = false"></div>

    {{-- Panel --}}
    <div x-show="$store.cart.drawerOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-xl flex flex-col">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between shrink-0">
            <p class="text-sm font-semibold text-gray-900">
                Shopping Cart <span class="text-gray-500" x-text="`(${$store.cart.count})`"></span>
            </p>
            <button type="button" x-on:click="$store.cart.drawerOpen = false" class="p-1 text-gray-400 hover:text-gray-600 transition-colors" aria-label="Close cart">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Items --}}
        <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
            <template x-for="item in $store.cart.items" :key="item.key">
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                        <template x-if="item.image">
                            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!item.image">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </template>
                    </div>

                    <div class="flex-1 min-w-0">
                        <a :href="item.url" class="text-sm font-medium text-gray-900 truncate block hover:text-indigo-600 transition-colors" x-text="item.name"></a>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="`{{ $currencySymbol }}${Number(item.unit_price).toFixed(2)}`"></p>

                        {{-- Qty stepper --}}
                        <div class="flex items-center mt-1.5 border border-gray-200 rounded-lg w-fit">
                            <button type="button"
                                    class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-gray-900 transition-colors"
                                    @click="$store.cart.updateQty(item, item.quantity - 1)">−</button>
                            <span class="w-7 text-center text-sm font-medium" x-text="item.quantity"></span>
                            <button type="button"
                                    class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-gray-900 transition-colors"
                                    @click="$store.cart.updateQty(item, item.quantity + 1)">+</button>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-1 shrink-0">
                        <span class="text-sm font-semibold text-gray-900" x-text="`{{ $currencySymbol }}${Number(item.subtotal).toFixed(2)}`"></span>
                        <button type="button"
                                class="p-1 text-gray-400 hover:text-red-500 transition-colors"
                                title="Remove"
                                @click="$store.cart.removeItem(item)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="$store.cart.items.length === 0" class="px-4 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                <p class="text-sm text-gray-500">Your cart is empty</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-4 border-t border-gray-100 bg-gray-50 shrink-0">
            <div class="flex justify-between text-sm font-semibold text-gray-900 mb-3">
                <span>Total</span>
                <span x-text="`{{ $currencySymbol }}${Number($store.cart.total).toFixed(2)}`"></span>
            </div>
            <a href="{{ auth()->check() ? route('cart.loading') : route('checkout.loading') }}"
               @click="$store.cart.drawerOpen = false"
               class="block w-full text-center bg-indigo-600 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-indigo-700 transition-colors">
                {{ auth()->check() ? 'View Cart' : 'Checkout' }}
            </a>
        </div>
    </div>
</div>


