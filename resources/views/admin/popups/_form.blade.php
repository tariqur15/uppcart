@php
    $isEdit = isset($popup) && $popup->exists;
@endphp

<div x-data="popupBuilder({{ json_encode($config) }})"
     x-init="register()"
     @submit="serialize()">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-30 -mx-4 sm:-mx-8 lg:-mx-12 px-4 sm:px-8 lg:px-12 py-4 bg-white/90 backdrop-blur-md border-b border-gray-200 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.popups.index') }}"
                   class="p-2 -ml-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-0.5">
                        <a href="{{ route('admin.popups.index') }}" class="hover:text-indigo-600 transition-colors">Popups</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-gray-600">{{ $isEdit ? 'Edit' : 'Create' }}</span>
                    </div>
                    <h1 class="text-lg font-semibold text-gray-900 leading-tight">{{ $isEdit ? 'Edit popup' : 'Create popup' }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.popups.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" form="popup-form"
                        class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $isEdit ? 'Update popup' : 'Create popup' }}
                </button>
            </div>
        </div>
    </div>

    <form id="popup-form" action="{{ $isEdit ? route('admin.popups.update', $popup) : route('admin.popups.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PATCH')
        @endif

        <input type="hidden" name="config" id="popupConfigJson" value="{{ json_encode($config) }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ============ MAIN COLUMN ============ --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Design Card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Design</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Upload your popup image and configure its appearance.</p>
                    </div>
                    <div class="px-6 py-5 space-y-5">

                        {{-- Image Upload --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Popup image <span class="text-red-500">*</span></label>
                            <div class="flex items-start gap-5">
                                <div class="w-40 h-28 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0 transition-colors hover:border-indigo-300 hover:bg-indigo-50/30" id="popupImagePreview">
                                    @if (! empty($config['image']))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($config['image']) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-center">
                                            <svg class="w-8 h-8 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-[10px] text-gray-400 mt-1">Preview</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 space-y-3">
                                    <label for="image" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 cursor-pointer transition shadow-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        Upload image
                                    </label>
                                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" class="hidden" onchange="previewPopupImage(this)">
                                    @if (! empty($config['image']))
                                        <label class="flex items-center gap-2 text-xs text-red-600 cursor-pointer">
                                            <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                            Remove current image
                                        </label>
                                    @endif
                                    <p class="text-xs text-gray-400">JPG, PNG, WebP or GIF. Max 4MB. Auto-scales to fit any screen.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Image Position & Size --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Image position</label>
                                <select x-model="config.image_position" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($imagePositions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Popup size</label>
                                <select x-model="config.width" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="xs">Small</option>
                                    <option value="sm">Medium</option>
                                    <option value="lg">Large</option>
                                </select>
                            </div>
                        </div>

                        {{-- Colors --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Colors</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="text-center">
                                    <div class="relative inline-block">
                                        <input type="color" x-model="config.bg_color" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1.5">Background</p>
                                </div>
                                <div class="text-center">
                                    <div class="relative inline-block">
                                        <input type="color" x-model="config.text_color" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1.5">Text</p>
                                </div>
                                <div class="text-center">
                                    <div class="relative inline-block">
                                        <input type="color" x-model="config.button_color" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1.5">Button</p>
                                </div>
                                <div class="text-center">
                                    <div class="relative inline-block">
                                        <input type="color" x-model="config.button_text_color" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1.5">Button text</p>
                                </div>
                            </div>
                        </div>

                        {{-- Overlay & Radius --}}
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
                    </div>
                </div>

                {{-- Content Card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Content</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Add your popup headline, message, and call-to-action.</p>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Title</label>
                            <input type="text" x-model="config.title" placeholder="e.g. Limited Time Offer"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
                            <textarea x-model="config.message" rows="3" placeholder="Details of the offer..."
                                      class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Button text</label>
                                <input type="text" x-model="config.button_text" placeholder="e.g. Shop Now"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Button URL</label>
                                <input type="text" x-model="config.button_url" placeholder="e.g. /collections/sale"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Display Rules Card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Display rules</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Control where and to whom the popup is shown.</p>
                    </div>
                    <div class="px-6 py-5 space-y-5">

                        {{-- Pages --}}
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Show popup on</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($pages as $value => $label)
                                    <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm transition-colors cursor-pointer"
                                           :class="config.show_on.includes('{{ $value }}') ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                                        <input type="checkbox" :checked="config.show_on.includes('{{ $value }}')" @change="toggle('show_on', '{{ $value }}')"
                                               class="sr-only">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="config.show_on.includes('{{ $value }}') ? 'bg-indigo-500' : 'bg-gray-300'"></span>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            <div x-show="config.show_on.includes('selected')" x-cloak class="mt-3">
                                <textarea x-model="selectedPagesText" rows="3" placeholder="One page per line, e.g. /about or full URL"
                                          class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                        </div>

                        {{-- Devices --}}
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Device</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($devices as $value => $label)
                                    <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm transition-colors cursor-pointer"
                                           :class="config.devices.includes('{{ $value }}') ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                                        <input type="checkbox" :checked="config.devices.includes('{{ $value }}')" @change="toggle('devices', '{{ $value }}')"
                                               class="sr-only">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="config.devices.includes('{{ $value }}') ? 'bg-indigo-500' : 'bg-gray-300'"></span>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Customers --}}
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Customer targeting</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($customers as $value => $label)
                                    <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm transition-colors cursor-pointer"
                                           :class="config.customers.includes('{{ $value }}') ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                                        <input type="checkbox" :checked="config.customers.includes('{{ $value }}')" @change="toggle('customers', '{{ $value }}')"
                                               class="sr-only">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="config.customers.includes('{{ $value }}') ? 'bg-indigo-500' : 'bg-gray-300'"></span>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ SIDEBAR ============ --}}
            <div class="space-y-6">

                {{-- Settings Card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Settings</h2>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name <span class="text-gray-400 font-normal">(internal)</span></label>
                            <input type="text" name="name" id="name" value="{{ $isEdit ? $popup->name : old('name') }}" required
                                   placeholder="e.g. Eid Sale Offer"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Active</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" @checked($isEdit ? $popup->is_active : true) class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1.5">Sort order</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ $isEdit ? $popup->sort_order : 0 }}" min="0"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Trigger Card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Trigger</h2>
                        <p class="text-xs text-gray-400 mt-0.5">When should the popup appear?</p>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">When to show</label>
                            <select x-model="config.trigger" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ($triggers as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
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
                </div>

                {{-- Frequency Card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Frequency</h2>
                        <p class="text-xs text-gray-400 mt-0.5">How often should visitors see this?</p>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Show frequency</label>
                            <select x-model="config.frequency" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ($frequencies as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm text-gray-700">Hide after closing</span>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :checked="config.hide_after_close" @change="config.hide_after_close = !config.hide_after_close" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </div>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm text-gray-700">Hide after CTA click</span>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :checked="config.hide_after_click" @change="config.hide_after_click = !config.hide_after_click" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Schedule Card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Schedule</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Set a date range for this popup.</p>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Start date &amp; time <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="datetime-local" x-model="config.start" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">End date &amp; time <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="datetime-local" x-model="config.end" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Live Preview --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Preview</h2>
                        <p class="text-xs text-gray-400 mt-0.5">How your popup will look.</p>
                    </div>
                    <div class="p-5 bg-gray-100 flex items-center justify-center min-h-[280px]">
                        <div class="w-full transition-all duration-200"
                             :class="{
                                'max-w-[200px]': config.width === 'xs',
                                'max-w-[280px]': config.width === 'sm',
                                'max-w-[400px]': config.width === 'lg',
                             }"
                             :style="'border-radius:' + (config.rounded === 'none' ? '0' : config.rounded === 'md' ? '0.75rem' : '1rem') + ';overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.12)'">
                            <div class="relative" :style="'background-color:' + config.bg_color">
                                {{-- Image --}}
                                <template x-if="form.image_url">
                                    <img :src="form.image_url" class="w-full h-auto block">
                                </template>
                                <template x-if="!form.image_url && config.image">
                                    <img :src="'/storage/' + config.image" class="w-full h-auto block">
                                </template>
                                <template x-if="!form.image_url && !config.image">
                                    <div class="w-full h-24 flex items-center justify-center" :style="'background-color:' + config.bg_color">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </template>

                                {{-- Content --}}
                                <div class="px-4 py-3 text-center space-y-2">
                                    <template x-if="config.title">
                                        <h3 class="text-sm font-bold" :style="'color:' + config.text_color" x-text="config.title"></h3>
                                    </template>
                                    <template x-if="config.message">
                                        <p class="text-xs leading-relaxed" :style="'color:' + config.text_color" x-text="config.message"></p>
                                    </template>
                                    <template x-if="config.button_text">
                                        <div class="pt-1">
                                            <span class="inline-block px-4 py-1.5 text-xs font-semibold rounded-lg"
                                                  :style="'background-color:' + config.button_color + ';color:' + config.button_text_color"
                                                  x-text="config.button_text"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
