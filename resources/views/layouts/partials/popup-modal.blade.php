@php
    $img = $config['image_url'] ?? null;
    if (! $img) {
        return;
    }
    $widthClass = match ($config['width'] ?? 'sm') {
        'xs' => 'max-w-xs',
        'lg' => 'max-w-lg',
        default => 'max-w-sm',
    };
    $roundedClass = match ($config['rounded'] ?? '2xl') {
        'none' => 'rounded-none',
        'md' => 'rounded-xl',
        default => 'rounded-2xl',
    };
@endphp
<div x-data="storePopup({{ json_encode([
    'id' => $id,
    'trigger' => $config['trigger'] ?? 'after_seconds',
    'trigger_seconds' => (int) ($config['trigger_seconds'] ?? 3),
    'scroll_percent' => (int) ($config['scroll_percent'] ?? 50),
    'frequency' => $config['frequency'] ?? 'once_per_session',
    'hide_after_close' => (bool) ($config['hide_after_close'] ?? true),
    'hide_after_click' => (bool) ($config['hide_after_click'] ?? false),
    'overlay_opacity' => (int) ($config['overlay_opacity'] ?? 60),
    'show_close' => true,
    'has_button_url' => (bool) ($config['button_url'] ?? null),
    'button_url' => $config['button_url'] ?? null,
]) }})" x-init="init()">
    <div x-show="open" x-cloak x-transition.opacity.duration.300ms
         class="fixed inset-0 z-[80] flex items-center justify-center p-4"
         :style="'background-color: rgba(0,0,0,' + (config.overlay_opacity / 100) + ')'"
         @click="close()">
        <div class="relative w-full {{ $widthClass }} bg-white {{ $roundedClass }} shadow-2xl overflow-hidden"
             @click.stop>
            <button type="button" x-cloak x-show="config.show_close"
                    @click="close()"
                    class="absolute top-3 right-3 z-20 p-1.5 rounded-full bg-white/80 hover:bg-white text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            @if (! empty($config['button_url']))
                <a href="{{ $config['button_url'] }}" @click="cta($event)">
                    <img src="{{ $img }}" alt="Promo" class="w-full h-auto block">
                </a>
            @else
                <img src="{{ $img }}" alt="Promo" class="w-full h-auto block cursor-pointer" @click="close()">
            @endif
        </div>
    </div>
</div>
