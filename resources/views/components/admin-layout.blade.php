<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }} — Admin</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        <style>
            .sidebar-link:hover {
                color: rgba(255,255,255,0.9) !important;
                background-color: rgba(255,255,255,0.05);
            }
            .bell-ring { animation: bell-ring 1.8s ease-in-out infinite; }
            @keyframes bell-ring {
                0%, 100% { transform: rotate(0deg); }
                8% { transform: rotate(14deg); }
                16% { transform: rotate(-12deg); }
                24% { transform: rotate(10deg); }
                32% { transform: rotate(-8deg); }
                40% { transform: rotate(4deg); }
                50%, 100% { transform: rotate(0deg); }
            }
            .bell-pulse { position: relative; }
            .bell-pulse::before {
                content: '';
                position: absolute;
                inset: -3px;
                border-radius: 50%;
                border: 2px solid #EF4444;
                animation: bell-pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            @keyframes bell-pulse-ring {
                0% { opacity: 1; transform: scale(1); }
                50% { opacity: 0; transform: scale(1.6); }
                100% { opacity: 0; transform: scale(1.6); }
            }
        </style>
    </head>
    <body class="font-sans antialiased" style="background-color: #F6F3F4;">
        <div x-data="{ sidebarOpen: false }" class="flex min-h-screen" style="background-color: #F6F3F4;">
            {{-- Overlay --}}
            <div x-show="sidebarOpen" @click="sidebarOpen = false"
                 class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

            {{-- Sidebar --}}
            <aside :class="`fixed lg:sticky top-0 left-0 z-40 h-screen w-80 flex flex-col overflow-y-auto transition-transform duration-200 ${sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}`"
                   style="background-color: #101828; border-right: 1px solid rgba(255,255,255,0.06);">
                {{-- Logo --}}
                <div class="px-5 pt-6 pb-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold" style="background-color: #6366f1; color: #fff;">
                            U
                        </div>
                        <span class="text-lg font-bold text-white/90">Uppcart</span>
                    </a>
                </div>

                <nav class="flex-1 px-3 pb-4 space-y-1">
                    {{-- Dashboard --}}
                    <x-admin-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </x-admin-link>

                    {{-- Orders Section --}}
                    <div class="pt-4 pb-1 px-3">
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.25);">Orders</p>
                    </div>
                    <x-admin-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Orders
                        <span x-show="$store.notifications.pendingOrders > 0" x-cloak
                              class="ml-2 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 rounded-full text-[10px] font-bold bg-red-500 text-white"
                              x-text="$store.notifications.pendingOrders > 99 ? '99+' : $store.notifications.pendingOrders"></span>
                    </x-admin-link>
                    <x-admin-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Customers
                    </x-admin-link>
                    <x-admin-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        Coupons
                    </x-admin-link>
                    <x-admin-link :href="route('admin.sales-reports.index')" :active="request()->routeIs('admin.sales-reports.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Sales Report
                    </x-admin-link>
                    <x-admin-link :href="route('admin.flash-sales.index')" :active="request()->routeIs('admin.flash-sales.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 2L4.5 13.5H11L10 22l8.5-11.5H12L13 2z"/>
                        </svg>
                        Flash Sales
                    </x-admin-link>

                    {{-- Purchase Section --}}
                    <div class="pt-4 pb-1 px-3">
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.25);">Purchase</p>
                    </div>
                    <x-admin-link :href="route('admin.purchase-orders.index')" :active="request()->routeIs('admin.purchase-orders.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Purchase Orders
                    </x-admin-link>
                    <x-admin-link :href="route('admin.suppliers.index')" :active="request()->routeIs('admin.suppliers.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Suppliers
                    </x-admin-link>

                    {{-- Products Section --}}
                    <div class="pt-4 pb-1 px-3">
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.25);">Products</p>
                    </div>
                    <x-admin-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Products
                    </x-admin-link>
                    <x-admin-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        Categories
                    </x-admin-link>
                    <x-admin-link :href="route('admin.brands.index')" :active="request()->routeIs('admin.brands.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Brands
                    </x-admin-link>
                    <x-admin-link :href="route('admin.collections.index')" :active="request()->routeIs('admin.collections.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Collections
                    </x-admin-link>
                    <x-admin-link :href="route('admin.attributes.index')" :active="request()->routeIs('admin.attributes.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Attributes
                    </x-admin-link>
                    <x-admin-link :href="route('admin.tags.index')" :active="request()->routeIs('admin.tags.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Tags
                    </x-admin-link>
                    <x-admin-link :href="route('admin.reviews.index')" :active="request()->routeIs('admin.reviews.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Reviews
                    </x-admin-link>

                    {{-- Content Section --}}
                    <div class="pt-4 pb-1 px-3">
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.25);">Content</p>
                    </div>
                    <x-admin-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Banners
                    </x-admin-link>
                    <x-admin-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Pages
                    </x-admin-link>
                    <x-admin-link :href="route('admin.blogs.index')" :active="request()->routeIs('admin.blogs.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        Blogs
                    </x-admin-link>

                    {{-- Users Section --}}
                    <div class="pt-4 pb-1 px-3">
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.25);">Users</p>
                    </div>
                    <x-admin-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Users
                    </x-admin-link>
                    <x-admin-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Roles
                    </x-admin-link>

                    {{-- Settings --}}
                    <div class="pt-4 pb-1 px-3">
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.25);">Settings</p>
                    </div>
                    <x-admin-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" dark>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </x-admin-link>
                </nav>

                {{-- Bottom links --}}
                <div class="border-t px-3 py-3" style="border-color: rgba(255,255,255,0.06);">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                         <button type="submit"
                                 class="sidebar-link flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-base font-medium transition-colors"
                                 style="color: rgba(255,255,255,0.5);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                    <a href="{{ route('home') }}"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors"
                       style="color: rgba(255,255,255,0.5);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Site
                    </a>
                </div>
            </aside>

            {{-- Main Content --}}
            <main class="flex-1 min-w-0 lg:ml-0 flex flex-col">
                {{-- Top Header --}}
                <div class="sticky top-0 z-30 bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 h-16 flex items-center gap-4 shrink-0">
                    {{-- Mobile sidebar toggle --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Global Search --}}
                    <div class="flex-1 max-w-lg" x-data="globalSearch" @click.away="open = false">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="search" x-model="query" @input="search()" @focus="open = true"
                                   placeholder="Search products, categories, orders..."
                                   autocomplete="off"
                                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-sm bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">

                            {{-- Search results dropdown --}}
                            <div x-show="open && searched && hasResults" x-cloak
                                 class="absolute top-full mt-2 left-0 right-0 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden max-h-[70vh] overflow-y-auto">

                                {{-- Products --}}
                                <template x-if="results?.products?.length > 0">
                                    <div>
                                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Products</p>
                                        </div>
                                        <template x-for="p in results.products" :key="'p'+p.id">
                                            <a :href="p.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0 overflow-hidden">
                                                    <img x-show="p.image" :src="p.image" alt="" class="w-full h-full object-contain">
                                                    <svg x-show="!p.image" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="p.name"></p>
                                                    <p class="text-xs text-gray-400" x-text="p.sku ? 'SKU: ' + p.sku : ''"></p>
                                                </div>
                                                <span class="text-sm font-semibold text-gray-900 shrink-0" x-text="'$' + Number(p.price).toFixed(2)"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                {{-- Categories --}}
                                <template x-if="results?.categories?.length > 0">
                                    <div>
                                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Categories</p>
                                        </div>
                                        <template x-for="c in results.categories" :key="'c'+c.id">
                                            <a :href="c.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-medium text-gray-900" x-text="c.name"></p>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                {{-- Orders --}}
                                <template x-if="results?.orders?.length > 0">
                                    <div>
                                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Orders</p>
                                        </div>
                                        <template x-for="o in results.orders" :key="'o'+o.id">
                                            <a :href="o.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="o.order_number"></p>
                                                    <p class="text-xs text-gray-400" x-text="o.customer"></p>
                                                </div>
                                                <span class="text-xs font-medium text-gray-500 shrink-0 capitalize" x-text="o.status"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            {{-- Empty state --}}
                            <div x-show="open && searched && !(results?.products?.length > 0 || results?.categories?.length > 0 || results?.orders?.length > 0)" x-cloak
                                 class="absolute top-full mt-2 left-0 right-0 bg-white rounded-xl shadow-lg border border-gray-200 p-6 text-center">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <p class="text-sm text-gray-500">No results found for "<span x-text="query"></span>"</p>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Notifications + Profile --}}
                    <div class="ml-auto shrink-0 flex items-center gap-2">
                        {{-- Notification Bell --}}
                        <div x-data="notificationBell()" x-init="init()" class="relative">
                            <button @click="open = !open"
                                    class="relative flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 transition-colors"
                                    :class="$store.notifications.unreadCount > 0 ? 'bell-pulse' : ''">
                                <svg class="w-5 h-5 text-gray-500" :class="$store.notifications.unreadCount > 0 ? 'bell-ring' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span x-show="$store.notifications.unreadCount > 0" x-cloak
                                      class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[16px] h-[16px] px-1 rounded-full bg-red-500 text-white text-[9px] font-bold"
                                      x-text="$store.notifications.unreadCount > 99 ? '99+' : $store.notifications.unreadCount"></span>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="open" x-cloak
                                 @click.outside="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900">Notifications</p>
                                    <button x-show="$store.notifications.unreadCount > 0" @click="markAllRead()"
                                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark all read</button>
                                </div>
                                <div class="flex border-b border-gray-100">
                                    <button @click="tab = 'orders'"
                                            class="flex-1 py-2.5 text-xs font-semibold uppercase tracking-wide transition-colors border-b-2 -mb-px"
                                            :class="tab === 'orders' ? 'text-indigo-600 border-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700'">
                                        Orders
                                    </button>
                                    <button @click="tab = 'messages'"
                                            class="flex-1 py-2.5 text-xs font-semibold uppercase tracking-wide transition-colors border-b-2 -mb-px"
                                            :class="tab === 'messages' ? 'text-indigo-600 border-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700'">
                                        Messages
                                    </button>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="tab === 'orders' && orders.length === 0">
                                        <div class="p-6 text-center">
                                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-sm text-gray-500">No order notifications yet</p>
                                        </div>
                                    </template>
                                    <template x-if="tab === 'messages' && messages.length === 0">
                                        <div class="p-6 text-center">
                                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <p class="text-sm text-gray-500">No message notifications yet</p>
                                        </div>
                                    </template>
                                    <template x-if="tab === 'orders'">
                                        <template x-for="n in orders" :key="n.id">
                                            <a :href="n.url || '#'"
                                               class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors"
                                               :class="!n.read_at ? 'bg-indigo-50/40' : ''"
                                               @click="markRead(n)">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                                     :class="n.type === 'new_order' ? 'bg-emerald-100' : 'bg-blue-100'">
                                                    <svg class="w-4 h-4" :class="n.type === 'new_order' ? 'text-emerald-600' : 'text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900" x-text="n.title"></p>
                                                    <p class="text-xs text-gray-500 mt-0.5 truncate" x-text="n.message"></p>
                                                    <p class="text-[10px] text-gray-400 mt-1" x-text="timeAgo(n.created_at)"></p>
                                                </div>
                                                <div x-show="!n.read_at" class="w-2 h-2 rounded-full bg-indigo-500 shrink-0 mt-1.5"></div>
                                            </a>
                                        </template>
                                    </template>
                                    <template x-if="tab === 'messages'">
                                        <template x-for="n in messages" :key="n.id">
                                            <a :href="n.url || '#'"
                                               class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors"
                                               :class="!n.read_at ? 'bg-indigo-50/40' : ''"
                                               @click="markRead(n)">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-emerald-100">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900" x-text="n.title"></p>
                                                    <p class="text-xs text-gray-500 mt-0.5 truncate" x-text="n.message"></p>
                                                    <p class="text-[10px] text-gray-400 mt-1" x-text="timeAgo(n.created_at)"></p>
                                                </div>
                                                <div x-show="!n.read_at" class="w-2 h-2 rounded-full bg-indigo-500 shrink-0 mt-1.5"></div>
                                            </a>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Profile --}}
                        @auth
                            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                <button @click="open = !open"
                                        class="flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="w-full h-full rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </span>
                                </button>

                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                                    </div>
                                    <div class="py-1">
                                        <a href="{{ route('profile.edit') }}"
                                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit Profile
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                                Log out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <div class="py-6 px-4 sm:px-8 lg:px-12 flex-1">
                    {{-- Flash messages --}}
                    <x-flash-messages />

                    {{ $slot }}
                </div>
                @stack('scripts')
            </main>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('notifications', {
                    unreadCount: 0,
                    pendingOrders: 0,
                });

                Alpine.data('notificationBell', () => ({
                    open: false,
                    tab: 'orders',
                    orders: [],
                    messages: [],
                    init() {
                        this.fetchNotifications();
                        setInterval(() => this.fetchNotifications(), 30000);
                    },
                    fetchNotifications() {
                        fetch('{{ route("admin.notifications.index") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.json())
                            .then(d => {
                                this.orders = d.orders || [];
                                this.messages = d.messages || [];
                                Alpine.store('notifications').unreadCount = d.unread_count;
                                Alpine.store('notifications').pendingOrders = d.pending_orders;
                            });
                    },
                    markRead(n) {
                        if (n.read_at) return;
                        fetch('{{ route("admin.notifications.mark-read") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ id: n.id })
                        }).then(r => r.json()).then(d => {
                            n.read_at = new Date().toISOString();
                            Alpine.store('notifications').unreadCount = d.unread_count;
                        });
                    },
                    markAllRead() {
                        fetch('{{ route("admin.notifications.mark-read") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({})
                        }).then(r => r.json()).then(d => {
                            this.fetchNotifications();
                        });
                    },
                    timeAgo(date) {
                        const s = Math.floor((new Date() - new Date(date)) / 1000);
                        if (s < 60) return 'just now';
                        if (s < 3600) return Math.floor(s / 60) + 'm ago';
                        if (s < 86400) return Math.floor(s / 3600) + 'h ago';
                        return Math.floor(s / 86400) + 'd ago';
                    },
                }));
            });
        </script>

        {{-- Toast notification --}}
        <div x-data="{ show: false, message: '', type: 'success' }"
             x-init="
                window.addEventListener('toast', (e) => {
                    message = e.detail.message;
                    type = e.detail.type || 'success';
                    show = true;
                    setTimeout(() => { show = false }, 3000);
                });
             "
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             x-cloak
             class="fixed bottom-4 right-4 z-50 max-w-sm"
             role="alert">
            <div class="flex items-center gap-3 bg-white rounded-xl shadow-lg border border-gray-200 px-5 py-4"
                 :class="type === 'success' ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500'">
                <template x-if="type === 'success'">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="type !== 'success'">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
                <p class="text-sm font-medium text-gray-800" x-text="message"></p>
                <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </body>
</html>