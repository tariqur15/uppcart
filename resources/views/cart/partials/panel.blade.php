        @if($items->isEmpty())
            {{-- Empty cart --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-[0_1px_2px_rgba(17,24,39,0.03),0_8px_22px_-14px_rgba(17,24,39,0.10)] flex flex-col items-center text-center gap-4 py-20 text-gray-400">
                <svg class="w-14 h-14 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                    <path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.6a2 2 0 002-1.6L23 6H6"/>
                </svg>
                <h2 class="text-xl font-bold text-black mt-1">Your cart is empty</h2>
                <p class="text-sm text-gray-500">Your cart is currently empty. Browse the shop and add</br> something you love.</p>
                <a href="{{ route('products.index') }}"
                   class="mt-2 text-white border-none rounded-xl px-7 py-3 font-semibold hover:opacity-90 transition-colors"
                   style="background-color: #7C3AED; font-size: 14px; line-height: 20px;">
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-6 items-start">

                {{-- LEFT: Cart items --}}
                <div>
                    <div id="freeShippingBar">
                    @if($freeShippingThreshold > 0 && $freeShippingType === 'order_amount')
                        @php
                            $remaining = max(0, $freeShippingThreshold - $total);
                            $pct = min(100, ($total / $freeShippingThreshold) * 100);
                        @endphp
                        @if($freeShippingQualifies)
                            <div class="shipping-progress mb-4 px-2">
                                <div class="shipping-message" style="font-size:15px;color:#16A34A;font-weight:600;">Congratulations, You've unlocked <strong>FREE Shipping!</strong></div>
                                <div class="progress-wrapper">
                                    <div class="progress-bar"><div class="progress-fill" style="width:100%;"></div></div>
                                    <div class="van-container van-parked">
                                        <div class="speed-line"></div><div class="speed-line"></div><div class="speed-line"></div>
                                        <div class="smoke"><span></span><span></span><span></span></div>
                                        <img src="https://merchant.pathao.com/assets/courier-truck-logo-animated.50cbdf0a.svg" alt="Delivery">
                                    </div>
                                    <div class="shipping-point reached">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="29" height="29"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                                        <div class="shipping-label" style="color:#16A34A;">Free<br>Shipping</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="shipping-progress mb-4 px-2">
                                <div class="shipping-message" style="font-size:15px;">Add items worth <strong>{{ $currencySymbol }}{{ number_format($remaining, 0) }}</strong> more to unlock FREE Shipping</div>
                                <div class="progress-wrapper" style="--target-pct: {{ $pct }}%;">
                                    <div class="progress-bar"><div class="progress-fill blue" style="width:{{ $pct }}%;"></div></div>
                                    <div class="van-container">
                                        <div class="speed-line"></div><div class="speed-line"></div><div class="speed-line"></div>
                                        <div class="smoke"><span></span><span></span><span></span></div>
                                        <img src="https://merchant.pathao.com/assets/courier-truck-logo-animated.50cbdf0a.svg" alt="Delivery">
                                    </div>
                                    <div class="shipping-point">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="29" height="29"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                                        <div class="shipping-label">Free<br>Shipping</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @elseif($freeShippingWeightThreshold > 0 && $freeShippingType === 'product_weight')
                        @php
                            $overWeight = max(0, $cartWeight - $freeShippingWeightThreshold);
                            $pct = min(100, ($cartWeight / $freeShippingWeightThreshold) * 100);
                        @endphp
                        @if($freeShippingQualifies)
                            <div class="shipping-progress mb-4 px-2">
                                <div class="shipping-message" style="font-size:15px;color:#16A34A;font-weight:600;">Congratulations, You've unlocked <strong>FREE Shipping!</strong></div>
                                <div class="progress-wrapper">
                                    <div class="progress-bar"><div class="progress-fill" style="width:100%;"></div></div>
                                    <div class="van-container van-parked">
                                        <div class="speed-line"></div><div class="speed-line"></div><div class="speed-line"></div>
                                        <div class="smoke"><span></span><span></span><span></span></div>
                                        <img src="https://merchant.pathao.com/assets/courier-truck-logo-animated.50cbdf0a.svg" alt="Delivery">
                                    </div>
                                    <div class="shipping-point reached">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="29" height="29"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                                        <div class="shipping-label" style="color:#16A34A;">Free<br>Shipping</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="shipping-progress mb-4 px-2">
                                <div class="shipping-message" style="font-size:15px;">Your cart is over the free shipping limit — remove <strong>{{ number_format($overWeight, 1) }} kg</strong> to get FREE Shipping</div>
                                <div class="progress-wrapper" style="--target-pct: {{ $pct }}%;">
                                    <div class="progress-bar"><div class="progress-fill blue" style="width:{{ $pct }}%;"></div></div>
                                    <div class="van-container">
                                        <div class="speed-line"></div><div class="speed-line"></div><div class="speed-line"></div>
                                        <div class="smoke"><span></span><span></span><span></span></div>
                                        <img src="https://merchant.pathao.com/assets/courier-truck-logo-animated.50cbdf0a.svg" alt="Delivery">
                                    </div>
                                    <div class="shipping-point">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="29" height="29"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                                        <div class="shipping-label">Free<br>Shipping</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @elseif($freeShippingQualifies)
                        <div class="shipping-progress mb-4 px-2">
                            <div class="shipping-message" style="font-size:15px;color:#16A34A;font-weight:600;">Congratulations, You've unlocked <strong>FREE Shipping!</strong></div>
                            <div class="progress-wrapper">
                                <div class="progress-bar"><div class="progress-fill" style="width:100%;"></div></div>
                                <div class="van-container van-parked">
                                    <div class="speed-line"></div><div class="speed-line"></div><div class="speed-line"></div>
                                    <div class="smoke"><span></span><span></span><span></span></div>
                                    <img src="https://merchant.pathao.com/assets/courier-truck-logo-animated.50cbdf0a.svg" alt="Delivery">
                                </div>
                                <div class="shipping-point reached">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="29" height="29"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                                    <div class="shipping-label" style="color:#16A34A;">Free<br>Shipping</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl shadow-[0_1px_2px_rgba(17,24,39,0.03),0_8px_22px_-14px_rgba(17,24,39,0.10)] overflow-hidden">

                        {{-- Select all --}}
                        <div class="select-all-row flex items-center gap-3 px-6 py-4 border-b border-gray-200 text-sm font-semibold text-black">
                            <div id="selectAllBox"
                                 class="w-5 h-5 rounded border-[1.5px] border-[#7C3AED] flex items-center justify-center cursor-pointer shrink-0 text-white bg-[#7C3AED] transition-colors duration-150">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                            </div>
                            <span>Select all items (<span id="selectedCount">{{ $items->count() }}</span>/<span id="totalCount">{{ $items->count() }}</span>)</span>
                        </div>

                        {{-- Cart rows --}}
                        <div id="cartList">
                            @foreach ($items as $item)
                                @php
                                    $isSession = str_starts_with((string) ($item->id ?? ''), 'session_');
                                    $unitPrice = $item->product->sale_price ?? $item->product->price;
                                    $subtotal = $unitPrice * $item->quantity;
                                    $stock = $item->product->stock;
                                @endphp
                                <div class="cart-row flex items-center gap-4 px-6 py-5 border-b border-gray-200 last:border-b-0 bg-[#F9FAFB]"
                                     data-id="{{ $item->product_id }}"
                                     data-unit-price="{{ $unitPrice }}"
                                     data-weight="{{ $item->product->weight ?? 0 }}"
                                     data-is-session="{{ $isSession ? '1' : '0' }}">
                                    {{-- Checkbox --}}
                                    <div class="cart-checkbox w-5 h-5 rounded border-[1.5px] border-[#7C3AED] flex items-center justify-center cursor-pointer shrink-0 text-white bg-[#7C3AED] transition-colors duration-150">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                                    </div>

                                    {{-- Thumbnail --}}
                                    <a href="{{ route('products.show', $item->product) }}" class="w-20 h-20 rounded-xl shrink-0 overflow-hidden flex items-center justify-center bg-gray-100">
                                        @if ($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        @endif
                                    </a>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('products.show', $item->product) }}" class="text-base font-bold text-black hover:text-[#7C3AED] transition-colors leading-tight line-clamp-1">
                                            {{ $item->product->name }}
                                        </a>
                                        <div class="flex items-center gap-2.5 mt-1.5 flex-wrap">
                                            @php
                                                $variantDisplay = null;
                                                if ($item->variant && is_array($item->variant)) {
                                                    $variantDisplay = collect($item->variant)->map(fn($v, $k) => $k . ': ' . $v)->implode(', ');
                                                } elseif ($item->variant && is_string($item->variant) && $item->variant !== '') {
                                                    $variantDisplay = $item->variant;
                                                } elseif (isset($item->product->variant) && is_string($item->product->variant) && $item->product->variant !== '') {
                                                    $variantDisplay = $item->product->variant;
                                                } elseif (isset($item->product->variant) && is_array($item->product->variant) && count($item->product->variant) > 0) {
                                                    $variantDisplay = collect($item->product->variant)->map(fn($v, $k) => $k . ': ' . $v)->implode(', ');
                                                }
                                            @endphp
                                            @if ($variantDisplay)
                                                @foreach(explode(' / ', $variantDisplay) as $v)
                                                    <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-md">{{ trim($v) }}</span>
                                                @endforeach
                                            @endif
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $stock > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                                                {{ $stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3 mt-2.5 text-sm text-gray-500">
                                            <span>{{ $currencySymbol }}{{ number_format($unitPrice, 0) }} / unit</span>
                                            <form action="{{ $isSession ? route('cart.update-session', $item->product_id) : route('cart.update', $item) }}" method="POST" class="inline-flex items-center">
                                                @csrf
                                                @method('PATCH')
                                                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                                    <button type="button" onclick="stepQty(this,-1)" class="w-7 h-7 bg-gray-50 hover:bg-purple-50 flex items-center justify-center text-sm border-none cursor-pointer text-gray-700 leading-none">−</button>
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" readonly class="w-8 text-center text-sm border-none bg-transparent font-medium text-gray-900 [-moz-appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none">
                                                    <button type="button" onclick="stepQty(this,1)" class="w-7 h-7 bg-gray-50 hover:bg-purple-50 flex items-center justify-center text-sm border-none cursor-pointer text-gray-700 leading-none">+</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Right: delete + total --}}
                                    <div class="flex flex-col items-end gap-4 ml-auto">
                                        <form action="{{ $isSession ? route('cart.destroy-session', $item->product_id) : route('cart.destroy', $item) }}" method="POST" class="cart-remove-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer bg-transparent border-none p-1">
                                                <svg class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor">
                                                    <path d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/>
                                                </svg>
                                            </button>
                                        </form>
                                        <span class="row-subtotal text-lg font-bold" style="color: #7C3AED;">{{ $currencySymbol }}{{ number_format($subtotal, 0) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Empty state (hidden, JS toggles) --}}
                        <div id="emptyCart" class="hidden flex-col items-center text-center gap-4 py-20 text-gray-400">
                            <svg class="w-14 h-14 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                                <path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.6a2 2 0 002-1.6L23 6H6"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-black">Your cart is empty</h3>
                            <a href="{{ route('products.index') }}" class="mt-2 text-white border-none rounded-xl px-7 py-3 font-semibold hover:opacity-90 transition-colors"
                               style="background-color: #7C3AED; font-size: 14px; line-height: 20px;">
                                Continue Shopping
                            </a>
                        </div>

                        {{-- Footer --}}
                        <div class="items-footer flex justify-between items-center px-6 py-4">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-base font-semibold hover:underline" style="color: #7C3AED;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                Continue Shopping
                            </a>
                            <button type="button"
                                    x-data="{ clearing: false }"
                                    @click="
                                        if (!confirm('Delete all items from your cart?')) return;
                                        clearing = true;
                                        fetch('{{ route('cart.clear') }}', {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        }).then(r => r.json()).then(data => {
                                            if (data.success) {
                                                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: 0 } }));
                                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Cart cleared', type: 'success' } }));
                                                location.reload();
                                            }
                                        }).finally(() => { clearing = false; });
                                    "
                                    :disabled="clearing"
                                    class="inline-flex items-center gap-1.5 text-base font-semibold text-red-500 hover:text-red-600 transition-colors disabled:opacity-50">
                                <svg class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor">
                                    <path d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/>
                                </svg>
                                <span x-text="clearing ? 'Deleting...' : 'Delete all'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Summary --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-[0_1px_2px_rgba(17,24,39,0.03),0_8px_22px_-14px_rgba(17,24,39,0.10)] p-6 lg:sticky lg:top-5">
                    <h2 class="text-xl font-extrabold text-black">Order Summary</h2>
                    <p id="selectedItemsText" class="text-sm text-gray-500 mt-1 mb-5">{{ $items->count() }} selected item{{ $items->count() !== 1 ? 's' : '' }}</p>

                    <div x-data="{
                        couponInput: '',
                        couponApplied: false,
                        couponCode: '',
                        couponDiscount: 0,
                        couponMsg: '',
                        couponMsgType: '',
                        applying: false,
                        async apply() {
                            const code = this.couponInput.trim().toUpperCase();
                            if (!code) return;
                            this.applying = true;
                            this.couponMsg = '';
                            this.couponApplied = false;
                            this.couponDiscount = 0;
                            try {
                                const res = await fetch('{{ route('checkout.validate-promo') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ code, subtotal: {{ $total }} })
                                });
                                const data = await res.json();
                                if (data.valid) {
                                    this.couponApplied = true;
                                    this.couponCode = data.code;
                                    this.couponDiscount = data.discount;
                                    this.couponMsg = data.message;
                                    this.couponMsgType = 'success';
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                                } else {
                                    this.couponApplied = false;
                                    this.couponMsg = data.message;
                                    this.couponMsgType = 'error';
                                }
                            } catch (e) {
                                this.couponMsg = 'Something went wrong. Please try again.';
                                this.couponMsgType = 'error';
                            }
                            this.applying = false;
                        }
                    }" class="mb-5">
                        <div class="flex items-center gap-2">
                            <input type="text"
                                   x-model="couponInput"
                                   :disabled="couponApplied"
                                   placeholder="Enter coupon code"
                                   class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2.5 bg-white focus:outline-none disabled:bg-gray-50 disabled:text-gray-400"
                                   style="border-color: #E5E7EB; color: #000; font-size: 14px;"
                                   onfocus="this.style.borderColor='#7C3AED'"
                                   onblur="this.style.borderColor='#E5E7EB'">
                            <button type="button"
                                    @click="apply()"
                                    :disabled="applying || couponApplied"
                                    class="px-4 py-2.5 font-semibold rounded-lg text-white hover:opacity-90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                                    style="background-color: #7C3AED; font-size: 14px; line-height: 20px;">
                                <span x-text="couponApplied ? 'Applied' : (applying ? 'Checking...' : 'Apply')"></span>
                            </button>
                        </div>
                        <template x-if="couponMsg">
                            <p class="text-xs mt-2 ml-0.5" :class="couponMsgType === 'success' ? 'text-emerald-600' : 'text-red-500'" x-text="couponMsg"></p>
                        </template>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm text-gray-500 py-1.5">
                            <span>Subtotal</span>
                            <span id="sumSubtotal">{{ $currencySymbol }}{{ number_format($total, 0) }}</span>
                        </div>
                        <div id="shippingLine" class="flex justify-between text-sm py-1.5" style="{{ $freeShippingQualifies ? 'color: #16A34A;' : 'color: #6B7280;' }}">
                            <span>Shipping</span>
                            @if ($freeShippingQualifies)
                                <span class="font-medium">Free</span>
                            @else
                                <span>Calculated at checkout</span>
                            @endif
                        </div>
                        <div id="shippingHint">
                            @if ($freeShippingQualifies)
                                <p class="text-xs font-medium" style="color: #16A34A;">Congratulations! You qualify for free shipping.</p>
                            @elseif ($freeShippingType === 'order_amount' && $freeShippingThreshold > 0)
                                <p class="text-xs text-gray-500">Add <span class="font-semibold text-black">{{ $currencySymbol }}{{ number_format($freeShippingThreshold - $total, 0) }}</span> more for free shipping.</p>
                            @elseif ($freeShippingType === 'product_weight' && $freeShippingWeightThreshold > 0)
                                <p class="text-xs text-gray-500">Delivery charge applies — <span class="font-semibold text-black">{{ number_format(max(0, $cartWeight - $freeShippingWeightThreshold), 2) }}kg</span> over the free shipping limit.</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-between text-lg font-extrabold text-black border-t border-gray-200 mt-3 pt-4">
                        <span>Total</span>
                        <span class="summary-total" style="color: #7C3AED;">{{ $currencySymbol }}{{ number_format($total, 0) }}</span>
                    </div>

                    <a href="{{ route('checkout.loading') }}"
                       class="mt-4 w-full text-white border-none rounded-xl py-3.5 font-bold hover:opacity-90 transition-colors flex items-center justify-center gap-2"
                       style="background-color: #7C3AED; font-size: 14px; line-height: 20px;">
                        Proceed to Checkout
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>

                    {{-- Trust badges --}}
                    <div class="flex justify-between mt-5 border-t border-gray-200 pt-4">
                        <div class="flex-1 text-center">
                            <svg class="w-5 h-5 mx-auto mb-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #7C3AED;">
                                <rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 018 0v3"/>
                            </svg>
                            <span class="text-xs text-gray-500">Secure Checkout</span>
                        </div>
                        <div class="flex-1 text-center">
                            <svg class="w-5 h-5 mx-auto mb-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #7C3AED;">
                                <path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                            </svg>
                            <span class="text-xs text-gray-500">Easy Returns</span>
                        </div>
                        <div class="flex-1 text-center">
                            <svg class="w-5 h-5 mx-auto mb-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #7C3AED;">
                                <path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/>
                            </svg>
                            <span class="text-xs text-gray-500">Support 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
