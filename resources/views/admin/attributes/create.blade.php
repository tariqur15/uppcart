<x-admin-layout>
    <div x-data="attributeForm()" class="max-w-5xl mx-auto space-y-8">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Attribute</h1>
                <p class="mt-1 text-sm text-gray-500">Define a new product attribute with its options.</p>
            </div>
        </div>

        <form action="{{ route('admin.attributes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Card 1: Attribute Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Attribute Details</h2>
                </div>
                <div class="px-6 py-6 space-y-6">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full max-w-md rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g. Size">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Code --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required
                               class="w-full max-w-md rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g. colour, size">
                        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Value Type --}}
                    <div>
                        <label for="value_type" class="block text-sm font-medium text-gray-700 mb-1">Value Type <span class="text-red-500">*</span></label>
                        <div class="relative max-w-md">
                            <select name="value_type" id="value_type" x-model="valueType"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 appearance-none pr-10">
                                <option value="text_only">Text Only</option>
                                <option value="color_only">Color Only</option>
                                <option value="image_only">Image Only</option>
                                <option value="mixed">Mixed</option>
                            </select>
                            <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        @error('value_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <div class="mt-2 max-w-md">
                            <template x-if="valueType === 'text_only'">
                                <p class="text-xs text-gray-400">Options can contain text values only.</p>
                            </template>
                            <template x-if="valueType === 'color_only'">
                                <p class="text-xs text-gray-400">Options can contain colors only.</p>
                            </template>
                            <template x-if="valueType === 'image_only'">
                                <p class="text-xs text-gray-400">Options can contain images only.</p>
                            </template>
                            <template x-if="valueType === 'mixed'">
                                <p class="text-xs text-gray-400">Admin can mix text, color and image values.</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Options --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Options</h2>
                    <button type="button" @click="addOption()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Option
                    </button>
                </div>

                <div class="px-6 py-6 space-y-4">
                    {{-- Forced type notice --}}
                    <template x-if="isTypeForced()">
                        <div class="flex items-center gap-2 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Option type is forced to <strong x-text="getForcedTypeLabel()"></strong> because the value type is <strong x-text="valueTypeLabel()"></strong>.</span>
                        </div>
                    </template>

                    {{-- No options message --}}
                    <template x-if="options.length === 0">
                        <div class="text-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm">No options added yet. Click "Add Option" to get started.</p>
                        </div>
                    </template>

                    {{-- Option rows --}}
                    <template x-for="(option, index) in options" :key="option.id">
                        <div class="border border-gray-200 rounded-lg p-4 space-y-4 relative">
                            {{-- Remove button --}}
                            <button type="button" @click="removeOption(index)"
                                    class="absolute -top-2.5 -right-2.5 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition shadow-sm"
                                    title="Remove option">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {{-- Display Name --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Display Name</label>
                                    <input type="text"
                                           :name="`options[${index}][display_name]`"
                                           x-model="option.display_name"
                                           placeholder="Display name"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           :required="isActive(index)">
                                    @error('options.*.display_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Option Type --}}
                                <div x-show="!isTypeForced()" x-transition>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Option Type</label>
                                    <div class="relative">
                                        <select :name="`options[${index}][option_type]`"
                                                x-model="option.option_type"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 appearance-none pr-8">
                                            <option value="text">Text</option>
                                            <option value="color">Color</option>
                                            <option value="image">Image</option>
                                        </select>
                                        <svg class="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Hidden forced type input --}}
                                <template x-if="isTypeForced()">
                                    <input type="hidden" :name="`options[${index}][option_type]`" :value="getForcedType()">
                                </template>

                                {{-- Dynamic Value Field --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>

                                    {{-- Text input --}}
                                    <template x-if="resolvedType(index) === 'text'">
                                        <input type="text"
                                               :name="`options[${index}][text_value]`"
                                               x-model="option.text_value"
                                               placeholder="Green"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </template>

                                    {{-- Color picker + preview --}}
                                    <template x-if="resolvedType(index) === 'color'">
                                        <div class="flex items-center gap-3">
                                            <input type="color"
                                                   :name="`options[${index}][color_value]`"
                                                   x-model="option.color_value"
                                                   class="w-10 h-10 p-0.5 rounded border border-gray-300 cursor-pointer">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg border border-gray-200" :style="{ backgroundColor: option.color_value }"></div>
                                                <span class="text-xs text-gray-500 font-mono" x-text="option.color_value"></span>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Image upload --}}
                                    <template x-if="resolvedType(index) === 'image'">
                                        <div>
                                            <input type="file"
                                                   :name="`options[${index}][image]`"
                                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                                   @change="handleImageUpload(index, $event)"
                                                   class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                                            <p class="text-[10px] text-gray-400 mt-1">Accepted: jpg, jpeg, png, webp</p>
                                            <template x-if="option.image_preview">
                                                <div class="mt-2 relative inline-block">
                                                    <img :src="option.image_preview" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                                    <button type="button" @click="removeImage(index)"
                                                            class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition text-xs shadow-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>


                        </div>
                    </template>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.attributes.index') }}"
                   class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition shadow-sm">
                    Save
                </button>
            </div>
        </form>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('attributeForm', () => ({
                valueType: '{{ old('value_type', 'text_only') }}',
                options: [],
                nextId: 1,

                init() {
                    // Restore old options on validation error
                    const oldOptions = @json(old('options', []));
                    if (oldOptions.length > 0) {
                        oldOptions.forEach((opt) => {
                            this.options.push({
                                id: this.nextId++,
                                display_name: opt.display_name || '',
                                option_type: this.getForcedType() || opt.option_type || 'text',
                                text_value: opt.text_value || '',
                                color_value: opt.color_value || '#000000',
                                image_file: null,
                                image_preview: null,
                            });
                        });
                    }
                },

                addOption() {
                    this.options.push({
                        id: this.nextId++,
                        display_name: '',
                        option_type: this.getForcedType() || 'text',
                        text_value: '',
                        color_value: '#000000',
                        image_file: null,
                        image_preview: null,
                    });
                },

                removeOption(index) {
                    this.options.splice(index, 1);
                },

                resolvedType(index) {
                    const forced = this.getForcedType();
                    return forced || this.options[index]?.option_type || 'text';
                },

                getForcedType() {
                    const map = {
                        text_only: 'text',
                        color_only: 'color',
                        image_only: 'image',
                    };
                    return map[this.valueType] || null;
                },

                isTypeForced() {
                    return this.valueType !== 'mixed';
                },

                getForcedTypeLabel() {
                    const map = {
                        text_only: 'Text',
                        color_only: 'Color',
                        image_only: 'Image',
                    };
                    return map[this.valueType] || '';
                },

                valueTypeLabel() {
                    const map = {
                        text_only: 'Text Only',
                        color_only: 'Color Only',
                        image_only: 'Image Only',
                        mixed: 'Mixed',
                    };
                    return map[this.valueType] || '';
                },

                handleImageUpload(index, event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.options[index].image_file = file;
                    this.options[index].image_preview = URL.createObjectURL(file);
                },

                removeImage(index) {
                    this.options[index].image_file = null;
                    this.options[index].image_preview = null;
                    const input = document.querySelector(`input[name="options[${index}][image]"]`);
                    if (input) input.value = '';
                },

                isActive(index) {
                    return index < this.options.length;
                },

                hasErrors(index) {
                    return false;
                },

                getErrors(index) {
                    return [];
                }

            
            }));
        });
    </script>
    @endpush
</x-admin-layout>
