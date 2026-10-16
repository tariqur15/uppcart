<x-admin-layout>
    @push('styles')
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devchauhann/fonts@v1.1.0/cdn/v1/css/all.css">
        <style>
            .cp *{box-sizing:border-box;}
            .cp{
                font-family:'Anthropic Sans', 'Inter', sans-serif;
                -webkit-font-smoothing:antialiased;
            }
        </style>
    @endpush

    <div class="cp px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.customers.index') }}" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition shadow-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Customer Details</h1>
                    <p class="text-xs text-gray-500">ID: {{ $user->customerCode() }} Joined {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('admin.customers.edit', $user) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm inline-flex items-center gap-1.5">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-500"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/></svg>
                    Edit Profile
                </a>
                <a href="{{ route('admin.orders.create', ['user_id' => $user->id]) }}" class="px-4 py-2 bg-indigo-600 rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition shadow-sm inline-flex items-center gap-1.5">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Create Order
                </a>
                @if ($isBlocked)
                    <form action="{{ route('admin.customers.restore', $user) }}" method="POST" onsubmit="return confirm('Unblock {{ $user->name }}?')" class="inline">
                        @csrf
                        <button class="px-4 py-2 bg-red-600 rounded-lg text-sm font-medium text-white hover:bg-red-700 transition shadow-sm inline-flex items-center gap-1.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
                            Unblock
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.customers.block', $user) }}" method="POST" onsubmit="return confirm('Block {{ $user->name }}? Their phone and latest IP will be blocked.')" class="inline">
                        @csrf
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm inline-flex items-center gap-1.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M5.5 5.5l13 13"/></svg>
                            Block
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Main grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Left sidebar (profile + messages) --}}
            <div class="space-y-6 -ml-4 sm:-ml-6 lg:-ml-8 lg:col-span-3 lg:order-1">

                {{-- Profile card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex flex-col items-center text-center border-b border-gray-100 pb-6">
                        @php $avatarColors = ['#A31E22','#6C4EE8','#0891B2','#12B76A','#F5A623','#3B82F6','#64748B']; @endphp
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-2xl font-bold mb-3 border-2" style="background:{{ $avatarColors[$user->id % 7] }}20; color:{{ $avatarColors[$user->id % 7] }}; border-color:{{ $avatarColors[$user->id % 7] }}40;">
                            {{ collect(explode(' ', $user->name ?? '?'))->filter()->map(fn ($w) => strtoupper($w[0]))->take(2)->join('') }}
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $user->name ?? 'Guest' }}</h2>
                        <span class="mt-1 text-xs text-gray-400 font-medium">Customer ID: {{ $user->customerCode() }}</span>
                        <span class="mt-1.5 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isBlocked ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isBlocked ? 'bg-red-500' : 'bg-emerald-500' }} mr-1.5"></span>
                            {{ $isBlocked ? 'Blocked' : 'Active' }}
                        </span>
                    </div>

                    <div class="pt-6 space-y-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Contact Info</h3>

                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs text-gray-400">Email Address</p>
                                <p class="font-medium text-gray-800 truncate">{{ $user->email ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Phone</p>
                                <p class="font-medium text-gray-800 flex items-center gap-2">
                                    {{ $user->phone }}
                                    <a href="{{ \App\Helpers\PhoneHelper::whatsappLink($user->phone, $storeSettings['timezone'] ?? null) }}" target="_blank" class="text-emerald-500 hover:text-emerald-700" title="Message on WhatsApp">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Default Shipping Address</p>
                                <p class="font-medium text-gray-800">
                                    @php $addr = $addresses->first(); @endphp
                                    {{ $addr ? $addr->address . ', ' . ($addr->thana ?? '') . ', ' . ($addr->district ?? '') . ' - ' . ($addr->division ?? '') : 'No address on file' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Message Customer --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold" style="background:#6C4EE8; color:#fff;">U</div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Message Customer</h3>
                            <p class="text-[11px] text-gray-400">{{ $messages->count() }} {{ Str::plural('message', $messages->count()) }} in this conversation</p>
                        </div>
                    </div>

                    <div class="h-80 overflow-y-auto p-4 space-y-3 bg-gray-50/60" id="messageThread"
                         x-data="adminMessagePoller()"
                         data-poll-url="{{ route('admin.customers.messages.poll', $user) }}"
                         data-last-id="{{ $messages->last()?->id ?? 0 }}">
                        @forelse ($messages as $msg)
                            @if ($msg->direction === 'outgoing')
                                <div class="flex justify-end">
                                    <div class="max-w-[85%] bg-indigo-600 text-white rounded-2xl rounded-br-md px-3.5 py-2">
                                        @if ($msg->attachment_path)
                                            <a href="{{ route('admin.customers.message.attachment', $msg) }}" target="_blank" class="block mb-1.5">
                                                <div class="flex items-center gap-2.5 bg-indigo-500/40 rounded-lg px-3 py-2 hover:bg-indigo-500/60 transition">
                                                    @if (str_starts_with($msg->attachment_mime, 'image/'))
                                                        <img src="{{ Storage::disk('public')->url($msg->attachment_path) }}" alt="{{ $msg->attachment_name }}" class="max-w-[200px] max-h-[160px] rounded-lg object-contain">
                                                    @else
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                                        <span class="text-[13px] font-medium truncate max-w-[180px]">{{ $msg->attachment_name }}</span>
                                                    @endif
                                                </div>
                                            </a>
                                        @endif
                                        @if ($msg->message)
                                            <p class="text-[13px] leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
                                        @endif
                                        <p class="text-[10px] text-indigo-200 mt-1 text-right">{{ $msg->created_at->format('M d, g:i A') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-[85%] bg-white border border-gray-200 rounded-2xl rounded-bl-md px-3.5 py-2 shadow-sm">
                                        @if ($msg->attachment_path)
                                            <a href="{{ route('admin.customers.message.attachment', $msg) }}" target="_blank" class="block mb-1.5">
                                                <div class="flex items-center gap-2.5 bg-gray-100 rounded-lg px-3 py-2 hover:bg-gray-200 transition">
                                                    @if (str_starts_with($msg->attachment_mime, 'image/'))
                                                        <img src="{{ Storage::disk('public')->url($msg->attachment_path) }}" alt="{{ $msg->attachment_name }}" class="max-w-[200px] max-h-[160px] rounded-lg object-contain">
                                                    @else
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0 text-gray-500"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                                        <span class="text-[13px] font-medium truncate max-w-[180px]">{{ $msg->attachment_name }}</span>
                                                    @endif
                                                </div>
                                            </a>
                                        @endif
                                        @if ($msg->message)
                                            <p class="text-[13px] leading-relaxed text-gray-700 whitespace-pre-wrap">{{ $msg->message }}</p>
                                        @endif
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $msg->created_at->format('M d, g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div id="messageEmptyState" class="flex flex-col items-center justify-center h-full text-center py-8">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="text-gray-300 mb-2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                                <p class="text-xs text-gray-400">No messages yet. Start the conversation below.</p>
                            </div>
                        @endforelse
                    </div>

                    <form action="{{ route('admin.customers.message', $user) }}" method="POST" enctype="multipart/form-data" class="border-t border-gray-100 p-3" x-data="chatComposer()" @submit.prevent="submit">
                        @csrf
                        <template x-if="error">
                            <p x-text="error" class="text-[11px] text-red-600 bg-red-50 rounded-md px-2.5 py-1.5 mb-2"></p>
                        </template>
                        <div class="flex items-end gap-2">
                            <div class="shrink-0 flex flex-col items-center gap-1">
                                <label for="attachment" class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 transition cursor-pointer" title="Attach a file">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                </label>
                                <input type="file" name="attachment" id="attachment" class="hidden">
                            </div>
                            <textarea name="message" rows="2" maxlength="5000" placeholder="Type a message..." x-model="message" x-ref="messageTextarea"
                                      class="flex-1 resize-none border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-700"></textarea>
                            <button type="submit" :disabled="sending" class="shrink-0 flex items-center gap-1.5 px-3.5 py-2.5 bg-indigo-600 rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <template x-if="sending">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="animate-spin"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" class="opacity-75"></path></svg>
                                </template>
                                <template x-if="!sending">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                </template>
                                <span x-text="sending ? 'Sending...' : 'Send'"></span>
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5 px-1">Stored as an admin record. No external delivery. Max 20 MB per file.</p>
                    </form>
                </div>
            </div>

            {{-- Right sidebar --}}
            <div class="space-y-6 -mr-2 sm:-mr-4 lg:-mr-4 lg:col-span-3 lg:order-3">

                {{-- Loyalty Tier --}}
                <div class="bg-gradient-to-br from-[#2A2016] to-[#4A3620] rounded-xl p-6 text-white relative overflow-hidden shadow-sm">
                    <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full" style="background:rgba(245,166,35,0.15);"></div>
                    <p class="relative text-[11px] font-bold uppercase tracking-widest text-amber-400">{{ $loyaltyTier }} Member</p>
                    <h3 class="relative text-xl font-bold mt-1">{{ number_format($loyaltyPoints) }} loyalty points</h3>
                    <p class="relative text-xs text-white/60 mt-1">৳{{ number_format($totalSpent) }} lifetime spending</p>

                    {{-- Tier flow --}}
                    <div class="relative mt-5">
                        <div class="relative h-1.5 rounded-full bg-white/10">
                            <div class="absolute inset-y-0 left-0 rounded-full bg-amber-400 transition-all duration-500" style="width: {{ $tierProgress }}%;"></div>
                        </div>
                        <div class="relative flex justify-between mt-2">
                            @foreach ($tierFlow as $tier)
                                <div class="flex flex-col items-center gap-1.5" title="Spend ৳{{ number_format($tier['threshold']) }}">
                                    <span class="w-2 h-2 rounded-full {{ $tier['current'] ? 'bg-amber-400 ring-4 ring-amber-400/25' : ($tier['reached'] ? 'bg-amber-400/80' : 'bg-white/15') }}"></span>
                                    <span class="text-[9px] font-semibold uppercase tracking-wider {{ $tier['current'] ? 'text-amber-300' : ($tier['reached'] ? 'text-white' : 'text-white/35') }}">{{ $tier['name'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($nextTierName)
                        <div class="relative mt-4 pt-3 border-t border-white/10 flex items-center justify-between">
                            <span class="text-xs text-white/60">{{ number_format($pointsToNext) }} points to <span class="text-amber-300 font-semibold">{{ $nextTierName }}</span></span>
                            <span class="text-[10px] text-white/40">৳{{ number_format($nextTierAmount) }}</span>
                        </div>
                    @else
                        <p class="relative text-xs text-white/60 mt-4 pt-3 border-t border-white/10">Highest tier reached</p>
                    @endif
                </div>

                {{-- Payment Methods Used --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Payment Methods Used</h3>
                    </div>
                    @php
                        $paymentLabels = ['bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'card' => 'Card', 'cod' => 'Cash on Delivery'];
                        $paymentColors = ['bkash' => '#E2136E', 'nagad' => '#F6921E', 'rocket' => '#8B1E8B', 'card' => '#2A2E43', 'cod' => '#12B76A'];
                        $pmTotal = $paymentBreakdown->sum('count');
                    @endphp
                    @forelse ($paymentBreakdown as $pm)
                        @php
                            $label = $paymentLabels[$pm->payment_method] ?? ucfirst($pm->payment_method);
                            $color = $paymentColors[$pm->payment_method] ?? '#64748B';
                            $pct = $pmTotal > 0 ? round(($pm->count / $pmTotal) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3 py-1.5">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>
                            <span class="text-sm text-gray-700 flex-1">{{ $label }}</span>
                            <div class="w-16 h-1.5 rounded-full bg-gray-100 overflow-hidden flex-shrink-0">
                                <div class="h-full rounded-full" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                            </div>
                            <span class="text-xs text-gray-400 w-9 text-right">{{ $pct }}%</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No payment data available.</p>
                    @endforelse
                </div>

                {{-- Admin Notes --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Admin Notes</h3>
                    </div>
                    <textarea rows="3" placeholder="Add internal notes about this customer..." class="w-full border border-gray-300 rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-700" readonly></textarea>
                </div>

                {{-- Account Activity --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Account Activity</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-5 pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-medium">Registered</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-medium">Last Login</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $lastLogin ? $lastLogin->format('M d, Y g:i A') : 'Never' }}</p>
                        </div>
                    </div>
                    @php
                        $activityDot = [
                            'registered' => 'bg-gray-400',
                            'login' => 'bg-blue-500',
                            'order' => 'bg-indigo-500',
                            'message' => 'bg-emerald-500',
                            'review' => 'bg-purple-500',
                        ];
                    @endphp
                    <div x-data="{ expanded: false }">
                    <ol class="relative border-l-2 border-gray-100 ml-1.5 space-y-4">
                        @forelse ($activity as $act)
                            <li class="ml-4 relative" @if ($loop->index >= 5) x-show="expanded" x-cloak @endif>
                                <span class="absolute -left-[22px] top-1 w-3 h-3 rounded-full ring-4 ring-white {{ $activityDot[$act['type']] ?? 'bg-gray-400' }}"></span>
                                <p class="text-[13px] font-medium text-gray-800 leading-snug">{{ $act['label'] }}</p>
                                @if ($act['detail'])
                                    <p class="text-xs text-gray-500 leading-snug">{{ $act['detail'] }}</p>
                                @endif
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $act['time']->format('M d, Y g:i A') }}</p>
                            </li>
                        @empty
                            <li class="ml-4 text-sm text-gray-500">No activity recorded yet.</li>
                        @endforelse
                    </ol>
                    @if ($activity->count() > 5)
                        <button type="button" @click="expanded = !expanded"
                                class="mt-4 w-full flex items-center justify-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                            <span x-show="!expanded">View All ({{ $activity->count() - 5 }} more)</span>
                            <span x-show="expanded" x-cloak>Show Less</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 class="transition-transform duration-200" :class="expanded ? 'rotate-180' : ''">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                    @endif
                    </div>
                </div>
            </div>

            {{-- Main content --}}
            <div class="lg:col-span-6 space-y-6 lg:order-2">

                {{-- Quick Metrics --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Spent</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">৳{{ number_format($totalSpent) }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $completedOrders }} Successful {{ Str::plural('Payment', $completedOrders) }}</p>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Orders</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $totalOrders }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $pendingOrders }} Pending Fulfillment</p>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Average Order</p>
                        <h3 class="text-2xl font-bold text-indigo-600 mt-1">৳{{ number_format($avgOrder) }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Per Completed Order</p>
                    </div>
                </div>

                {{-- Order History / Addresses --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                    {{-- Tabs --}}
                    <div class="border-b border-gray-200 flex space-x-6 px-6 pt-4 bg-gray-50">
                        <button onclick="switchTab('orders')" id="tab-orders" class="pb-3 text-sm font-semibold text-indigo-600 border-b-2 border-indigo-600">
                            Orders ({{ $orders->total() }})
                        </button>
                        <button onclick="switchTab('addresses')" id="tab-addresses" class="pb-3 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Saved Addresses ({{ $addresses->count() }})
                        </button>
                    </div>

                    {{-- Orders tab --}}
                    <div id="content-orders">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        <th class="p-4">Order ID</th>
                                        <th class="p-4">Date</th>
                                        <th class="p-4">Payment</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4">Total</th>
                                        <th class="p-4 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-sm">
                                    @forelse ($orders as $order)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="p-4 font-semibold text-indigo-600">#{{ $order->order_number }}</td>
                                            <td class="p-4 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                            <td class="p-4">
                                                @php
                                                    $payColors = ['paid' => 'bg-emerald-100 text-emerald-800', 'cod' => 'bg-amber-100 text-amber-800', 'refunded' => 'bg-rose-100 text-rose-800', 'unpaid' => 'bg-gray-100 text-gray-600'];
                                                    $payClass = $payColors[$order->payment_status] ?? 'bg-gray-100 text-gray-600';
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $payClass }}">{{ ucfirst($order->payment_status) }}</span>
                                            </td>
                                            <td class="p-4">
                                                @php
                                                    $statusColors = ['pending' => 'bg-amber-100 text-amber-800', 'confirmed' => 'bg-blue-100 text-blue-800', 'processing' => 'bg-purple-100 text-purple-800', 'shipped' => 'bg-teal-100 text-teal-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'delivered' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-rose-100 text-rose-800'];
                                                    $statClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600';
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statClass }}">{{ ucfirst($order->status) }}</span>
                                            </td>
                                            <td class="p-4 font-medium text-gray-900">৳{{ number_format($order->total_price, 2) }}</td>
                                            <td class="p-4 text-center">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-500 hover:text-indigo-600 inline-flex items-center justify-center" title="View Order">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="p-8 text-center text-sm text-gray-500">No orders found for this customer.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($orders->hasPages())
                            <div class="px-4 py-3 border-t border-gray-200">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Addresses tab --}}
                    <div id="content-addresses" class="hidden p-6 space-y-4">
                        @forelse ($addresses as $addr)
                            <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-bold {{ $loop->first ? 'bg-indigo-50 text-indigo-600' : 'bg-gray-100 text-gray-600' }} px-2 py-0.5 rounded">
                                            {{ $loop->first ? 'Default' : ($addr->address_type ? ucfirst($addr->address_type) : 'Other') }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $addr->first_name }} {{ $addr->last_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $addr->address }}, {{ $addr->thana }}, {{ $addr->district }} - {{ $addr->division }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Phone: {{ $addr->phone }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-8">No saved addresses found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function switchTab(tabName) {
                document.getElementById('content-orders').classList.add('hidden');
                document.getElementById('content-addresses').classList.add('hidden');

                const tabs = ['orders', 'addresses'];
                tabs.forEach(tab => {
                    const btn = document.getElementById(`tab-${tab}`);
                    btn.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600', 'font-semibold');
                    btn.classList.add('text-gray-500', 'font-medium');
                });

                document.getElementById(`content-${tabName}`).classList.remove('hidden');
                const activeBtn = document.getElementById(`tab-${tabName}`);
                activeBtn.classList.remove('text-gray-500', 'font-medium');
                activeBtn.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600', 'font-semibold');
            }
        </script>
    @endpush
</x-admin-layout>