@props(['endsAt'])

<div x-data="flashCountdown('{{ \Illuminate\Support\Carbon::parse($endsAt)->toIso8601String() }}')"
     x-cloak
     class="flex items-start gap-1.5 sm:gap-2">
    @foreach ([['d', 'Days'], ['h', 'Hrs'], ['m', 'Min'], ['s', 'Sec']] as [$key, $label])
        <div class="flex flex-col items-center">
            <div class="bg-white text-violet-900 rounded-xl min-w-12 sm:min-w-14 px-1.5 py-2 text-center shadow-md ring-2 ring-violet-500/30">
                <span class="text-xl sm:text-2xl leading-none font-extrabold tabular-nums" x-text="padded.{{ $key }}"></span>
                <span class="block mt-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider">{{ $label }}</span>
            </div>
        </div>
    @endforeach
</div>
