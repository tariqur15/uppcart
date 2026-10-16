{{--
    Renders a single copy of a top bar's inner content (icon, message, highlight,
    countdown, coupon and button). Used by the top bar renderer; duplicated for
    the seamless marquee effect.
    Expects: $bar, $c, $hc, $bcolor, and optional $typewriter (bool) to apply the
    typewriter reveal + caret to the message text.
--}}
@if (($bar['icon'] ?? '') !== '')
    <span class="{{ ($bar['icon_position'] ?? 'left') === 'right' ? 'order-last' : '' }}" x-text="'{{ $bar['icon'] }}'"></span>
@endif
@if ($c['message'] !== '')
    @if (!empty($typewriter))
        <span class="tb-type-inner" style="--tb-chars: {{ max(mb_strlen($c['message']), 1) }}ch;" x-text="'{{ $c['message'] }}'"></span>
    @else
        <span>{{ $c['message'] }}</span>
    @endif
@endif
@if ($c['highlight'] !== '')
    <span style="color: {{ $hc }};" class="font-bold">{{ $c['highlight'] }}</span>
@endif
@if ($bar['type'] === 'countdown' && $c['countdown_end'] !== '')
    <span class="inline-flex items-center gap-1 font-mono font-bold tabular-nums"
          x-data="topBarCountdown('{{ \Carbon\Carbon::parse($c['countdown_end'])->toIso8601String() }}', '{{ $c['countdown_start'] !== '' ? \Carbon\Carbon::parse($c['countdown_start'])->toIso8601String() : '' }}', {{ json_encode($c['expired_message']) }})"
          x-init="start()">
        <span x-text="display"></span>
    </span>
@endif
@if ($c['coupon'] !== '')
    <span class="tb-coupon">
        <svg class="w-3.5 h-3.5 tb-coupon-scissors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.848 8.25l1.536.887M7.848 8.25a3 3 0 11-5.196-3 3 3 0 015.196 3zm0 0l8.196 4.732M12 12l8.196 4.732m-8.196-4.732L20.25 5.25m-8.196 4.732L7.848 15.75m8.196-2.018l1.536.887m-1.536-.887l-1.536.887m0 0a3 3 0 105.196 3 3 3 0 10-5.196-3zM7.848 15.75l1.536-.887M7.848 15.75a3 3 0 11-5.196 3 3 3 0 015.196-3z"/></svg>
        <span class="tb-coupon-code">{{ $c['coupon'] }}</span>
    </span>
@endif
@if ($c['button_text'] !== '')
    <a href="{{ $c['button_url'] ?: '#' }}" style="background: {{ $bcolor }}; color: #ffffff;"
       class="rounded px-3 py-1 text-xs font-bold hover:opacity-90 transition-opacity">{{ $c['button_text'] }}</a>
@endif
