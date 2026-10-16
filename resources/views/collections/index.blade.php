<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Collections</h1>
        <p class="text-gray-500 mb-8">Browse our curated product collections.</p>

        @if ($collections->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($collections as $collection)
                    <a href="{{ route('collections.show', $collection->slug) }}" class="group bg-white rounded-xl border border-gray-200 hover:border-indigo-200 hover:shadow-md transition-all overflow-hidden">
                        <div class="aspect-[16/9] bg-gray-100 overflow-hidden">
                            @if ($collection->image)
                                <img src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <h2 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $collection->name }}</h2>
                            @if ($collection->description)
                                <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $collection->description }}</p>
                            @endif
                            <p class="mt-2 text-sm font-medium text-indigo-600">{{ $collection->products_count }} {{ Str::plural('product', $collection->products_count) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $collections->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h2 class="text-xl font-semibold text-gray-900">No collections available</h2>
                <p class="text-gray-500 mt-1">Check back soon for new collections.</p>
            </div>
        @endif
    </div>
</x-app-layout>
