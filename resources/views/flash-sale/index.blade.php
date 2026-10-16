<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 px-6 py-10 sm:px-10 shadow-[0_24px_70px_-20px_rgba(139,92,246,0.45)]">
            <div class="pointer-events-none absolute -right-10 -top-16 h-64 w-64 rounded-full bg-white/15 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-20 -left-10 h-64 w-64 rounded-full bg-black/10 blur-2xl"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>

            <div class="relative flex flex-col gap-6 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 shadow-inner ring-1 ring-white/40 backdrop-blur">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22l8.5-11.5H12L13 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Flash Sale</h1>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-white ring-1 ring-white/40">
                                <span class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                                Live
                            </span>
                        </div>
                        <p class="mt-1 text-sm font-medium text-amber-50/90">Huge discounts, limited time only. Prices drop — but not for long!</p>
                    </div>
                </div>
                @if ($countdown?->ends_at)
                    <div>
                        <p class="mb-1.5 text-center text-xs font-bold uppercase tracking-widest text-white">Ends in</p>
                        <x-flash-countdown :ends-at="$countdown->ends_at" />
                    </div>
                @endif
            </div>
        </div>

        {{-- Grid --}}
        @if ($flashProducts->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach ($flashProducts as $flashProduct)
                    <x-product-card :product="$flashProduct->product" :flash-ends-at="$flashProduct->flashSale?->ends_at" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $flashProducts->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <svg class="w-16 h-16 text-amber-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22l8.5-11.5H12L13 2z"/></svg>
                <p class="text-gray-500">No flash sale products are available right now.</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-4 text-[var(--color-accent)] font-medium hover:underline">Browse all products</a>
            </div>
        @endif
    </div>
</x-app-layout>
