<x-admin-layout>
    <div class="mb-6">
        <nav class="text-xs text-gray-400 mb-0.5">
            <span class="text-gray-700">Settings</span>
            <span class="mx-1">/</span>
            <span class="text-gray-500">{{ $tabs[$activeTab] }}</span>
        </nav>
        <h1 class="text-lg font-semibold text-gray-900 leading-tight">Settings</h1>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 overflow-x-auto" aria-label="Settings tabs">
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('admin.settings.index', ['tab' => $key]) }}"
                       class="relative px-5 py-3.5 text-sm font-medium whitespace-nowrap transition-colors
                              {{ $activeTab === $key
                                  ? 'text-indigo-600'
                                  : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                        @if ($activeTab === $key)
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 rounded-t-full"></span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Tab content --}}
        <div class="p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                {{-- ============ GENERAL TAB ============ --}}
                @if ($activeTab === 'general')
                    <div class="space-y-6 max-w-2xl">
                        {{-- Store Name --}}
                        <div>
                            <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1.5">Store name</label>
                            <input type="text" name="store_name" id="store_name"
                                   value="{{ $settings['store_name'] ?? '' }}"
                                   placeholder="e.g. Uppcart"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1.5">Your store name as it appears across the site.</p>
                        </div>

                        {{-- Tagline --}}
                        <div>
                            <label for="store_tagline" class="block text-sm font-medium text-gray-700 mb-1.5">Tagline</label>
                            <input type="text" name="store_tagline" id="store_tagline"
                                   value="{{ $settings['store_tagline'] ?? '' }}"
                                   placeholder="e.g. Quality you can trust"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1.5">A short slogan shown near the logo.</p>
                        </div>

                        {{-- Logo + Favicon --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Logo &amp; Favicon</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                {{-- Logo --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Store logo</label>
                                    <div class="flex items-center gap-4">
                                        <div class="w-20 h-20 rounded-xl border-2 border-dashed border-gray-300 bg-white flex items-center justify-center overflow-hidden shrink-0" id="logo-preview">
                                            @if ($settings['store_logo'] ?? null)
                                                <img src="{{ asset('storage/' . $settings['store_logo']) }}" alt="Logo" class="w-full h-full object-contain">
                                            @else
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <label for="store_logo"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50 cursor-pointer transition">
                                                Choose file
                                            </label>
                                            <input type="file" name="store_logo" id="store_logo" accept="image/*" class="hidden">
                                            <p class="text-[11px] text-gray-400 mt-1">PNG, SVG. Max 2 MB.</p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-xs font-medium text-gray-600 mb-2">Logo size</label>
                                        <div class="flex items-center gap-3">
                                            <input type="range" name="store_logo_size" min="24" max="64"
                                                   value="{{ $settings['store_logo_size'] ?? '32' }}"
                                                   id="logo-size-slider"
                                                   class="flex-1 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                            <span id="logo-size-value" class="text-xs font-mono text-gray-600 w-10 text-right">{{ $settings['store_logo_size'] ?? '32' }}px</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Favicon --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Favicon</label>
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-xl border-2 border-dashed border-gray-300 bg-white flex items-center justify-center overflow-hidden shrink-0" id="favicon-preview">
                                            @if ($settings['store_favicon'] ?? null)
                                                <img src="{{ asset('storage/' . $settings['store_favicon']) }}" alt="Favicon" class="w-full h-full object-contain">
                                            @else
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <label for="store_favicon"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50 cursor-pointer transition">
                                                Choose file
                                            </label>
                                            <input type="file" name="store_favicon" id="store_favicon" accept="image/*" class="hidden">
                                            <p class="text-[11px] text-gray-400 mt-1">32x32 or 64x64 PNG.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Maintenance Mode --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Site status</h4>
                            <div class="space-y-4">
                                {{-- Maintenance Mode --}}
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Maintenance mode</label>
                                        <p class="text-xs text-gray-400 mt-0.5">Show a maintenance page to all visitors except admins.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="maintenance_mode" value="0">
                                        <input type="checkbox" name="maintenance_mode" value="1"
                                               {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                {{-- Coming Soon --}}
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Coming soon</label>
                                        <p class="text-xs text-gray-400 mt-0.5">Show a "coming soon" landing page with a countdown.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="coming_soon" value="0">
                                        <input type="checkbox" name="coming_soon" value="1"
                                               {{ ($settings['coming_soon'] ?? '0') === '1' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                {{-- Launch date --}}
                                <div id="launch-date-row" style="{{ ($settings['coming_soon'] ?? '0') !== '1' ? 'display:none' : '' }}">
                                    <label for="coming_soon_launch_date" class="block text-sm font-medium text-gray-700 mb-1.5">Launch date</label>
                                    <input type="datetime-local" name="coming_soon_launch_date" id="coming_soon_launch_date"
                                           value="{{ $settings['coming_soon_launch_date'] ?? '' }}"
                                           class="w-full max-w-xs rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-400 mt-1.5">Countdown will target this date. If left empty, defaults to 14 days from now.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Currency --}}
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-1.5">Currency</label>
                            <select name="currency" id="currency"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                <option value="BDT" {{ ($settings['currency'] ?? 'BDT') === 'BDT' ? 'selected' : '' }}>BDT (&#2547; Bangladeshi Taka)</option>
                                <option value="USD" {{ ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD ($ US Dollar)</option>
                                <option value="INR" {{ ($settings['currency'] ?? '') === 'INR' ? 'selected' : '' }}>INR (&#8377; Indian Rupee)</option>
                            </select>
                        </div>

                        {{-- Timezone --}}
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1.5">Timezone</label>
                            <select name="timezone" id="timezone"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                <option value="Asia/Dhaka" {{ ($settings['timezone'] ?? '') === 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (GMT+6)</option>
                                <option value="Asia/Kolkata" {{ ($settings['timezone'] ?? '') === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (GMT+5:30)</option>
                                <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                                <option value="America/New_York" {{ ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York (GMT-5)</option>
                                <option value="Europe/London" {{ ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT+0)</option>
                            </select>
                        </div>

                        {{-- WhatsApp Reminder Message --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">Abandoned cart WhatsApp messages</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">Three message templates for the abandoned carts list. Each cart link restores the customer's saved items, and the discount template includes a unique 5% coupon generated per cart.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="abandoned_whatsapp_enabled" value="0">
                                    <input type="checkbox" name="abandoned_whatsapp_enabled" value="1"
                                           {{ ($settings['abandoned_whatsapp_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <label for="abandoned_whatsapp_reminder_message" class="block text-sm font-medium text-gray-700 mb-1.5">1. Reminder message</label>
                                    <textarea name="abandoned_whatsapp_reminder_message" id="abandoned_whatsapp_reminder_message" rows="3"
                                              placeholder="Hi {name}! You have {item_count} item(s) worth {total} in your cart at {store_name}. Ready to complete your order? {cart_link}"
                                              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settings['abandoned_whatsapp_reminder_message'] ?? '' }}</textarea>
                                </div>

                                <div>
                                    <label for="abandoned_whatsapp_stock_message" class="block text-sm font-medium text-gray-700 mb-1.5">2. Stock limited message</label>
                                    <textarea name="abandoned_whatsapp_stock_message" id="abandoned_whatsapp_stock_message" rows="3"
                                              placeholder="Hi {name}! Some items in your cart are now in limited stock. Complete your order soon so they don't sell out: {cart_link}"
                                              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settings['abandoned_whatsapp_stock_message'] ?? '' }}</textarea>
                                </div>

                                <div>
                                    <label for="abandoned_whatsapp_discount_message" class="block text-sm font-medium text-gray-700 mb-1.5">3. 5% discount coupon message</label>
                                    <textarea name="abandoned_whatsapp_discount_message" id="abandoned_whatsapp_discount_message" rows="3"
                                              placeholder="Hi {name}! Complete your order today and get 5% off with code {coupon_code}. Shop now: {cart_link}"
                                              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settings['abandoned_whatsapp_discount_message'] ?? '' }}</textarea>
                                    <p class="text-xs text-gray-400 mt-1.5">Each cart gets its own one-time 5% coupon, valid for 7 days. The code is automatically generated and shown via <code class="text-gray-500">{coupon_code}</code>.</p>
                                </div>
                            </div>

                            <p class="text-xs text-gray-400 mt-4 border-t border-gray-200 pt-3">Placeholders for all templates: <code class="text-gray-500">{name}</code> customer name, <code class="text-gray-500">{phone}</code> customer phone, <code class="text-gray-500">{total}</code> cart total, <code class="text-gray-500">{item_count}</code> number of items, <code class="text-gray-500">{store_name}</code> your store name, <code class="text-gray-500">{cart_link}</code> a link that restores the customer's saved cart, <code class="text-gray-500">{url}</code> your store URL.</p>
                        </div>
                    </div>
                @endif

                {{-- ============ SHIPPING TAB ============ --}}
                @if ($activeTab === 'shipping')
                    @php
                        $savedZones = json_decode($settings['shipping_zones'] ?? '[]', true);
                        if (!is_array($savedZones)) {
                            $savedZones = [];
                        }
                    @endphp
                    <div x-data="{ subTab: 'zone' }" class="space-y-6 max-w-2xl">
                        {{-- Sub-tab navigation --}}
                        <div class="flex gap-1 border-b border-gray-200 -mb-px">
                            <button type="button" @click="subTab = 'zone'"
                                    class="px-4 py-2 text-sm font-medium rounded-t-lg -mb-px border-b-2 transition-colors"
                                    :class="subTab === 'zone' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                                Shipping Zone
                            </button>
                            <button type="button" @click="subTab = 'free'"
                                    class="px-4 py-2 text-sm font-medium rounded-t-lg -mb-px border-b-2 transition-colors"
                                    :class="subTab === 'free' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                                Free Shipping Condition
                            </button>
                            <button type="button" @click="subTab = 'courier'"
                                    class="px-4 py-2 text-sm font-medium rounded-t-lg -mb-px border-b-2 transition-colors"
                                    :class="subTab === 'courier' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                                Courier
                            </button>
                        </div>

                        {{-- Shipping Zone panel --}}
                        <div x-show="subTab === 'zone'">
                            <div x-data="shippingZones()" class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-500">Group delivery areas into zones and assign a shipping method to each zone.</p>
                                    <button type="button" @click="addZone()"
                                            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Add zone
                                    </button>
                                </div>

                                {{-- Zones list --}}
                                <template x-for="(zone, zi) in zones" :key="zi">
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <input type="text" x-model="zone.name" placeholder="Zone name"
                                                   class="text-sm font-semibold text-gray-900 bg-transparent border-0 border-b border-gray-300 px-0 py-0.5 focus:border-indigo-500 focus:ring-0 w-56 placeholder-gray-400">
                                            <button type="button" @click="removeZone(zi)" class="text-gray-400 hover:text-red-500 transition-colors" title="Remove zone">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>

                                        {{-- Zone regions --}}
                                        <div class="mb-4">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Zone regions</label>
                                            <div class="relative" @click.outside="zone.open = false">
                                                <div class="flex flex-wrap gap-1.5 items-center border border-gray-300 bg-white rounded-lg px-3 py-2 cursor-text focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500"
                                                     @click="zone.open = true">
                                                    <template x-for="name in selectedChips(zone)" :key="name">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-medium rounded-md">
                                                            <span x-text="name"></span>
                                                            <button type="button" @click.stop="removeRegion(zone, name)" class="text-indigo-400 hover:text-red-500 transition-colors" title="Remove region">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </span>
                                                    </template>
                                                    <span class="text-sm text-gray-400" x-show="selectedChips(zone).length === 0">Select regions...</span>
                                                    <input type="text" x-model="zone.search"
                                                           placeholder="Search city / thana / district..."
                                                           class="flex-1 min-w-[120px] bg-transparent text-sm outline-none border-0 p-0 focus:ring-0"
                                                           @focus="zone.open = true" @click.stop>
                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-150 shrink-0" :class="zone.open ? 'rotate-180' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </div>

                                                <div x-show="zone.open" x-cloak @click.stop
                                                     class="absolute z-20 mt-1 left-0 right-0 border border-gray-200 bg-white rounded-lg shadow-lg p-2 max-h-72 overflow-y-auto">
                                                    <p class="text-xs text-gray-400 px-1 pb-1" x-show="filteredVisibleFlat(zone).length === 1 && (zone.search || '').trim() !== ''">No matches found.</p>
                                                    <template x-for="node in filteredVisibleFlat(zone)" :key="node.key">
                                                        <div class="flex items-center gap-1 py-0.5"
                                                             :style="'padding-left:' + (node.level * 14) + 'px'">
                                                            <button type="button" x-show="hasChildren(node)"
                                                                    @click="toggleExpanded(node.key)"
                                                                    class="w-4 h-4 flex items-center justify-center text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer"
                                                                    :title="node.expanded ? 'Collapse' : 'Expand'">
                                                                <svg class="w-3 h-3 transition-transform duration-150"
                                                                     :class="node.expanded ? 'rotate-180' : ''"
                                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                                </svg>
                                                            </button>
                                                            <span x-show="!hasChildren(node)" class="w-4 shrink-0"></span>
                                                            <label class="flex items-center gap-2 cursor-pointer select-none min-w-0">
                                                                <input type="checkbox" :checked="isChecked(zone, node.key)"
                                                                       :indeterminate="isIndeterminate(zone, node.key)"
                                                                       @change="toggleNode(zone, node.key)"
                                                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0">
                                                                <span class="text-sm truncate"
                                                                      :class="node.level === 0 ? 'font-semibold text-gray-900' : (node.level === 1 ? 'font-medium text-gray-800' : 'text-gray-600')"
                                                                      x-text="node.name"></span>
                                                            </label>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Shipping methods table --}}
                                        <div class="mb-2 flex items-center justify-between">
                                            <h4 class="text-sm font-semibold text-gray-900">Shipping methods</h4>
                                            <button type="button" @click="openAddMethod(zi)"
                                                    class="flex items-center gap-1.5 px-3 py-1.5 border border-indigo-300 text-indigo-600 hover:bg-indigo-50 text-xs font-medium rounded-lg transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                Add shipping method
                                            </button>
                                        </div>
                                        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                                        <th class="px-3 py-2">Title</th>
                                                        <th class="px-3 py-2">Enabled</th>
                                                        <th class="px-3 py-2">Description</th>
                                                        <th class="px-3 py-2"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    <template x-for="(method, mi) in zone.methods" :key="mi">
                                                        <tr>
                                                            <td class="px-3 py-2 text-sm text-gray-800" x-text="method.title"></td>
                                                            <td class="px-3 py-2">
                                                                <label class="relative inline-flex items-center cursor-pointer">
                                                                    <input type="checkbox" :checked="method.enabled" @change="toggleMethod(zi, mi)" class="sr-only peer">
                                                                    <div class="w-8 h-4.5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                                </label>
                                                            </td>
                                                            <td class="px-3 py-2 text-sm text-gray-500" x-text="method.description"></td>
                                                            <td class="px-3 py-2 text-right">
                                                                <button type="button" @click="removeMethod(zi, mi)" class="text-gray-400 hover:text-red-500 transition-colors" title="Remove">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <tr x-show="!zone.methods || zone.methods.length === 0">
                                                        <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-400">No shipping methods added yet.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="addZone()" x-show="zones.length === 0"
                                        class="flex items-center gap-2 px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition-colors w-full justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Add zone
                                </button>

                                {{-- Hidden input to serialize --}}
                                <input type="hidden" name="shipping_zones" :value="JSON.stringify(serialize())">

                                {{-- Add zone modal --}}
                                <div x-show="showZoneModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4)">
                                    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-5">
                                        <h3 class="text-base font-semibold text-gray-900 mb-4">Add zone</h3>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Zone name</label>
                                                <input type="text" x-model="newZone.name" placeholder="e.g. Inside Dhaka" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <p class="text-xs text-gray-400">You'll pick this zone's regions from the location dropdown after adding it.</p>
                                        </div>
                                        <div class="flex justify-end gap-2 mt-5">
                                            <button type="button" @click="showZoneModal = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</button>
                                            <button type="button" @click="saveZone()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Add zone</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Add method modal --}}
                                <div x-show="showMethodModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4)">
                                    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-5">
                                        <h3 class="text-base font-semibold text-gray-900 mb-4">Add shipping method</h3>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Shipping method type</label>
                                                <select x-model="newMethod.type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    <option value="free">Free Shipping</option>
                                                    <option value="weight_based">Weight Based</option>
                                                    <option value="flat_rate">Flat Rate</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                                                <input type="text" x-model="newMethod.title" placeholder="e.g. Standard Delivery" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                                <input type="text" x-model="newMethod.description" placeholder="e.g. Arrives in 1-2 days" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div x-show="newMethod.type === 'flat_rate'">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Delivery amount (&#2547;)</label>
                                                <input type="number" x-model="newMethod.amount" min="0" step="0.01" placeholder="e.g. 60" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div x-show="newMethod.type === 'weight_based'" class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Amount (&#2547;)</label>
                                                    <input type="number" x-model="newMethod.amount" min="0" step="0.01" placeholder="e.g. 80" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Per kg (&#2547;)</label>
                                                    <input type="number" x-model="newMethod.per_kg" min="0" step="0.01" placeholder="e.g. 20" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" x-model="newMethod.enabled" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="text-sm text-gray-700">Enabled</span>
                                            </label>
                                        </div>
                                        <div class="flex justify-end gap-2 mt-5">
                                            <button type="button" @click="showMethodModal = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</button>
                                            <button type="button" @click="addMethod()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Add method</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Free Shipping Condition panel --}}
                        <div x-show="subTab === 'free'" x-cloak>
                            <div class="border-t border-gray-200 pt-6" x-data="freeShippingConfig()">
                                <h4 class="text-sm font-semibold text-gray-900 mb-1">Free Shipping Condition</h4>
                            <p class="text-xs text-gray-400 mb-4">Choose when customers qualify for free delivery.</p>

                            <input type="hidden" name="free_shipping_type" :value="freeType">
                            <input type="hidden" name="free_shipping_products" :value="JSON.stringify(freeProducts)">
                            <input type="hidden" name="free_shipping_categories" :value="JSON.stringify(freeCategories)">
                            <input type="hidden" name="free_shipping_collections" :value="JSON.stringify(freeCollections)">

                            {{-- Radio options --}}
                            <div class="space-y-2.5 mb-5">
                                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border transition-colors" :class="freeType === 'order_amount' ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="radio" value="order_amount" x-model="freeType" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Order Amount</div>
                                        <div class="text-xs text-gray-500">Free shipping when order total reaches a minimum amount</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border transition-colors" :class="freeType === 'specific_products' ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="radio" value="specific_products" x-model="freeType" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Specific Products</div>
                                        <div class="text-xs text-gray-500">Free shipping on selected products</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border transition-colors" :class="freeType === 'specific_categories' ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="radio" value="specific_categories" x-model="freeType" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Specific Categories</div>
                                        <div class="text-xs text-gray-500">Free shipping on products from selected categories</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border transition-colors" :class="freeType === 'specific_collections' ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="radio" value="specific_collections" x-model="freeType" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Specific Collections</div>
                                        <div class="text-xs text-gray-500">Free shipping on products from selected collections</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border transition-colors" :class="freeType === 'product_weight' ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="radio" value="product_weight" x-model="freeType" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Product Weight</div>
                                        <div class="text-xs text-gray-500">Free shipping when cart weight stays within a maximum</div>
                                    </div>
                                </label>
                            </div>

                            {{-- Conditional panels --}}
                            <template x-if="freeType === 'order_amount'">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Minimum order amount (&#2547;)</label>
                                    <input type="number" name="free_shipping_threshold"
                                           value="{{ $settings['free_shipping_threshold'] ?? '' }}"
                                           placeholder="e.g. 500" min="0" step="1"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-400 mt-1.5">Orders above this amount get free delivery.</p>
                                </div>
                            </template>

                            <template x-if="freeType === 'specific_products'">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <input type="text" x-model="productSearch" @focus="loadProducts()" placeholder="Search products..."
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-3 focus:border-indigo-500 focus:ring-indigo-500">
                                    <div class="max-h-48 overflow-y-auto space-y-1">
                                        <template x-for="p in filteredProducts()" :key="p.id">
                                            <label class="flex items-center gap-2 p-2 rounded cursor-pointer hover:bg-white" :class="freeProducts.includes(p.id) ? 'bg-indigo-50' : ''">
                                                <input type="checkbox" :value="p.id" :checked="freeProducts.includes(p.id)" @change="toggleProduct(p.id)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="text-sm text-gray-700" x-text="p.name"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2" x-text="freeProducts.length + ' product(s) selected'"></p>
                                </div>
                            </template>

                            <template x-if="freeType === 'specific_categories'">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <input type="text" x-model="categorySearch" @focus="loadCategories()" placeholder="Search categories..."
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-3 focus:border-indigo-500 focus:ring-indigo-500">
                                    <div class="max-h-48 overflow-y-auto space-y-1">
                                        <template x-for="c in filteredCategories()" :key="c.id">
                                            <label class="flex items-center gap-2 p-2 rounded cursor-pointer hover:bg-white" :class="freeCategories.includes(c.id) ? 'bg-indigo-50' : ''">
                                                <input type="checkbox" :value="c.id" :checked="freeCategories.includes(c.id)" @change="toggleCategory(c.id)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="text-sm text-gray-700" x-text="c.name"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2" x-text="freeCategories.length + ' category(ies) selected'"></p>
                                </div>
                            </template>

                            <template x-if="freeType === 'specific_collections'">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <input type="text" x-model="collectionSearch" @focus="loadCollections()" placeholder="Search collections..."
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-3 focus:border-indigo-500 focus:ring-indigo-500">
                                    <div class="max-h-48 overflow-y-auto space-y-1">
                                        <template x-for="c in filteredCollections()" :key="c.id">
                                            <label class="flex items-center gap-2 p-2 rounded cursor-pointer hover:bg-white" :class="freeCollections.includes(c.id) ? 'bg-indigo-50' : ''">
                                                <input type="checkbox" :value="c.id" :checked="freeCollections.includes(c.id)" @change="toggleCollection(c.id)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="text-sm text-gray-700" x-text="c.name"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2" x-text="freeCollections.length + ' collection(s) selected'"></p>
                                </div>
                            </template>

                            <template x-if="freeType === 'product_weight'">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Maximum weight (kg)</label>
                                    <input type="number" name="free_shipping_weight_threshold"
                                           value="{{ $settings['free_shipping_weight_threshold'] ?? '' }}"
                                           placeholder="e.g. 2.00" min="0" step="0.01"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-400 mt-1.5">Carts up to this total weight get free delivery. Delivery charge applies once cart weight exceeds the limit. Set product weights on the product edit page.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                        {{-- Courier panel --}}
                        <div x-show="subTab === 'courier'" x-cloak>
                            <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Courier</h4>
                            <p class="text-xs text-gray-400 mb-5">Connect third-party courier services for automated order forwarding.</p>

                            {{-- Steadfast --}}
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-10 h-6 rounded-md text-[10px] font-bold text-white bg-[#004B87]">SF</span>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">Steadfast Courier</h4>
                                            <p class="text-[11px] text-gray-400">Deliver parcels nationwide via Steadfast.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="courier_steadfast_enabled" value="1"
                                               {{ ($settings['courier_steadfast_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div x-data="{ show: false }">
                                        <label for="courier_steadfast_api_key" class="block text-xs font-medium text-gray-600 mb-1">API Key</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="courier_steadfast_api_key" id="courier_steadfast_api_key"
                                                   value="{{ $settings['courier_steadfast_api_key'] ?? '' }}"
                                                   placeholder="Your Steadfast API key"
                                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div x-data="{ show: false }">
                                        <label for="courier_steadfast_secret" class="block text-xs font-medium text-gray-600 mb-1">Secret Key</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="courier_steadfast_secret" id="courier_steadfast_secret"
                                                   value="{{ $settings['courier_steadfast_secret'] ?? '' }}"
                                                   placeholder="Your Steadfast secret key"
                                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="courier_steadfast_merchant_id" class="block text-xs font-medium text-gray-600 mb-1">Merchant ID</label>
                                    <input type="text" name="courier_steadfast_merchant_id" id="courier_steadfast_merchant_id"
                                           value="{{ $settings['courier_steadfast_merchant_id'] ?? '' }}"
                                           placeholder="Your Steadfast merchant ID"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            {{-- Pathao Courier --}}
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-10 h-6 rounded-md text-[10px] font-bold text-white bg-[#ED1C24]">PA</span>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">Pathao Courier</h4>
                                            <p class="text-[11px] text-gray-400">Deliver parcels via Pathao's logistics network.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="courier_pathao_enabled" value="1"
                                               {{ ($settings['courier_pathao_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label for="courier_pathao_merchant_id" class="block text-xs font-medium text-gray-600 mb-1">Client ID</label>
                                        <input type="text" name="courier_pathao_merchant_id" id="courier_pathao_merchant_id"
                                               value="{{ $settings['courier_pathao_merchant_id'] ?? '' }}"
                                               placeholder="Your Pathao client ID"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div x-data="{ show: false }">
                                        <label for="courier_pathao_secret_key" class="block text-xs font-medium text-gray-600 mb-1">Client Secret</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="courier_pathao_secret_key" id="courier_pathao_secret_key"
                                                   value="{{ $settings['courier_pathao_secret_key'] ?? '' }}"
                                                   placeholder="Your Pathao client secret"
                                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="courier_pathao_store_id" class="block text-xs font-medium text-gray-600 mb-1">Store ID</label>
                                        <input type="number" name="courier_pathao_store_id" id="courier_pathao_store_id"
                                               value="{{ $settings['courier_pathao_store_id'] ?? '' }}"
                                               placeholder="Pathao store ID"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="courier_pathao_api_url" class="block text-xs font-medium text-gray-600 mb-1">API Base URL</label>
                                    <input type="url" name="courier_pathao_api_url" id="courier_pathao_api_url"
                                           value="{{ $settings['courier_pathao_api_url'] ?? 'https://merchant-api-live.pathao.com' }}"
                                           placeholder="https://merchant-api-live.pathao.com"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-[11px] text-gray-400 mt-1">Use <code class="bg-gray-100 px-1 rounded">https://merchant-api-sandbox.pathao.com</code> for testing.</p>
                                </div>
                            </div>

                            {{-- RedX Courier --}}
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-10 h-6 rounded-md text-[10px] font-bold text-white bg-[#E4002B]">RX</span>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">RedX Courier</h4>
                                            <p class="text-[11px] text-gray-400">Deliver parcels via RedX logistics network.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="courier_redx_enabled" value="1"
                                               {{ ($settings['courier_redx_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div x-data="{ show: false }">
                                        <label for="courier_redx_api_key" class="block text-xs font-medium text-gray-600 mb-1">API Token</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="courier_redx_api_key" id="courier_redx_api_key"
                                                   value="{{ $settings['courier_redx_api_key'] ?? '' }}"
                                                   placeholder="Your RedX API token"
                                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="courier_redx_store_id" class="block text-xs font-medium text-gray-600 mb-1">Store ID</label>
                                        <input type="text" name="courier_redx_store_id" id="courier_redx_store_id"
                                               value="{{ $settings['courier_redx_store_id'] ?? '' }}"
                                               placeholder="Your RedX store ID"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="courier_redx_api_url" class="block text-xs font-medium text-gray-600 mb-1">API Base URL</label>
                                    <input type="url" name="courier_redx_api_url" id="courier_redx_api_url"
                                           value="{{ $settings['courier_redx_api_url'] ?? 'https://openapi.redx.com.bd/v1.0.0-beta' }}"
                                           placeholder="https://openapi.redx.com.bd/v1.0.0-beta"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ============ PAYMENTS TAB ============ --}}
                @if ($activeTab === 'payments')
                    <div class="space-y-6 max-w-2xl">
                        <p class="text-sm text-gray-500 mb-2">Configure payment methods available to customers.</p>

                        @php
                            $paymentGateways = [
                                'bkash' => [
                                    'name' => 'bKash',
                                    'color' => '#E2136E',
                                    'badge' => 'bKash',
                                    'fields' => [['key' => 'payment_bkash_number', 'label' => 'Merchant number', 'placeholder' => '01XXXXXXXXX', 'type' => 'text']],
                                ],
                                'nagad' => [
                                    'name' => 'Nagad',
                                    'color' => '#F6921E',
                                    'badge' => 'Nagad',
                                    'fields' => [['key' => 'payment_nagad_number', 'label' => 'Merchant number', 'placeholder' => '01XXXXXXXXX', 'type' => 'text']],
                                ],
                                'rocket' => [
                                    'name' => 'Rocket',
                                    'color' => '#8C3494',
                                    'badge' => 'Rkt',
                                    'fields' => [['key' => 'payment_rocket_number', 'label' => 'Merchant number', 'placeholder' => '01XXXXXXXXX', 'type' => 'text']],
                                ],
                                'cod' => [
                                    'name' => 'Cash on Delivery',
                                    'color' => '#0B4F3C',
                                    'badge' => 'COD',
                                    'fields' => [],
                                ],
                                'card' => [
                                    'name' => 'Card / Visa / Mastercard',
                                    'color' => '#2A2E43',
                                    'badge' => 'VISA',
                                    'fields' => [],
                                ],
                            ];
                        @endphp

                        {{-- Mobile Money & Manual Gateways --}}
                        @foreach ($paymentGateways as $key => $gateway)
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-4 mb-3">
                                    {{-- Logo --}}
                                    <div class="w-12 h-12 rounded-lg border-2 border-dashed border-gray-300 bg-white flex items-center justify-center overflow-hidden shrink-0 relative group" id="{{ $key }}-logo-preview">
                                        @if ($settings["payment_{$key}_logo"] ?? null)
                                            <img src="{{ asset('storage/' . $settings["payment_{$key}_logo"]) }}" alt="{{ $gateway['name'] }}" class="w-full h-full object-contain">
                                        @else
                                            <span class="inline-flex items-center justify-center w-full h-full rounded-md text-[10px] font-bold text-white" style="background: {{ $gateway['color'] }}">{{ $gateway['badge'] }}</span>
                                        @endif
                                    </div>
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $gateway['name'] }}</h4>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="payment_{{ $key }}_enabled" value="1"
                                                       {{ ($settings["payment_{$key}_enabled"] ?? ($key === 'cod' ? '1' : '0')) === '1' ? 'checked' : '' }}
                                                       class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                            </label>
                                        </div>
                                        <input type="text" name="payment_{{ $key }}_description"
                                               value="{{ $settings["payment_{$key}_description"] ?? '' }}"
                                               placeholder="Short description shown to customers..."
                                               class="w-full text-xs text-gray-500 bg-transparent border-0 border-b border-gray-200 px-0 py-1 mt-1 focus:border-indigo-500 focus:ring-0 placeholder-gray-400">
                                    </div>
                                </div>

                                {{-- Logo upload --}}
                                <div class="flex items-center gap-3 mb-3 ml-16">
                                    <label for="payment_{{ $key }}_logo"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 border border-gray-300 text-gray-600 rounded-md text-[11px] font-medium hover:bg-gray-50 cursor-pointer transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ ($settings["payment_{$key}_logo"] ?? null) ? 'Change logo' : 'Upload logo' }}
                                    </label>
                                    <input type="file" name="payment_{{ $key }}_logo" id="payment_{{ $key }}_logo" accept="image/*" class="hidden">
                                    @if ($settings["payment_{$key}_logo"] ?? null)
                                        <span class="text-[11px] text-gray-400">Current logo saved</span>
                                    @endif
                                </div>

                                {{-- Extra fields --}}
                                @if (!empty($gateway['fields']))
                                    <div class="ml-16 space-y-3">
                                        @foreach ($gateway['fields'] as $field)
                                            <div>
                                                <label for="{{ $field['key'] }}" class="block text-xs font-medium text-gray-600 mb-1">{{ $field['label'] }}</label>
                                                <input type="{{ $field['type'] }}" name="{{ $field['key'] }}" id="{{ $field['key'] }}"
                                                       value="{{ $settings[$field['key']] ?? '' }}"
                                                       placeholder="{{ $field['placeholder'] }}"
                                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @endforeach

                        {{-- SSLCommerZ --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-12 h-12 rounded-lg border-2 border-dashed border-gray-300 bg-white flex items-center justify-center overflow-hidden shrink-0" id="sslcommerz-logo-preview">
                                    @if ($settings['payment_sslcommerz_logo'] ?? null)
                                        <img src="{{ asset('storage/' . $settings['payment_sslcommerz_logo']) }}" alt="SSLCommerZ" class="w-full h-full object-contain">
                                    @else
                                        <span class="inline-flex items-center justify-center w-full h-full rounded-md text-[9px] font-bold text-white bg-[#1A3C6E] leading-tight text-center">SSL<br>COMMERZ</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-semibold text-gray-900">SSLCommerZ</h4>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="payment_sslcommerz_enabled" value="1"
                                                   {{ ($settings['payment_sslcommerz_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                    <input type="text" name="payment_sslcommerz_description"
                                           value="{{ $settings['payment_sslcommerz_description'] ?? 'Accept Visa, Mastercard, AMEX, bKash, Nagad, and internet banking via SSLCommerZ' }}"
                                           placeholder="Short description shown to customers..."
                                           class="w-full text-xs text-gray-500 bg-transparent border-0 border-b border-gray-200 px-0 py-1 mt-1 focus:border-indigo-500 focus:ring-0 placeholder-gray-400">
                                </div>
                            </div>

                            {{-- Logo upload --}}
                            <div class="flex items-center gap-3 mb-4 ml-16">
                                <label for="payment_sslcommerz_logo"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 border border-gray-300 text-gray-600 rounded-md text-[11px] font-medium hover:bg-gray-50 cursor-pointer transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ ($settings['payment_sslcommerz_logo'] ?? null) ? 'Change logo' : 'Upload logo' }}
                                </label>
                                <input type="file" name="payment_sslcommerz_logo" id="payment_sslcommerz_logo" accept="image/*" class="hidden">
                            </div>

                            {{-- Sandbox toggle --}}
                            <div class="ml-16 mb-3 flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Sandbox mode</label>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Enable for testing. Disable for live payments.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="payment_sslcommerz_sandbox" value="1"
                                           {{ ($settings['payment_sslcommerz_sandbox'] ?? '1') === '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            {{-- Credentials --}}
                            <div class="ml-16 space-y-3">
                                <div>
                                    <label for="payment_sslcommerz_store_id" class="block text-xs font-medium text-gray-600 mb-1">Store ID</label>
                                    <input type="text" name="payment_sslcommerz_store_id" id="payment_sslcommerz_store_id"
                                           value="{{ $settings['payment_sslcommerz_store_id'] ?? '' }}"
                                           placeholder="Your SSLCommerZ store ID"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="payment_sslcommerz_store_passwd" class="block text-xs font-medium text-gray-600 mb-1">Store Password</label>
                                    <input type="password" name="payment_sslcommerz_store_passwd" id="payment_sslcommerz_store_passwd"
                                           value="{{ $settings['payment_sslcommerz_store_passwd'] ?? '' }}"
                                           placeholder="Your SSLCommerZ store password"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ============ INTEGRATION TAB ============ --}}
                @if ($activeTab === 'integration')
                    <div class="space-y-6 max-w-2xl">
                        <p class="text-sm text-gray-500 mb-2">Connect third-party services and APIs.</p>

                        {{-- SMS Gateway --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">SMS Gateway</h4>
                            <p class="text-xs text-gray-400 mb-4">Send order notifications via SMS.</p>
                            <div class="space-y-4">
                                <div>
                                    <label for="sms_provider" class="block text-xs font-medium text-gray-600 mb-1">Provider</label>
                                    <select name="sms_provider" id="sms_provider"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                        <option value="">None</option>
                                        <option value="twilio" {{ ($settings['sms_provider'] ?? '') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                                        <option value="nexmo" {{ ($settings['sms_provider'] ?? '') === 'nexmo' ? 'selected' : '' }}>Nexmo / Vonage</option>
                                        <option value="custom" {{ ($settings['sms_provider'] ?? '') === 'custom' ? 'selected' : '' }}>Custom API</option>
                                    </select>
                                </div>
                                <div x-data="{ show: false }">
                                    <label for="sms_api_key" class="block text-xs font-medium text-gray-600 mb-1">API key</label>
                                    <div class="relative">
                                        <input :type="show ? 'text' : 'password'" name="sms_api_key" id="sms_api_key"
                                               value="{{ $settings['sms_api_key'] ?? '' }}"
                                               placeholder="Enter API key"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div x-data="{ show: false }">
                                    <label for="sms_api_secret" class="block text-xs font-medium text-gray-600 mb-1">API secret</label>
                                    <div class="relative">
                                        <input :type="show ? 'text' : 'password'" name="sms_api_secret" id="sms_api_secret"
                                               value="{{ $settings['sms_api_secret'] ?? '' }}"
                                               placeholder="Enter API secret"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Google Analytics --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Google Analytics</h4>
                            <p class="text-xs text-gray-400 mb-4">Track visitors and conversion events.</p>
                            <div class="space-y-4">
                                <div>
                                    <label for="google_analytics_id" class="block text-xs font-medium text-gray-600 mb-1">Measurement ID</label>
                                    <input type="text" name="google_analytics_id" id="google_analytics_id"
                                           value="{{ $settings['google_analytics_id'] ?? '' }}"
                                           placeholder="G-XXXXXXXXXX"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="google_tag_manager_id" class="block text-xs font-medium text-gray-600 mb-1">Google Tag Manager Container ID</label>
                                    <input type="text" name="google_tag_manager_id" id="google_tag_manager_id"
                                           value="{{ $settings['google_tag_manager_id'] ?? '' }}"
                                           placeholder="GTM-XXXXXXX"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-[11px] text-gray-400 mt-1">Found in your GTM admin under Containers.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Facebook Pixel --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Facebook Pixel</h4>
                            <p class="text-xs text-gray-400 mb-4">Track conversions from Facebook ads.</p>
                            <div>
                                <label for="facebook_pixel_id" class="block text-xs font-medium text-gray-600 mb-1">Pixel ID</label>
                                <input type="text" name="facebook_pixel_id" id="facebook_pixel_id"
                                       value="{{ $settings['facebook_pixel_id'] ?? '' }}"
                                       placeholder="123456789012345"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        {{-- Google reCAPTCHA --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Google reCAPTCHA</h4>
                            <p class="text-xs text-gray-400 mb-4">Protect forms from spam and abuse.</p>
                            <div class="space-y-4">
                                <div>
                                    <label for="recaptcha_site_key" class="block text-xs font-medium text-gray-600 mb-1">Site key</label>
                                    <input type="text" name="recaptcha_site_key" id="recaptcha_site_key"
                                           value="{{ $settings['recaptcha_site_key'] ?? '' }}"
                                           placeholder="6Le..."
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="recaptcha_secret_key" class="block text-xs font-medium text-gray-600 mb-1">Secret key</label>
                                    <input type="password" name="recaptcha_secret_key" id="recaptcha_secret_key"
                                           value="{{ $settings['recaptcha_secret_key'] ?? '' }}"
                                           placeholder="6Le..."
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        {{-- Fraud Check API --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Fraud Check API</h4>
                            <p class="text-xs text-gray-400 mb-4">Verify orders against BD Courier's fraud detection API.</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="fraud_check_enabled" value="1"
                                               {{ ($settings['fraud_check_enabled'] ?? '') === '1' ? 'checked' : '' }}
                                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-700">Enable fraud checking</span>
                                    </label>
                                </div>
                                <div>
                                    <label for="fraud_check_api_url" class="block text-xs font-medium text-gray-600 mb-1">API URL</label>
                                    <input type="url" name="fraud_check_api_url" id="fraud_check_api_url"
                                           value="{{ $settings['fraud_check_api_url'] ?? 'https://api.bdcourier.com' }}"
                                           placeholder="https://api.bdcourier.com"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div x-data="{ show: false }">
                                    <label for="fraud_check_api_key" class="block text-xs font-medium text-gray-600 mb-1">API Key</label>
                                    <div class="relative">
                                        <input :type="show ? 'text' : 'password'" name="fraud_check_api_key" id="fraud_check_api_key"
                                               value="{{ $settings['fraud_check_api_key'] ?? '' }}"
                                               placeholder="Enter API key"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Social Login (OAuth) --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Social Login (Google &amp; Facebook)</h4>
                            <p class="text-xs text-gray-400 mb-4">Enable "Login with Google/Facebook" on your storefront. Create apps at the respective developer consoles, then paste the credentials below.</p>

                            {{-- Google --}}
                            <div class="mb-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12.545 10.239v3.821h5.445c-.712 2.315-2.647 3.972-5.445 3.972a6.033 6.033 0 110-12.064c1.498 0 2.866.549 3.921 1.453l2.814-2.814A9.969 9.969 0 0012.545 2C7.021 2 2.543 6.477 2.543 12s4.478 10 10.002 10c8.396 0 10.249-7.85 9.426-11.748l-9.426-.013z"/>
                                    </svg>
                                    <h5 class="text-sm font-semibold text-gray-800">Google</h5>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label for="google_oauth_client_id" class="block text-xs font-medium text-gray-600 mb-1">Client ID</label>
                                        <input type="text" name="google_oauth_client_id" id="google_oauth_client_id"
                                               value="{{ $settings['google_oauth_client_id'] ?? '' }}"
                                               placeholder="xxxxxxxx.apps.googleusercontent.com"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div x-data="{ show: false }">
                                        <label for="google_oauth_client_secret" class="block text-xs font-medium text-gray-600 mb-1">Client Secret</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="google_oauth_client_secret" id="google_oauth_client_secret"
                                                   value="{{ $settings['google_oauth_client_secret'] ?? '' }}"
                                                   placeholder="GOCSPX-..."
                                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="google_oauth_redirect_uri" class="block text-xs font-medium text-gray-600 mb-1">Authorized Redirect URI</label>
                                        <input type="url" name="google_oauth_redirect_uri" id="google_oauth_redirect_uri"
                                               value="{{ $settings['google_oauth_redirect_uri'] ?? url('auth/google/callback') }}"
                                               placeholder="{{ url('auth/google/callback') }}"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-[11px] text-gray-400 mt-1">Add this exact URL in your Google Cloud Console "Authorized redirect URIs".</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Facebook --}}
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-4 h-4 text-[#1877F2]" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <h5 class="text-sm font-semibold text-gray-800">Facebook</h5>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label for="facebook_oauth_client_id" class="block text-xs font-medium text-gray-600 mb-1">App ID</label>
                                        <input type="text" name="facebook_oauth_client_id" id="facebook_oauth_client_id"
                                               value="{{ $settings['facebook_oauth_client_id'] ?? '' }}"
                                               placeholder="1234567890"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div x-data="{ show: false }">
                                        <label for="facebook_oauth_client_secret" class="block text-xs font-medium text-gray-600 mb-1">App Secret</label>
                                        <div class="relative">
                                            <input :type="show ? 'text' : 'password'" name="facebook_oauth_client_secret" id="facebook_oauth_client_secret"
                                                   value="{{ $settings['facebook_oauth_client_secret'] ?? '' }}"
                                                   placeholder="xxxxxxxxxxxxxxxx"
                                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="facebook_oauth_redirect_uri" class="block text-xs font-medium text-gray-600 mb-1">Valid OAuth Redirect URI</label>
                                        <input type="url" name="facebook_oauth_redirect_uri" id="facebook_oauth_redirect_uri"
                                               value="{{ $settings['facebook_oauth_redirect_uri'] ?? url('auth/facebook/callback') }}"
                                               placeholder="{{ url('auth/facebook/callback') }}"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-[11px] text-gray-400 mt-1">Add this exact URL in your Facebook app settings under "Valid OAuth Redirect URIs".</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ============ SEO TAB ============ --}}
                @if ($activeTab === 'seo')
                    <div class="space-y-6 max-w-2xl">
                        <p class="text-sm text-gray-500 mb-2">Default SEO settings for your store. These can be overridden per page/product.</p>

                        {{-- Meta Title --}}
                        <div>
                            <label for="seo_meta_title" class="block text-sm font-medium text-gray-700 mb-1.5">Default meta title</label>
                            <input type="text" name="seo_meta_title" id="seo_meta_title"
                                   value="{{ $settings['seo_meta_title'] ?? '' }}"
                                   placeholder="e.g. Uppcart â€” Online Store"
                                   maxlength="70"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1.5"><span class="seo-title-count">70</span> characters recommended. Shown in browser tabs and Google results.</p>
                        </div>

                        {{-- Meta Description --}}
                        <div>
                            <label for="seo_meta_description" class="block text-sm font-medium text-gray-700 mb-1.5">Default meta description</label>
                            <textarea name="seo_meta_description" id="seo_meta_description" rows="3"
                                      placeholder="A short description of your store for search engines..."
                                      maxlength="160"
                                      class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none">{{ $settings['seo_meta_description'] ?? '' }}</textarea>
                            <p class="text-xs text-gray-400 mt-1.5"><span class="seo-desc-count">160</span> characters recommended.</p>
                        </div>

                        {{-- OG Image --}}
                        <div>
                            <label for="seo_og_image" class="block text-sm font-medium text-gray-700 mb-1.5">Default social share image (URL)</label>
                            <input type="url" name="seo_og_image" id="seo_og_image"
                                   value="{{ $settings['seo_og_image'] ?? '' }}"
                                   placeholder="https://example.com/og-image.png"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1.5">Recommended: 1200x630px. Used when your store is shared on social media.</p>
                        </div>

                        {{-- Canonical URL --}}
                        <div>
                            <label for="seo_canonical_url" class="block text-sm font-medium text-gray-700 mb-1.5">Canonical URL</label>
                            <input type="url" name="seo_canonical_url" id="seo_canonical_url"
                                   value="{{ $settings['seo_canonical_url'] ?? '' }}"
                                   placeholder="https://uppcart.com"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1.5">The preferred URL for your store. Used to prevent duplicate content issues.</p>
                        </div>

                        {{-- Google Search Console --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Google Search Console</h4>
                            <p class="text-xs text-gray-400 mb-4">Verify your site with Google and submit sitemaps.</p>
                            <div>
                                <label for="google_verification_code" class="block text-xs font-medium text-gray-600 mb-1">Verification code</label>
                                <input type="text" name="google_verification_code" id="google_verification_code"
                                       value="{{ $settings['google_verification_code'] ?? '' }}"
                                       placeholder="Paste your verification meta tag content"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        {{-- Robots --}}
                        <div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Allow search engine indexing</label>
                                    <p class="text-xs text-gray-400 mt-0.5">Disable to add <code class="bg-gray-100 px-1 rounded">noindex</code> to your store.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="seo_indexable" value="1"
                                           {{ ($settings['seo_indexable'] ?? '1') === '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ============ CONTACT TAB ============ --}}
                @if ($activeTab === 'contact')
                    @php
                        $chatFaqs = json_decode($settings['live_chat_faqs'] ?? '[]', true);
                        if (empty($chatFaqs) || ! is_array($chatFaqs)) {
                            $chatFaqs = [
                                ['q' => 'How long does delivery take?', 'a' => 'Delivery usually takes 2â€“4 business days inside Dhaka and 3â€“6 business days outside Dhaka.'],
                                ['q' => 'How can I pay?', 'a' => 'We accept Cash on Delivery (COD), bKash, Nagad, Rocket, and card payments.'],
                                ['q' => 'Can I return or exchange a product?', 'a' => 'Yes, you can return or exchange within 3 days of delivery if the product is unused and in original condition.'],
                                ['q' => 'How do I track my order?', 'a' => 'Use the "Track Order" page with your order number and phone number.'],
                                ['q' => 'Can I cancel my order?', 'a' => 'Orders can be cancelled before they are shipped. Message us and we will help you.'],
                            ];
                        }
                    @endphp
                    <div class="space-y-6 max-w-2xl">
                        <p class="text-sm text-gray-500 mb-2">Configure how customers can reach you.</p>

                        {{-- Contact Email --}}
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1.5">Contact email</label>
                            <input type="email" name="contact_email" id="contact_email"
                                   value="{{ $settings['contact_email'] ?? '' }}"
                                   placeholder="support@uppcart.com"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1.5">Public-facing support email shown on the contact page.</p>
                        </div>

                        {{-- Contact Phone --}}
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1.5">Contact phone</label>
                            <input type="tel" name="contact_phone" id="contact_phone"
                                   value="{{ $settings['contact_phone'] ?? '' }}"
                                   placeholder="01XXXXXXXXX"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- WhatsApp --}}
                        <div>
                            <label for="contact_whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp phone</label>
                            <input type="tel" name="contact_whatsapp" id="contact_whatsapp"
                                   value="{{ $settings['contact_whatsapp'] ?? '' }}"
                                   inputmode="numeric" pattern="[0-9+]*"
                                   oninput="this.value = this.value.replace(/[^\d+]/g, '').replace(/(?!^)\+/g, '')"
                                   placeholder="+8801877774081"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1.5">Used for the WhatsApp chat button. Include country code, no spaces.</p>
                        </div>

                        {{-- Facebook --}}
                        <div>
                            <label for="contact_facebook" class="block text-sm font-medium text-gray-700 mb-1.5">Facebook page URL</label>
                            <input type="url" name="contact_facebook" id="contact_facebook"
                                   value="{{ $settings['contact_facebook'] ?? '' }}"
                                   placeholder="https://facebook.com/yourpage"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Instagram --}}
                        <div>
                            <label for="contact_instagram" class="block text-sm font-medium text-gray-700 mb-1.5">Instagram profile URL</label>
                            <input type="url" name="contact_instagram" id="contact_instagram"
                                   value="{{ $settings['contact_instagram'] ?? '' }}"
                                   placeholder="https://instagram.com/yourhandle"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- YouTube --}}
                        <div>
                            <label for="contact_youtube" class="block text-sm font-medium text-gray-700 mb-1.5">YouTube channel URL</label>
                            <input type="url" name="contact_youtube" id="contact_youtube"
                                   value="{{ $settings['contact_youtube'] ?? '' }}"
                                   placeholder="https://youtube.com/@yourchannel"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- TikTok --}}
                        <div>
                            <label for="contact_tiktok" class="block text-sm font-medium text-gray-700 mb-1.5">TikTok profile URL</label>
                            <input type="url" name="contact_tiktok" id="contact_tiktok"
                                   value="{{ $settings['contact_tiktok'] ?? '' }}"
                                   placeholder="https://tiktok.com/@yourhandle"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Chat Bubbles --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-5"
                             x-data="{
                                 chatWhatsapp: {{ ($settings['chat_bubble_whatsapp'] ?? '0') === '1' ? 'true' : 'false' }},
                                 chatMessenger: {{ ($settings['chat_bubble_messenger'] ?? '0') === '1' ? 'true' : 'false' }},
                                 chatCustom: {{ ($settings['chat_bubble_custom'] ?? '0') === '1' ? 'true' : 'false' }},
                             }">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Chat Bubbles</h4>
                            <p class="text-xs text-gray-400 mb-4">Show floating chat buttons on your storefront for instant customer support.</p>
                            <div class="space-y-4">
                                {{-- WhatsApp --}}
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-[#25D366]/10 text-[#25D366]">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        </span>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">WhatsApp chat bubble</label>
                                            <p class="text-[11px] text-gray-400 mt-0.5">Uses the chat bubble number below.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="chat_bubble_whatsapp" value="1"
                                               x-model="chatWhatsapp"
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                {{-- WhatsApp chat bubble number --}}
                                <div x-show="chatWhatsapp" x-cloak>
                                    <label for="chat_bubble_whatsapp_number" class="block text-xs font-medium text-gray-600 mb-1">WhatsApp chat bubble number</label>
                                    <input type="tel" name="chat_bubble_whatsapp_number" id="chat_bubble_whatsapp_number"
                                           value="{{ $settings['chat_bubble_whatsapp_number'] ?? '' }}"
                                           inputmode="numeric" pattern="[0-9+]*"
                                           oninput="this.value = this.value.replace(/[^\d+]/g, '').replace(/(?!^)\+/g, '')"
                                           placeholder="+8801877774081"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-[11px] text-gray-400 mt-1">Include country code, no spaces. Leave empty to use the WhatsApp phone from above.</p>
                                </div>

                                {{-- Messenger --}}
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-[#0084FF]/10 text-[#0084FF]">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.36 2 2 6.13 2 11.7c0 2.91 1.2 5.42 3.15 7.17V22l3.04-1.67c.85.24 1.76.37 2.81.37 5.64 0 10-4.13 10-9.7S17.64 2 12 2zm1.08 13.05l-2.55-2.73L5.8 15.1l5.46-5.79 2.6 2.73 3.68-2.73-5.46 5.74z"/></svg>
                                        </span>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Messenger chat bubble</label>
                                            <p class="text-[11px] text-gray-400 mt-0.5">Facebook Messenger instant chat.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="chat_bubble_messenger" value="1"
                                               x-model="chatMessenger"
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                {{-- Messenger Page ID or username --}}
                                <div x-show="chatMessenger" x-cloak>
                                    <label for="chat_bubble_messenger_page_id" class="block text-xs font-medium text-gray-600 mb-1">Messenger Page ID or username</label>
                                    <input type="text" name="chat_bubble_messenger_page_id" id="chat_bubble_messenger_page_id"
                                           value="{{ $settings['chat_bubble_messenger_page_id'] ?? '' }}"
                                           placeholder="e.g. yourpage or page ID"
                                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-[11px] text-gray-400 mt-1">m.me/yourpage â€” use your page username or numeric ID.</p>
                                </div>

                                {{-- Live chat --}}
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                        </span>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Live chat bubble</label>
                                            <p class="text-[11px] text-gray-400 mt-0.5">In-app chat with a bot and customer agents. Messages appear on the customer profile.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="chat_bubble_custom" value="1"
                                               x-model="chatCustom"
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Live Chat FAQs --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-5" x-data="liveChatFaqs()">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Live Chat FAQs</h4>
                            <p class="text-xs text-gray-400 mb-4">Common questions shown to customers inside the live chat widget. Customers can tap these before connecting with an agent.</p>

                            <template x-for="(faq, idx) in faqs" :key="idx">
                                <div class="border border-gray-200 rounded-lg bg-white p-4 mb-3 relative">
                                    <button type="button" @click="removeFaq(idx)"
                                            class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors" title="Remove FAQ">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    <div class="mb-3">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Question</label>
                                        <input type="text" x-model="faq.q" placeholder="e.g. How long does delivery take?"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Answer</label>
                                        <textarea x-model="faq.a" rows="2" placeholder="Short answer shown to the customer..."
                                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white resize-none"></textarea>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addFaq()"
                                    class="flex items-center gap-2 px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition-colors w-full justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add FAQ
                            </button>

                            <input type="hidden" name="live_chat_faqs" :value="JSON.stringify(faqs)">
                        </div>

                        {{-- Contact Address --}}
                        <div>
                            <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-1.5">Office / showroom address</label>
                            <textarea name="contact_address" id="contact_address" rows="3"
                                      placeholder="Full address..."
                                      class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none">{{ $settings['contact_address'] ?? '' }}</textarea>
                        </div>

                        {{-- Business Hours --}}
                        <div>
                            <label for="contact_business_hours" class="block text-sm font-medium text-gray-700 mb-1.5">Business hours</label>
                            <input type="text" name="contact_business_hours" id="contact_business_hours"
                                   value="{{ $settings['contact_business_hours'] ?? '' }}"
                                   placeholder="e.g. Satâ€“Thu, 10 AM â€“ 8 PM"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Map Embed URL --}}
                        <div>
                            <label for="contact_map_url" class="block text-sm font-medium text-gray-700 mb-1.5">Google Maps embed URL</label>
                            <input type="url" name="contact_map_url" id="contact_map_url"
                                   value="{{ $settings['contact_map_url'] ?? '' }}"
                                   placeholder="https://www.google.com/maps/embed?..."
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1.5">Paste the embed URL from Google Maps share option.</p>
                        </div>
                    </div>
                @endif

                {{-- ============ ADDITIONAL TAB ============ --}}
                @if ($activeTab === 'additional')
                    <div x-data="{ subTab: '{{ request('sub', 'top') }}' }" class="max-w-3xl">
                        {{-- Sub-tabs --}}
                        <div class="flex items-center gap-2 mb-6 border-b border-gray-200 pb-3">
                            <button type="button" @click="subTab = 'top'"
                                    :class="subTab === 'top' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Top Bar
                            </button>
                            <button type="button" @click="subTab = 'popup'"
                                    :class="subTab === 'popup' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Popup
                            </button>
                            <button type="button" @click="subTab = 'others'"
                                    :class="subTab === 'others' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Others
                            </button>
                        </div>

                        <div x-show="subTab === 'top'" x-cloak class="space-y-6">
                            {{-- ============ TOP BAR ============ --}}
                        <script>window.topBarTypes = @js(\App\Services\TopBarService::TYPES); window.topBarAnimations = @js(\App\Services\TopBarService::ANIMATIONS);</script>
                        <div class="bg-white rounded-lg border border-gray-200 p-5"
                             x-data="topBarBuilder(@js($topBarsConfig ?? []))"
                             @input="$nextTick(() => serialize())"
                             @change="$nextTick(() => serialize())">
                            <input type="hidden" name="top_bars" :value="serialized">

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">Top Bars</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">Create announcements, promotional bars, countdowns and more. Multiple bars can run at once and are rendered in order.</p>
                                </div>
                                <button type="button" @click="addBar()"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add Top Bar
                                </button>
                            </div>

                            <template x-if="bars.length === 0">
                                <div class="text-center py-10 border-2 border-dashed border-gray-200 rounded-lg">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <p class="text-sm text-gray-500">No top bars yet. Click <span class="font-semibold text-indigo-600">Add Top Bar</span> to create your first one.</p>
                                </div>
                            </template>

                            <div class="space-y-4">
                                <template x-for="(bar, index) in bars" :key="bar.id">
                                    <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50/50">
                                        {{-- Card header --}}
                                        <div class="flex items-center gap-3 px-4 py-3 bg-white border-b border-gray-200 cursor-pointer" @click="bar._open = !bar._open">
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                                            <span x-text="bar.name || 'Untitled Top Bar'" class="text-sm font-semibold text-gray-900 truncate"></span>
                                            <span class="ml-1 px-2 py-0.5 text-[11px] font-medium rounded-full" :class="bar.status ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'" x-text="bar.status ? 'Active' : 'Inactive'"></span>
                                            <span class="text-[11px] text-gray-400" x-text="typeLabel(bar.type)"></span>
                                            <div class="ml-auto flex items-center gap-1" @click.stop>
                                                <button type="button" @click="move(index, -1)" title="Move up" class="p-1.5 text-gray-400 hover:text-gray-700 rounded hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button>
                                                <button type="button" @click="move(index, 1)" title="Move down" class="p-1.5 text-gray-400 hover:text-gray-700 rounded hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
                                                <button type="button" @click="duplicate(index)" title="Duplicate" class="p-1.5 text-gray-400 hover:text-gray-700 rounded hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg></button>
                                                <button type="button" @click="remove(index)" title="Delete" class="p-1.5 text-red-400 hover:text-red-600 rounded hover:bg-red-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                            </div>
                                        </div>

                                        {{-- Card body --}}
                                        <div x-show="bar._open" x-collapse x-cloak class="p-4 space-y-5">
                                            {{-- 1. Basic Information --}}
                                            <div>
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Basic Information</h5>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                                                        <input type="text" x-model="bar.name" placeholder="e.g. Free Shipping Bar"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                                        <select x-model="bar.status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <option :value="true">Active</option>
                                                            <option :value="false">Inactive</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                                                        <select x-model="bar.type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <template x-for="opt in typesList" :key="opt.value">
                                                                <option :value="opt.value" x-text="opt.label"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 2. Content --}}
                                            <div>
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Content</h5>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Message / Text</label>
                                                        <input type="text" x-model="bar.message" placeholder="e.g. Free Shipping on orders over &#2547;1,000"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                        <template x-if="bar.type === 'discount'">
                                                            <p class="text-[11px] text-gray-400 mt-1">Use <code class="text-indigo-500">{coupon}</code> to inject the coupon code into the message.</p>
                                                        </template>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Highlight Text</label>
                                                        <input type="text" x-model="bar.highlight_text" placeholder="e.g. Shop Now"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Icon (emoji)</label>
                                                        <input type="text" x-model="bar.icon" placeholder="e.g. &#128666;"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Button Text</label>
                                                        <input type="text" x-model="bar.button_text" placeholder="e.g. Shop Now"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Button URL</label>
                                                        <input type="text" x-model="bar.button_url" placeholder="e.g. /shop"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div x-show="bar.type === 'discount' || bar.type === 'promotional'">
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Coupon Code</label>
                                                        <input type="text" x-model="bar.coupon_code" placeholder="e.g. SAVE20"
                                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 3. Countdown (countdown type) --}}
                                            <div x-show="bar.type === 'countdown'">
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Countdown Timer</h5>
                                                <p class="text-xs text-gray-400 -mt-2 mb-3">Set an <b>Ends at</b> time to activate the countdown. Optionally set a <b>Starts at</b> to show a "starts in" countdown before it begins, then it flips to counting down to the end.</p>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Starts At (optional)</label>
                                                        <input type="datetime-local" x-model="bar.countdown_start" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Ends At</label>
                                                        <input type="datetime-local" x-model="bar.countdown_end" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Expired Message</label>
                                                        <input type="text" x-model="bar.expired_message" placeholder="e.g. Offer ended" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 4. Design --}}
                                            <div>
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Design</h5>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Background</label>
                                                        <input type="color" x-model="bar.bg_color" class="h-9 w-full rounded border border-gray-300 bg-white cursor-pointer">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Text Color</label>
                                                        <input type="color" x-model="bar.text_color" class="h-9 w-full rounded border border-gray-300 bg-white cursor-pointer">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Highlight</label>
                                                        <input type="color" x-model="bar.highlight_color" class="h-9 w-full rounded border border-gray-300 bg-white cursor-pointer">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Button</label>
                                                        <input type="color" x-model="bar.button_color" class="h-9 w-full rounded border border-gray-300 bg-white cursor-pointer">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Font Size</label>
                                                        <select x-model="bar.font_size" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <option value="xs">Extra Small</option>
                                                            <option value="sm">Small</option>
                                                            <option value="base">Base</option>
                                                            <option value="lg">Large</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Font Weight</label>
                                                        <select x-model="bar.font_weight" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <option value="normal">Normal</option>
                                                            <option value="medium">Medium</option>
                                                            <option value="semibold">Semibold</option>
                                                            <option value="bold">Bold</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Bar Height (rem)</label>
                                                        <input type="number" x-model="bar.bar_height" min="2" max="24" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Text Align</label>
                                                        <select x-model="bar.text_align" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <option value="left">Left</option>
                                                            <option value="center">Center</option>
                                                            <option value="right">Right</option>
                                                         </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Icon Position</label>
                                                        <select x-model="bar.icon_position" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <option value="left">Left</option>
                                                            <option value="right">Right</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Close Button</label>
                                                        <select x-model="bar.show_close" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <option :value="true">Show</option>
                                                            <option :value="false">Hide</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Sticky</label>
                                                        <select x-model="bar.sticky" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <option :value="false">No</option>
                                                            <option :value="true">Yes</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Animation</label>
                                                        <select x-model="bar.animation" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                            <template x-for="opt in animationsList" :key="opt.value">
                                                                <option :value="opt.value" x-text="opt.label"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 5. Display Settings --}}
                                            <div>
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Display Settings</h5>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-2">Show on</label>
                                                        <div class="space-y-1.5">
                                                            <template x-for="opt in ['all','homepage','shop','products','cart','checkout']" :key="opt">
                                                                <label class="flex items-center gap-2 text-xs text-gray-700">
                                                                    <input type="checkbox" :value="opt" x-model="bar.show_on" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                                    <span x-text="pageLabel(opt)"></span>
                                                                </label>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-2">Devices</label>
                                                        <div class="space-y-1.5">
                                                            <template x-for="opt in ['all','desktop','mobile','tablet']" :key="opt">
                                                                <label class="flex items-center gap-2 text-xs text-gray-700">
                                                                    <input type="checkbox" :value="opt" x-model="bar.devices" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                                    <span x-text="deviceLabel(opt)"></span>
                                                                </label>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 6. Scheduling --}}
                                            <div>
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Scheduling (optional)</h5>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Start Date &amp; Time</label>
                                                        <input type="datetime-local" x-model="bar.start" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">End Date &amp; Time</label>
                                                        <input type="datetime-local" x-model="bar.end" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                    </div>
                                                </div>
                                                <p class="text-[11px] text-gray-400 mt-1">Leave blank for immediate / no end. Schedule seasonal campaigns automatically.</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        </div>

                        <div x-show="subTab === 'popup'" x-cloak
                             x-data="popupManager({{ json_encode([
                                 'popups' => $popups->map(fn ($p) => [
                                     'id' => $p->id,
                                     'name' => $p->name,
                                     'is_active' => (bool) $p->is_active,
                                     'sort_order' => (int) $p->sort_order,
                                     'trigger' => $p->config['trigger'] ?? 'immediately',
                                     'views' => (int) ($p->stats['views'] ?? 0),
                                     'clicks' => (int) ($p->stats['clicks'] ?? 0),
                                     'image_url' => ! empty($p->config['image']) ? \Illuminate\Support\Facades\Storage::url($p->config['image']) : '',
                                     'has_image' => ! empty($p->config['image']),
                                     'config' => $p->config,
                                 ])->values(),
                                 'defaults' => $popupDefaults,
                                 'storeUrl' => route('admin.popups.store'),
                                 'updateUrl' => route('admin.popups.update', ['popup' => '__ID__']),
                                 'destroyUrl' => route('admin.popups.destroy', ['popup' => '__ID__']),
                                 'devices' => $popupDevices,
                                 'pages' => $popupPages,
                                 'customers' => $popupCustomers,
                                 'triggers' => $popupTriggers,
                                 'frequencies' => $popupFrequencies,
                                 'imagePositions' => $popupImagePositions,
                                 'csrf' => csrf_token(),
                             ]) }})"
                             class="space-y-5">
                            {{-- ============ POPUP MANAGER ============ --}}

                            {{-- List view --}}
                            <div x-show="!editing">
                                <div class="flex items-start justify-between mb-6">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900">Popup campaigns</h4>
                                        <p class="text-xs text-gray-400 mt-0.5">Create, schedule and target full-image promotional popups.</p>
                                    </div>
                                    <button type="button" @click="startCreate()"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl text-sm font-semibold hover:from-indigo-700 hover:to-violet-700 transition shadow-sm shadow-indigo-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        New popup
                                    </button>
                                </div>

                                <div x-show="popups.length === 0" class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
                                    <div class="mx-auto w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4">
                                        <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <h5 class="text-sm font-semibold text-gray-800">No popups yet</h5>
                                    <p class="text-xs text-gray-400 mt-1 mb-5">Create your first image popup to capture more attention.</p>
                                    <button type="button" @click="startCreate()"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                                        Add popup
                                    </button>
                                </div>

                                <div x-show="popups.length > 0" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-200">
                                                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Popup</th>
                                                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trigger</th>
                                                <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Views</th>
                                                <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Clicks</th>
                                                <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Active</th>
                                                <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="popup in popups" :key="popup.id">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-5 py-3">
                                                        <div class="w-14 h-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                                            <template x-if="popup.image_url">
                                                                <img :src="popup.image_url" class="w-full h-full object-cover">
                                                            </template>
                                                            <template x-if="!popup.image_url">
                                                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                            </template>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-3">
                                                        <span class="font-medium text-gray-900" x-text="popup.name || 'Untitled popup'"></span>
                                                    </td>
                                                    <td class="px-5 py-3 text-center text-gray-500" x-text="triggers[popup.trigger] || '—'"></td>
                                                    <td class="px-5 py-3 text-center text-gray-500" x-text="popup.views"></td>
                                                    <td class="px-5 py-3 text-center text-gray-500" x-text="popup.clicks"></td>
                                                    <td class="px-5 py-3 text-center">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                              :class="popup.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                                                              x-text="popup.is_active ? 'Active' : 'Inactive'"></span>
                                                    </td>
                                                    <td class="px-5 py-3 text-right">
                                                        <div class="flex items-center justify-end gap-3">
                                                            <button type="button" @click="startEdit(popup)"
                                                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors">Edit</button>
                                                            <a :href="'{{ route('admin.popups.analytics', ['popup' => '__ID__']) }}'.replace('__ID__', popup.id)"
                                                               class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Analytics</a>
                                                            <button type="button" @click="remove(popup)"
                                                                    class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">Delete</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Editor view --}}
                            <div x-show="editing" x-cloak class="space-y-6">

                                {{-- Editor Header --}}
                                <div class="flex items-center justify-between">
                                    <h4 class="text-base font-semibold text-gray-900" x-text="editingId ? 'Edit popup' : 'New popup'"></h4>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="cancelEdit()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                                        <button type="button" @click="prepareSubmit()"
                                                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span x-text="editingId ? 'Update popup' : 'Create popup'"></span>
                                        </button>
                                    </div>
                                </div>

                                {{-- ============ FIELDS ============ --}}
                                <div class="max-w-2xl space-y-6">

                                    {{-- Name --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                                        <input type="text" x-model="form.name" form="popupManagerForm" name="name" required
                                               placeholder="e.g. Eid Sale Offer"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    {{-- Image upload --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Popup image <span class="text-red-500">*</span></label>
                                        <div class="flex items-start gap-5">
                                            <div class="w-40 h-28 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0 transition-colors hover:border-indigo-300 hover:bg-indigo-50/30" id="settingsPopupImagePreview">
                                                <template x-if="form.image_url">
                                                    <img :src="form.image_url" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!form.image_url">
                                                    <div class="text-center">
                                                        <svg class="w-8 h-8 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        <p class="text-[10px] text-gray-400 mt-1">Preview</p>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="flex-1 space-y-3">
                                                <label for="settingsImage" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 cursor-pointer transition shadow-sm">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    Upload image
                                                </label>
                                                <input type="file" id="settingsImage" name="image" form="popupManagerForm"
                                                       accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" class="hidden"
                                                       :required="!editingId"
                                                       @change="previewImage($event)">
                                                <template x-if="editingId && form.has_image">
                                                    <label class="flex items-center gap-2 text-xs text-red-600 cursor-pointer">
                                                        <input type="checkbox" x-model="form.remove_image" form="popupManagerForm" name="remove_image" value="1"
                                                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                        Remove current image
                                                    </label>
                                                </template>
                                                <p class="text-xs text-gray-400">JPG, PNG, WebP or GIF. Max 4MB. Auto-scales to fit any screen.</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Popup URL --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Popup URL</label>
                                        <input type="text" x-model="config.button_url" placeholder="e.g. /collections/sale"
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    {{-- Image position + Popup size --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Image position</label>
                                            <select x-model="config.image_position"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <template x-for="(label, value) in imagePositions" :key="value">
                                                    <option :value="value" x-text="label"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Popup size</label>
                                            <select x-model="config.width"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="xs">Small</option>
                                                <option value="sm">Medium</option>
                                                <option value="lg">Large</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Overlay opacity + Corner radius --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Overlay opacity <span class="text-indigo-600 font-semibold" x-text="config.overlay_opacity + '%'"></span></label>
                                            <input type="range" x-model.number="config.overlay_opacity" min="0" max="90" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Corner radius</label>
                                            <select x-model="config.rounded" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="none">None</option>
                                                <option value="md">Rounded</option>
                                                <option value="2xl">Rounded 2xl</option>
                                            </select>
                                        </div>
                                    </div>

                                    <hr class="border-gray-200">

                                    {{-- Settings --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Sort order</label>
                                            <input type="number" x-model.number="form.sort_order" form="popupManagerForm" name="sort_order" min="0"
                                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Active</label>
                                            <label class="relative inline-flex items-center cursor-pointer mt-1.5">
                                                <input type="checkbox" x-model="form.is_active" form="popupManagerForm" name="is_active" value="1" class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                            </label>
                                        </div>
                                    </div>

                                    <hr class="border-gray-200">

                                    {{-- Display rules --}}
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 mb-2">Show popup on</p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="(label, value) in pages" :key="value">
                                                    <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm transition-colors cursor-pointer"
                                                           :class="config.show_on.includes(value) ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                                                        <input type="checkbox" :checked="config.show_on.includes(value)" @change="toggle('show_on', value)" class="sr-only">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="config.show_on.includes(value) ? 'bg-indigo-500' : 'bg-gray-300'"></span>
                                                        <span x-text="label"></span>
                                                    </label>
                                                </template>
                                            </div>
                                            <div x-show="config.show_on.includes('selected')" x-cloak class="mt-3">
                                                <textarea x-model="selectedPagesText" rows="3" placeholder="One page per line, e.g. /about or full URL"
                                                          class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 mb-2">Device</p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="(label, value) in devices" :key="value">
                                                    <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm transition-colors cursor-pointer"
                                                           :class="config.devices.includes(value) ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                                                        <input type="checkbox" :checked="config.devices.includes(value)" @change="toggle('devices', value)" class="sr-only">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="config.devices.includes(value) ? 'bg-indigo-500' : 'bg-gray-300'"></span>
                                                        <span x-text="label"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 mb-2">Customer targeting</p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="(label, value) in customers" :key="value">
                                                    <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm transition-colors cursor-pointer"
                                                           :class="config.customers.includes(value) ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                                                        <input type="checkbox" :checked="config.customers.includes(value)" @change="toggle('customers', value)" class="sr-only">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="config.customers.includes(value) ? 'bg-indigo-500' : 'bg-gray-300'"></span>
                                                        <span x-text="label"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="border-gray-200">

                                    {{-- Trigger --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">When to show</label>
                                            <select x-model="config.trigger" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <template x-for="(label, value) in triggers" :key="value">
                                                    <option :value="value" x-text="label"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div x-show="config.trigger === 'after_seconds'" x-cloak>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Delay (seconds)</label>
                                            <input type="number" x-model.number="config.trigger_seconds" min="0" max="120"
                                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div x-show="config.trigger === 'on_scroll'" x-cloak>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Scroll percentage <span class="text-indigo-600 font-semibold" x-text="config.scroll_percent + '%'"></span></label>
                                            <select x-model.number="config.scroll_percent" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="25">After 25%</option>
                                                <option value="50">After 50%</option>
                                                <option value="75">After 75%</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Frequency --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Show frequency</label>
                                        <select x-model="config.frequency" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <template x-for="(label, value) in frequencies" :key="value">
                                                <option :value="value" x-text="label"></option>
                                            </template>
                                        </select>
                                    </div>

                                    {{-- Frequency toggles --}}
                                    <div class="flex flex-wrap gap-6">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" :checked="config.hide_after_close" @change="config.hide_after_close = !config.hide_after_close"
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">Don't show again after closing</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" :checked="config.hide_after_click" @change="config.hide_after_click = !config.hide_after_click"
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">Don't show again after clicking the CTA</span>
                                        </label>
                                    </div>

                                    <hr class="border-gray-200">

                                    {{-- Schedule --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Start date &amp; time <span class="text-gray-400 font-normal">(optional)</span></label>
                                            <input type="datetime-local" x-model="config.start" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">End date &amp; time <span class="text-gray-400 font-normal">(optional)</span></label>
                                            <input type="datetime-local" x-model="config.end" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    </div>

                                    <hr class="border-gray-200">

                                    {{-- Live Preview --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                                        <div class="bg-gray-100 rounded-xl p-5 flex items-center justify-center min-h-[280px]">
                                            <div class="w-full transition-all duration-200"
                                                 :class="{
                                                    'max-w-[200px]': config.width === 'xs',
                                                    'max-w-[280px]': config.width === 'sm',
                                                    'max-w-[400px]': config.width === 'lg',
                                                 }"
                                                 :style="'border-radius:' + (config.rounded === 'none' ? '0' : config.rounded === 'md' ? '0.75rem' : '1rem') + ';overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.12)'">
                                                <div class="relative" :style="'background-color:' + config.bg_color">
                                                    <template x-if="form.image_url">
                                                        <img :src="form.image_url" class="w-full h-auto block">
                                                    </template>
                                                    <template x-if="!form.image_url">
                                                        <div class="w-full h-24 flex items-center justify-center" :style="'background-color:' + config.bg_color">
                                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        </div>
                                                    </template>
                                                    <div class="px-4 py-3 text-center space-y-2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div x-show="subTab === 'others'" x-cloak class="space-y-6">
                            {{-- ============ FAKE ORDER ============ --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-5"
                             x-data="{ fakeOrdersEnabled: {{ ($settings['fake_orders_enabled'] ?? '0') === '1' ? 'true' : 'false' }} }">
                            <div class="flex items-center justify-between mb-1">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">Fake Order Notifications</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">Show social-proof "someone just bought" popups to boost conversions.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="fake_orders_enabled" value="1" x-model="fakeOrdersEnabled" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            <div x-show="fakeOrdersEnabled" x-cloak class="space-y-4 mt-4">
                                <div>
                                    <label for="fake_order_message" class="block text-xs font-medium text-gray-600 mb-1">Notification text</label>
                                    <input type="text" name="fake_order_message" id="fake_order_message"
                                           value="{{ $settings['fake_order_message'] ?? '' }}"
                                           placeholder="e.g. {name} from {city} just purchased a {product}"
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                    <p class="text-[11px] text-gray-400 mt-1">Use <code class="text-indigo-500">{name}</code>, <code class="text-indigo-500">{city}</code> and <code class="text-indigo-500">{product}</code> as placeholders.</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="fake_order_interval_seconds" class="block text-xs font-medium text-gray-600 mb-1">Interval (seconds)</label>
                                        <input type="number" name="fake_order_interval_seconds" id="fake_order_interval_seconds" min="5"
                                               value="{{ $settings['fake_order_interval_seconds'] ?? '20' }}"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                    </div>
                                    <div>
                                        <label for="fake_order_position" class="block text-xs font-medium text-gray-600 mb-1">Display position</label>
                                        <select name="fake_order_position" id="fake_order_position"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                            @foreach ([
                                                'bottom-left' => 'Bottom left',
                                                'bottom-right' => 'Bottom right',
                                                'top-left' => 'Top left',
                                                'top-right' => 'Top right',
                                            ] as $posKey => $posLabel)
                                                <option value="{{ $posKey }}" {{ ($settings['fake_order_position'] ?? 'bottom-left') === $posKey ? 'selected' : '' }}>{{ $posLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @php
                                    $fakeOrderPages = array_filter(array_map('trim', explode("\n", $settings['fake_order_pages'] ?? 'all')));
                                    $fakeOrderPresets = [
                                        'all' => 'All pages',
                                        'home' => 'Homepage only',
                                        'products' => 'Shop page',
                                        'product' => 'Product pages',
                                        'cart' => 'Cart',
                                        'checkout' => 'Checkout',
                                    ];
                                    $fakeOrderPagesString = implode(',', $fakeOrderPages);
                                    $fakeOrderMode = array_key_exists($fakeOrderPagesString, $fakeOrderPresets) ? $fakeOrderPagesString : 'custom';
                                    $fakeOrderCustomUrls = $fakeOrderMode === 'custom' ? implode("\n", $fakeOrderPages) : '';
                                @endphp
                                <div x-data="{ fakeOrderMode: '{{ $fakeOrderMode }}' }">
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Show popup on</label>
                                    <div class="space-y-1.5">
                                        @foreach ($fakeOrderPresets as $presetKey => $presetLabel)
                                            <label class="flex items-center gap-2 text-xs text-gray-600">
                                                <input type="radio" name="fake_order_mode" value="{{ $presetKey }}"
                                                       x-model="fakeOrderMode"
                                                       class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                {{ $presetLabel }}
                                            </label>
                                        @endforeach
                                        <label class="flex items-center gap-2 text-xs text-gray-600">
                                            <input type="radio" name="fake_order_mode" value="custom"
                                                   x-model="fakeOrderMode"
                                                   class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            Selected pages (custom URLs)
                                        </label>
                                    </div>

                                    <div x-show="fakeOrderMode === 'custom'" x-cloak class="mt-3 pl-6">
                                        <label for="fake_order_pages" class="block text-xs font-medium text-gray-600 mb-1">Page URLs (one per line)</label>
                                        <textarea name="fake_order_pages" id="fake_order_pages" rows="4"
                                                  placeholder="/blog{{ "\n" }}/about{{ "\n" }}/products/123"
                                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white font-mono">{{ $fakeOrderCustomUrls }}</textarea>
                                        <p class="text-[11px] text-gray-400 mt-1">Enter the paths or full URLs of the pages where the popup should appear, one per line.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @endif

                {{-- Save Button --}}
                <div class="mt-8 pt-6 border-t border-gray-200 flex items-center gap-3">
                    <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                        Save changes
                    </button>
                    <span class="text-xs text-gray-400">Changes are saved immediately.</span>
                </div>
            </form>

            {{-- Popup manager form (lives outside the settings form; targeted via form="popupManagerForm") --}}
            <form id="popupManagerForm" method="POST" enctype="multipart/form-data" action="{{ route('admin.popups.store') }}" style="display:none">
                @csrf
                <input type="hidden" name="_method" id="popupManagerMethod" value="POST">
                <input type="hidden" name="config" id="popupManagerConfigJson" value="{}">
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function shippingZones() {
                return {
                    zones: [],
                    showZoneModal: false,
                    newZone: { name: '' },
                    showMethodModal: false,
                    methodZoneIndex: null,
                    newMethod: { type: 'free', title: '', description: '', enabled: true, amount: '', per_kg: '' },
                    geo: {!! json_encode($geoData ?? []) !!},
                    treeRoots: [],
                    nodeMap: {},
                    treeFlat: [],
                    init() {
                        this.buildTree();
                        const saved = {!! json_encode($savedZones ?? []) !!};
                        this.zones = (Array.isArray(saved) ? saved : []).map(z => ({
                            name: z.name || '',
                            methods: Array.isArray(z.methods) ? z.methods : [],
                            regions: Array.isArray(z.regions) ? z.regions : [],
                            checked: {},
                            search: '',
                            open: false,
                        }));
                        this.zones.forEach(z => {
                            (Array.isArray(z.regions) ? z.regions : []).forEach(r => {
                                const key = this.keyByName(r);
                                if (key) this.setCheck(z, key, true);
                            });
                        });
                    },
                    buildTree() {
                        this.treeRoots = [];
                        this.nodeMap = {};
                        const root = { key: 'root', name: 'Bangladesh', level: 0, parent: null, expanded: true, children: [] };
                        this.nodeMap['root'] = root;
                        (this.geo || []).forEach(div => {
                            const vk = 'V:' + div.name;
                            const d = { key: vk, name: div.name, level: 1, parent: 'root', expanded: false, children: [] };
                            this.nodeMap[d.key] = d;
                            root.children.push(d);
                            (div.districts || []).forEach(dist => {
                                const dk = vk + '::D:' + dist.name;
                                const di = { key: dk, name: dist.name, level: 2, parent: d.key, expanded: false, children: [] };
                                this.nodeMap[di.key] = di;
                                d.children.push(di);
                                (dist.upazilas || []).forEach(up => {
                                    const uk = dk + '::U:' + up;
                                    const u = { key: uk, name: up, level: 3, parent: di.key, expanded: false, children: [] };
                                    this.nodeMap[u.key] = u;
                                    di.children.push(u);
                                });
                            });
                        });
                        this.treeRoots = [root];
                        this.treeFlat = [];
                        const walk = list => (list || []).forEach(n => { this.treeFlat.push(n); walk(n.children); });
                        walk(this.treeRoots);
                    },
                    hasChildren(node) {
                        return !!(node && node.children && node.children.length);
                    },
                    toggleExpanded(key) {
                        const node = this.nodeMap[key];
                        if (node) node.expanded = !node.expanded;
                    },
                    filteredVisibleFlat(zone) {
                        const q = (zone.search || '').trim().toLowerCase();
                        const out = [];
                        const walk = node => {
                            const selfMatch = !q || node.name.toLowerCase().includes(q);
                            const childMatch = (node.children || []).some(c => this.branchMatches(c, q));
                            if (!selfMatch && !childMatch) return;
                            out.push(node);
                            if (!q && !node.expanded) return;
                            (node.children || []).forEach(walk);
                        };
                        walk(this.treeRoots[0]);
                        return out;
                    },
                    branchMatches(node, q) {
                        if (!q) return true;
                        if (node.name.toLowerCase().includes(q)) return true;
                        return (node.children || []).some(c => this.branchMatches(c, q));
                    },
                    descendantsOf(key) {
                        const stack = [...(this.nodeMap[key]?.children || [])];
                        const out = [];
                        while (stack.length) {
                            const n = stack.pop();
                            out.push(n.key);
                            stack.push(...(n.children || []));
                        }
                        return out;
                    },
                    setCheck(zone, key, val) {
                        if (!zone.checked) zone.checked = {};
                        zone.checked[key] = val;
                        this.descendantsOf(key).forEach(k => { zone.checked[k] = val; });
                    },
                    keyByName(name) {
                        const raw = String(name || '').trim();
                        const n = raw.toLowerCase();
                        if (n === 'all' || n === 'bangladesh') return 'root';
                        const matches = this.treeFlat.filter(nd => nd.name.toLowerCase() === n);
                        if (!matches.length) return null;
                        const byLevel = level => matches.find(nd => nd.level === level);
                        return (byLevel(1) || byLevel(2) || matches[0]).key;
                    },
                    isChecked(zone, key) {
                        return !!(zone.checked && zone.checked[key]);
                    },
                    isIndeterminate(zone, key) {
                        const node = this.nodeMap[key];
                        if (!node || !node.children || !node.children.length) return false;
                        const kids = node.children.map(c => c.key);
                        const any = kids.some(k => this.isChecked(zone, k));
                        const all = kids.every(k => this.isChecked(zone, k));
                        return any && !all;
                    },
                    toggleNode(zone, key) {
                        if (!zone.checked) zone.checked = {};
                        this.setCheck(zone, key, !zone.checked[key]);
                    },
                    regionSummary(zone) {
                        const names = this.serializeRegions(zone);
                        if (!names.length) return 'No regions selected yet.';
                        if (names.includes('all')) return 'All of Bangladesh';
                        if (names.length <= 3) return 'Regions: ' + names.join(', ');
                        return names.length + ' regions selected';
                    },
                    serializeRegions(zone) {
                        const out = [];
                        this.treeFlat.forEach(nd => {
                            if (!this.isChecked(zone, nd.key)) return;
                            if (nd.parent && this.isChecked(zone, nd.parent)) return;
                            out.push(nd.key === 'root' ? 'all' : nd.name);
                        });
                        return out;
                    },
                    selectedChips(zone) {
                        return this.serializeRegions(zone).map(n => n === 'all' ? 'Bangladesh' : n);
                    },
                    removeRegion(zone, name) {
                        const resolve = n => {
                            if (n === 'all') return 'root';
                            const hit = this.treeFlat.find(nd =>
                                nd.name === n && this.isChecked(zone, nd.key) && (!nd.parent || !this.isChecked(zone, nd.parent)),
                            );
                            return hit ? hit.key : this.keyByName(n);
                        };
                        const removeKey = name === 'Bangladesh' ? 'root' : resolve(name);
                        const remaining = this.serializeRegions(zone).map(resolve).filter(k => k !== removeKey);
                        zone.checked = {};
                        remaining.forEach(k => this.setCheck(zone, k, true));
                    },
                    addZone() {
                        this.newZone = { name: '' };
                        this.showZoneModal = true;
                    },
                    saveZone() {
                        if (!this.newZone.name.trim()) return;
                        const z = { name: this.newZone.name, methods: [], checked: {}, search: '', open: false };
                        this.setCheck(z, 'root', true);
                        this.zones.push(z);
                        this.showZoneModal = false;
                    },
                    removeZone(i) {
                        this.zones.splice(i, 1);
                    },
                    openAddMethod(zi) {
                        this.methodZoneIndex = zi;
                        this.newMethod = { type: 'free', title: '', description: '', enabled: true, amount: '', per_kg: '' };
                        this.showMethodModal = true;
                    },
                    addMethod() {
                        if (this.methodZoneIndex === null) return;
                        const z = this.zones[this.methodZoneIndex];
                        if (!z) return;
                        if (!z.methods) z.methods = [];
                        if (!String(this.newMethod.title || '').trim()) this.newMethod.title = this.typeLabel(this.newMethod.type);
                        const m = {
                            type: this.newMethod.type,
                            title: this.newMethod.title,
                            description: this.newMethod.description || '',
                            enabled: this.newMethod.enabled !== false,
                        };
                        if (this.newMethod.type === 'flat_rate') m.amount = this.newMethod.amount;
                        if (this.newMethod.type === 'weight_based') { m.amount = this.newMethod.amount; m.per_kg = this.newMethod.per_kg; }
                        z.methods.push(m);
                        this.showMethodModal = false;
                    },
                    removeMethod(zi, mi) {
                        this.zones[zi].methods.splice(mi, 1);
                    },
                    toggleMethod(zi, mi) {
                        this.zones[zi].methods[mi].enabled = !this.zones[zi].methods[mi].enabled;
                    },
                    serialize() {
                        return this.zones.map(z => ({
                            name: z.name,
                            regions: this.serializeRegions(z),
                            methods: z.methods || [],
                        }));
                    },
                    typeLabel(t) {
                        return { free: 'Free Shipping', weight_based: 'Weight Based', flat_rate: 'Flat Rate' }[t] || t;
                    },
                };
            }

            function freeShippingConfig() {
                return {
                    freeType: '{{ $settings["free_shipping_type"] ?? "order_amount" }}',
                    freeProducts: {!! $settings["free_shipping_products"] ?? "[]" !!},
                    freeCategories: {!! $settings["free_shipping_categories"] ?? "[]" !!},
                    freeCollections: {!! $settings["free_shipping_collections"] ?? "[]" !!},
                    productSearch: '',
                    categorySearch: '',
                    collectionSearch: '',
                    allProducts: {!! json_encode($allProducts ?? []) !!},
                    allCategories: {!! json_encode($allCategories ?? []) !!},
                    allCollections: {!! json_encode($allCollections ?? []) !!},
                    loadProducts() {},
                    loadCategories() {},
                    loadCollections() {},
                    toggleProduct(id) {
                        const idx = this.freeProducts.indexOf(id);
                        if (idx === -1) { this.freeProducts.push(id); } else { this.freeProducts.splice(idx, 1); }
                    },
                    toggleCategory(id) {
                        const idx = this.freeCategories.indexOf(id);
                        if (idx === -1) { this.freeCategories.push(id); } else { this.freeCategories.splice(idx, 1); }
                    },
                    toggleCollection(id) {
                        const idx = this.freeCollections.indexOf(id);
                        if (idx === -1) { this.freeCollections.push(id); } else { this.freeCollections.splice(idx, 1); }
                    },
                    filteredProducts() {
                        const q = this.productSearch.toLowerCase();
                        if (!q) return this.allProducts;
                        return this.allProducts.filter(p => p.name.toLowerCase().includes(q));
                    },
                    filteredCategories() {
                        const q = this.categorySearch.toLowerCase();
                        if (!q) return this.allCategories;
                        return this.allCategories.filter(c => c.name.toLowerCase().includes(q));
                    },
                    filteredCollections() {
                        const q = this.collectionSearch.toLowerCase();
                        if (!q) return this.allCollections;
                        return this.allCollections.filter(c => c.name.toLowerCase().includes(q));
                    }
                };
            }

            function liveChatFaqs() {
                return {
                    faqs: {!! json_encode($chatFaqs ?? []) !!},
                    addFaq() {
                        this.faqs.push({ q: '', a: '' });
                    },
                    removeFaq(idx) {
                        this.faqs.splice(idx, 1);
                    },
                };
            }

            document.addEventListener('DOMContentLoaded', function() {
                // SEO character counters
                const titleInput = document.getElementById('seo_meta_title');
                const descInput = document.getElementById('seo_meta_description');
                const titleCount = document.querySelector('.seo-title-count');
                const descCount = document.querySelector('.seo-desc-count');

                if (titleInput && titleCount) {
                    titleInput.addEventListener('input', function() {
                        titleCount.textContent = 70 - this.value.length;
                    });
                }
                if (descInput && descCount) {
                    descInput.addEventListener('input', function() {
                        descCount.textContent = 160 - this.value.length;
                    });
                }

                // Logo / Favicon image previews
                function setupPreview(inputId, previewId) {
                    const input = document.getElementById(inputId);
                    const preview = document.getElementById(previewId);
                    if (!input || !preview) return;
                    input.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="w-full h-full object-contain">';
                            };
                            reader.readAsDataURL(this.files[0]);
                        }
                    });
                }
                setupPreview('store_logo', 'logo-preview');
                setupPreview('store_favicon', 'favicon-preview');

                // Payment gateway logo previews
                ['bkash', 'nagad', 'rocket', 'cod', 'card', 'sslcommerz'].forEach(function (key) {
                    setupPreview('payment_' + key + '_logo', key + '-logo-preview');
                });

                // Logo size slider
                const logoSlider = document.getElementById('logo-size-slider');
                const logoSizeValue = document.getElementById('logo-size-value');
                if (logoSlider && logoSizeValue) {
                    logoSlider.addEventListener('input', function () {
                        logoSizeValue.textContent = this.value + 'px';
                    });
                }

                // Coming soon toggle â€” show/hide launch date
                const comingSoonCheckbox = document.querySelector('input[name="coming_soon"][type="checkbox"]');
                const launchDateRow = document.getElementById('launch-date-row');
                if (comingSoonCheckbox && launchDateRow) {
                    comingSoonCheckbox.addEventListener('change', function () {
                        launchDateRow.style.display = this.checked ? '' : 'none';
                    });
                }
            });
        </script>
    @endpush

    @push('scripts')
        <script>
            function popupManager(init) {
                return {
                    popups: init.popups || [],
                    defaults: init.defaults || {},
                    devices: init.devices || {},
                    pages: init.pages || {},
                    customers: init.customers || {},
                    triggers: init.triggers || {},
                    frequencies: init.frequencies || {},
                    imagePositions: init.imagePositions || {},
                    storeUrl: init.storeUrl || '',
                    updateUrl: init.updateUrl || '',
                    destroyUrl: init.destroyUrl || '',
                    csrf: init.csrf || '',
                    editable: false,
                    editing: false,
                    editingId: null,
                    form: { name: '', is_active: true, sort_order: 0, image_url: '', has_image: false, remove_image: false },
                    config: {},
                    selectedPagesText: '',

                    init() {
                        this.config = JSON.parse(JSON.stringify(this.defaults));
                        this.$watch('selectedPagesText', (value) => {
                            this.config.selected_pages = (value || '').split('\n').map(p => p.trim()).filter(p => p !== '');
                        });
                    },

                    managerUrl() {
                        return this.editingId
                            ? this.updateUrl.replace('__ID__', this.editingId)
                            : this.storeUrl;
                    },

                    toggle(field, value) {
                        const arr = this.config[field] || [];
                        const i = arr.indexOf(value);
                        if (i > -1) {
                            this.config[field] = arr.filter(x => x !== value);
                        } else {
                            this.config[field] = [...arr, value];
                        }
                    },

                    startCreate() {
                        this.editingId = null;
                        this.form = { name: '', is_active: true, sort_order: 0, image_url: '', has_image: false, remove_image: false };
                        this.config = JSON.parse(JSON.stringify(this.defaults));
                        this.selectedPagesText = '';
                        this.config.selected_pages = [];
                        const fileInput = document.getElementById('settingsImage');
                        if (fileInput) { fileInput.value = ''; }
                        this.editing = true;
                    },

                    startEdit(popup) {
                        const found = this.popups.find(p => p.id === popup.id);
                        if (!found) return;
                        this.editingId = popup.id;
                        this.form = {
                            name: found.name,
                            is_active: found.is_active,
                            sort_order: found.sort_order,
                            image_url: found.image_url || '',
                            has_image: found.has_image || false,
                            remove_image: false,
                        };
                        this.config = JSON.parse(JSON.stringify(found.config || this.defaults));
                        this.selectedPagesText = Array.isArray(this.config.selected_pages) ? this.config.selected_pages.join('\n') : '';
                        this.editing = true;
                    },

                    cancelEdit() {
                        this.editing = false;
                        this.editingId = null;
                    },

                    prepareSubmit() {
                        const imageInput = document.getElementById('settingsImage');
                        const hasNewFile = imageInput && imageInput.files.length > 0;
                        if (!this.editingId && !this.form.image_url && !hasNewFile) {
                            alert('Please upload a popup image.');
                            return;
                        }
                        const formData = new FormData();
                        formData.append('_token', this.csrf);
                        formData.append('_method', this.editingId ? 'PATCH' : 'POST');
                        formData.append('name', this.form.name);
                        formData.append('config', JSON.stringify(this.config));
                        formData.append('is_active', this.form.is_active ? '1' : '0');
                        formData.append('sort_order', this.form.sort_order || '0');
                        if (this.form.remove_image) {
                            formData.append('remove_image', '1');
                        }
                        if (hasNewFile) {
                            formData.append('image', imageInput.files[0]);
                        }
                        fetch(this.managerUrl(), {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-CSRF-TOKEN': this.csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        }).then(r => {
                            return r.json().then(data => {
                                if (data && data.redirect) {
                                    window.location.href = data.redirect;
                                } else if (r.ok) {
                                    window.location.href = r.url || this.managerUrl();
                                } else {
                                    alert('Failed to save popup.');
                                }
                            }).catch(() => {
                                if (r.redirected || r.ok) {
                                    window.location.href = r.url || this.managerUrl();
                                } else {
                                    alert('Failed to save popup.');
                                }
                            });
                        }).catch(e => {
                            alert('Failed to save popup: ' + e.message);
                        });
                    },

                    remove(popup) {
                        if (!confirm('Delete this popup?')) return;
                        fetch(this.destroyUrl.replace('__ID__', popup.id), {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': init.csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        }).then(() => {
                            this.popups = this.popups.filter(p => p.id !== popup.id);
                        });
                    },

                    previewImage(event) {
                        if (event.target.files && event.target.files[0]) {
                            const reader = new FileReader();
                            reader.onload = (e) => { this.form.image_url = e.target.result; };
                            reader.readAsDataURL(event.target.files[0]);
                        }
                    },
                };
            }
        </script>

        <script>
            function previewPopupImage(input) {
                const preview = document.getElementById('popupImagePreview');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
</x-admin-layout>
