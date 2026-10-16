<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
            <form method="GET" action="{{ route('products.index') }}" class="relative">
                @foreach (request()->except('search', 'page') as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                       class="pl-9 pr-4 py-2 w-full sm:w-64 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </form>
        </div>
    </x-slot>

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Filters Sidebar --}}
        <div class="lg:w-56 shrink-0" x-data="{ filtersOpen: false }">
            <button @click="filtersOpen = !filtersOpen"
                    class="lg:hidden flex items-center gap-2 text-sm font-medium text-gray-700 mb-4 px-4 sm:px-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filters
            </button>

            <div :class="filtersOpen ? 'block' : 'hidden lg:block'"
                 class="bg-white rounded-xl border border-gray-200 p-4 space-y-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Filters</h3>
                    @if (request('category'))
                        <a href="{{ route('products.index', request()->only('search')) }}" class="text-xs text-indigo-600 hover:underline">Clear all</a>
                    @endif
                </div>

                {{-- Category filter --}}
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Category</p>
                    <div class="space-y-1">
                        <a href="{{ route('products.index', request()->except('category', 'page')) }}"
                           class="block text-sm {{ !request('category') ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
                            All
                        </a>
                        @foreach ($categories as $cat)
                            <a href="{{ route('products.index', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}"
                               class="block text-sm {{ request('category') === $cat->slug ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="flex-1 min-w-0">
            {{-- Category pills --}}
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="{{ route('products.index', request()->except('category', 'page')) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    All
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('products.index', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request('category') === $cat->slug ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
                @if (request('search'))
                    <div class="ml-auto">
                        <p class="text-sm text-gray-500">Results for "<strong>{{ request('search') }}</strong>"</p>
                    </div>
                @endif
            </div>

            @if ($products->isEmpty())
                <div class="text-center py-16">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="text-gray-500 text-lg">No products found.</p>
                    @if (request('search'))
                        <p class="text-gray-400 text-sm mt-1">Try a different search term or clear some filters.</p>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
