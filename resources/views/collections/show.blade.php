<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('collections.index') }}" class="hover:text-indigo-600 transition-colors">Collections</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900">{{ $collection->name }}</span>
        </div>

        {{-- Cover --}}
        @if ($collection->image)
            <div class="relative w-full aspect-[21/9] rounded-xl overflow-hidden bg-gray-100 mb-8">
                <img src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                <div class="absolute bottom-4 sm:bottom-6 left-4 sm:left-6">
                    <h1 class="text-xl sm:text-3xl font-bold text-white">{{ $collection->name }}</h1>
                    @if ($collection->description)
                        <p class="text-sm text-white/80 mt-1">{{ $collection->description }}</p>
                    @endif
                    <p class="text-sm text-white/60 mt-1">{{ $products->total() }} {{ Str::plural('product', $products->total()) }}</p>
                </div>
            </div>
        @else
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ $collection->name }}</h1>
                @if ($collection->description)
                    <p class="mt-2 text-gray-600">{{ $collection->description }}</p>
                @endif
                <p class="mt-2 text-sm text-gray-500">{{ $products->total() }} {{ Str::plural('product', $products->total()) }}</p>
            </div>
        @endif

        {{-- Products --}}
        @if ($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-16 border-2 border-dashed border-gray-200 rounded-xl">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <h2 class="text-xl font-semibold text-gray-900">No products in this collection</h2>
                <p class="text-gray-500 mt-1">Check back soon for new products.</p>
            </div>
        @endif
    </div>
</x-app-layout>
