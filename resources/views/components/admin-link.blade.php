@props(['active' => false, 'href' => '#', 'dark' => false])

@php
if ($dark) {
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium text-white bg-[#5C00C6] transition-colors'
        : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium text-white/80 hover:text-white hover:bg-white/15 transition-colors';
} else {
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium text-indigo-700 bg-indigo-50 transition-colors'
        : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors';
}
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
