<x-admin-layout>
    <div x-data="reviewCreate()">
        <div class="sticky top-0 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-white/90 backdrop-blur-md border-b border-gray-200 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reviews.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <nav class="text-xs text-gray-400 mb-0.5">
                        <a href="{{ route('admin.reviews.index') }}" class="hover:text-gray-600 transition-colors">Reviews</a>
                        <span class="mx-1">/</span>
                        <span class="text-gray-700">New review</span>
                    </nav>
                    <h1 class="text-lg font-semibold text-gray-900 leading-tight">New Review</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reviews.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" form="review-form"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    Save
                </button>
            </div>
        </div>

        <div class="max-w-2xl">
            <form id="review-form" method="POST" action="{{ route('admin.reviews.store') }}">
                @csrf
                <input type="hidden" name="product_id" :value="selectedProductId">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Review Details</h3>

                    {{-- Product search --}}
                    <div class="mb-4 relative" @click.outside="productOpen = false">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Product</label>
                        <div class="relative">
                            <input type="text"
                                   x-model="productQuery"
                                   @focus="productOpen = true"
                                   @input="productOpen = true"
                                   placeholder="Search products..."
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <template x-if="selectedProductId">
                                <button type="button" @click="clearProduct()"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </template>
                        </div>

                            <div x-show="productOpen && filteredProducts.length > 0" x-cloak
                                 class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-72 overflow-y-auto">
                            <template x-for="p in filteredProducts" :key="p.id">
                                <button type="button" @click="selectProduct(p)"
                                        class="w-full text-left px-3 py-2.5 text-sm hover:bg-indigo-50 transition-colors border-b border-gray-50 last:border-b-0 flex items-center gap-3"
                                        :class="{ 'bg-indigo-50 text-indigo-700 font-medium': selectedProductId === p.id }">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden shrink-0">
                                        <img :src="p.image ? '/storage/' + p.image : 'https://placehold.co/160x160?text=' + encodeURIComponent(p.name.charAt(0))"
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate" x-text="p.name"></p>
                                        <p class="text-xs text-gray-400" x-text="p.sku"></p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 shrink-0" x-text="'$' + parseFloat(p.price).toFixed(2)"></span>
                                </button>
                            </template>
                        </div>

                        <div x-show="productOpen && filteredProducts.length === 0 && productQuery.length > 0" x-cloak
                             class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg p-4 text-sm text-gray-400 text-center">
                            No products found.
                        </div>

                        <template x-if="selectedProduct">
                            <p class="text-xs text-emerald-600 mt-1.5" x-text="'Selected: ' + selectedProduct.name"></p>
                        </template>
                        @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Customer --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Customer</label>

                        <div class="flex items-center gap-3 mb-3">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="customer_mode" value="existing" x-model="customerMode" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Select existing</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="customer_mode" value="new" x-model="customerMode" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Create new</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="customer_mode" value="guest" x-model="customerMode" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Guest</span>
                            </label>
                        </div>

                        {{-- Select existing --}}
                        <div x-show="customerMode === 'existing'" class="relative" @click.outside="userOpen = false">
                            <div class="relative">
                                <input type="text"
                                       x-model="userQuery"
                                       @focus="userOpen = true"
                                       @input="userOpen = true"
                                       placeholder="Search customers..."
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <template x-if="selectedUserId">
                                    <button type="button" @click="clearUser()"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="user_id" :value="selectedUserId">

                            <div x-show="userOpen && filteredUsers.length > 0" x-cloak
                                 class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="u in filteredUsers" :key="u.id">
                                    <button type="button" @click="selectUser(u)"
                                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 transition-colors border-b border-gray-50 last:border-b-0"
                                            :class="{ 'bg-indigo-50 text-indigo-700 font-medium': selectedUserId === u.id }">
                                        <span x-text="u.name"></span>
                                        <span class="text-gray-400 ml-1" x-text="u.email"></span>
                                    </button>
                                </template>
                            </div>

                            <div x-show="userOpen && filteredUsers.length === 0 && userQuery.length > 0" x-cloak
                                 class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg p-4 text-sm text-gray-400 text-center">
                                No customers found.
                            </div>

                            <template x-if="selectedUser">
                                <p class="text-xs text-emerald-600 mt-1.5" x-text="'Selected: ' + selectedUser.name + ' (' + selectedUser.email + ')'"></p>
                            </template>
                        </div>

                        {{-- Create new --}}
                        <div x-show="customerMode === 'new'" class="grid grid-cols-2 gap-3">
                            <div>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                       placeholder="Customer name"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                       placeholder="Email (optional)"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            @error('customer_name') <p class="text-red-500 text-xs mt-1 col-span-2">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-400 col-span-2">A new user account will be created automatically.</p>
                        </div>

                        {{-- Guest hint --}}
                        <p x-show="customerMode === 'guest'" class="text-xs text-gray-400 mt-1">No account linked — the review will be attributed to a guest.</p>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-4">
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-1.5">Rating</label>
                        <select name="rating" id="rating" required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select rating...</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }} {{ str_repeat('★', $i) }}</option>
                            @endfor
                        </select>
                        @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Title --}}
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Optional review title">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Body --}}
                    <div class="mb-4">
                        <label for="body" class="block text-sm font-medium text-gray-700 mb-1.5">Review</label>
                        <textarea name="body" id="body" rows="6" required
                                  class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"
                                  placeholder="Write the review...">{{ old('body') }}</textarea>
                        @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Moderation --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Moderation</h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_approved" value="1"
                               {{ old('is_approved') ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Approved</span>
                    </label>
                    <p class="text-xs text-gray-400 mt-1.5">Approved reviews are visible on the product page.</p>
                </div>
            </form>
        </div>
    </div>

    @pushOnce('scripts')
    <script>
        function reviewCreate() {
            return {
                products: JSON.parse('{!! $products->toJson() !!}'),
                users: JSON.parse('{!! $users->toJson() !!}'),
                productQuery: '',
                productOpen: false,
                selectedProductId: '{{ old('product_id') }}',
                userQuery: '',
                userOpen: false,
                selectedUserId: '{{ old('user_id') }}',
                customerMode: '{{ old('customer_mode', 'existing') }}',

                get selectedProduct() {
                    return this.products.find(p => p.id == this.selectedProductId) || null;
                },

                get filteredProducts() {
                    if (!this.productQuery) return this.products;
                    const q = this.productQuery.toLowerCase();
                    return this.products.filter(p =>
                        p.name.toLowerCase().includes(q) || (p.sku && p.sku.toLowerCase().includes(q))
                    );
                },

                selectProduct(p) {
                    this.selectedProductId = p.id;
                    this.productQuery = p.name;
                    this.productOpen = false;
                },

                clearProduct() {
                    this.selectedProductId = '';
                    this.productQuery = '';
                },

                get selectedUser() {
                    return this.users.find(u => u.id == this.selectedUserId) || null;
                },

                get filteredUsers() {
                    if (!this.userQuery) return this.users;
                    const q = this.userQuery.toLowerCase();
                    return this.users.filter(u =>
                        u.name.toLowerCase().includes(q) || (u.email && u.email.toLowerCase().includes(q))
                    );
                },

                selectUser(u) {
                    this.selectedUserId = u.id;
                    this.userQuery = u.name;
                    this.userOpen = false;
                },

                clearUser() {
                    this.selectedUserId = '';
                    this.userQuery = '';
                },
            };
        }
    </script>
    @endPushOnce
</x-admin-layout>