<x-app-layout>
    <div class="w-full mx-auto px-6 lg:px-10 pb-16 pt-6" style="font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; line-height: 24px; letter-spacing: normal;">

        {{-- Back to shop --}}
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 font-medium hover:text-gray-900 transition-colors mb-5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
            Back to shop
        </a>

        {{-- Page heading --}}
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                    <path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.6a2 2 0 002-1.6L23 6H6"/>
                </svg>
            </div>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-black mb-4">Shopping Cart</h1>
        </div>
        <p class="page-sub text-sm text-gray-500 mb-6 ml-[52px] {{ $items->isEmpty() ? 'hidden' : '' }}">{{ $items->sum('quantity') }} item{{ $items->sum('quantity') !== 1 ? 's' : '' }} in your cart</p>

        <div id="cartPanel">
            @include('cart.partials.panel')
        </div>

        {{-- Recommended products --}}
        @if (isset($recommended) && $recommended->isNotEmpty())
            <div class="mt-10">
                <div class="flex justify-between items-baseline mb-4">
                    <h2 class="text-xl lg:text-2xl font-extrabold text-black">Products You May Like</h2>
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold hover:underline" style="color: #7C3AED;">See All</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 auto-rows-fr items-stretch">
                    @foreach ($recommended as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <style>
        .shipping-progress { width: 100%; overflow: visible; }
        .shipping-message { text-align: center; font-size: 15px; color: #333; margin-bottom: 22px; }
        .shipping-message strong { font-weight: 700; }
        .progress-wrapper { position: relative; height: 85px; margin-right: 30px; overflow: visible; }
        .progress-bar { position: absolute; left: 0; right: 0; top: 31px; height: 12px; border-radius: 20px; background: #e1e4ed; overflow: visible; }
        .progress-fill { position: relative; height: 100%; border-radius: 20px; background: linear-gradient(90deg, #16a900, #28b91d, #16a900); background-size: 200% 100%; animation: greenFlow 1.2s linear infinite; }
        .progress-fill.blue { background: linear-gradient(90deg, #3B82F6, #60A5FA, #3B82F6); background-size: 200% 100%; animation: blueFlow 1.2s linear infinite; }
        @keyframes greenFlow { from { background-position: 0 0; } to { background-position: 200% 0; } }
        @keyframes blueFlow { from { background-position: 0 0; } to { background-position: 200% 0; } }
        .van-container {
            position: absolute;
            bottom: 46px;
            width: 85px;
            height: 44px;
            z-index: 5;
            left: -85px;
            animation: vanDrive 4s ease-in-out infinite, vanVibration 0.10s linear infinite;
        }
        .van-container.van-parked {
            left: calc(100% - 114px);
            animation: none;
            z-index: 15;
        }
        .van-container img { display: block; width: 100%; height: 100%; object-fit: contain; user-select: none; pointer-events: none; }
        @keyframes vanDrive {
            0%   { left: -85px; opacity: 1; }
            8%   { left: -40px; }
            45%  { left: calc((var(--target-pct, 50%) - 85px) * 0.55); }
            80%  { left: calc(var(--target-pct, 50%) - 88px); }
            88%  { left: calc(var(--target-pct, 50%) - 85px); opacity: 1; }
            94%  { left: calc(var(--target-pct, 50%) - 85px); opacity: 0; }
            95%  { left: -85px; opacity: 0; }
            100% { left: -85px; opacity: 1; }
        }
        @keyframes vanVibration {
            0%   { transform: translateY(0) rotate(0deg); }
            20%  { transform: translateY(-1px) rotate(-0.4deg); }
            40%  { transform: translateY(1px) rotate(0.3deg); }
            60%  { transform: translateY(-1px) rotate(-0.3deg); }
            80%  { transform: translateY(1px) rotate(0.4deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }
        .smoke { position: absolute; left: -30px; bottom: 6px; width: 60px; height: 35px; pointer-events: none; }
        .smoke span { position: absolute; display: block; border-radius: 50%; background: rgba(170,170,170,0.55); filter: blur(2px); animation: smokeMove 1.1s ease-out infinite; }
        .smoke span:nth-child(1) { width: 18px; height: 18px; left: 30px; bottom: 3px; }
        .smoke span:nth-child(2) { width: 14px; height: 14px; left: 15px; bottom: 8px; animation-delay: .25s; }
        .smoke span:nth-child(3) { width: 11px; height: 11px; left: 2px; bottom: 13px; animation-delay: .45s; }
        @keyframes smokeMove {
            0%   { transform: scale(.6) translate(0,0); opacity: .65; }
            50%  { transform: scale(1.1) translate(-8px,-5px); opacity: .45; }
            100% { transform: scale(1.7) translate(-22px,-12px); opacity: 0; }
        }
        .speed-line { position: absolute; left: -20px; height: 2px; background: #aaa; border-radius: 10px; animation: speedLine .45s linear infinite; }
        .speed-line:nth-child(1) { top: 14px; width: 20px; }
        .speed-line:nth-child(2) { top: 22px; width: 14px; animation-delay: .15s; }
        .speed-line:nth-child(3) { top: 30px; width: 22px; animation-delay: .3s; }
        @keyframes speedLine {
            from { opacity: .8; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(-18px); }
        }
        .shipping-point { position: absolute; right: -29px; top: -7px; width: 58px; height: 58px; border-radius: 50%; background: #fff; border: 2px solid #d6d9e0; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,.08); z-index: 10; flex-direction: column; }
        .shipping-point.reached { border-color: #16A34A; }
        .shipping-label { position: absolute; top: 56px; width: 90px; text-align: center; font-size: 13px; line-height: 17px; color: #333; left: 50%; transform: translateX(-50%); }
        @media (max-width: 600px) {
            .progress-wrapper { margin-right: 25px; }
            .van-container { width: 65px; height: 34px; }
            .van-container.van-parked { left: calc(100% - 94px); }
            .shipping-point { right: -25px; width: 50px; height: 50px; }
            .shipping-label { top: 48px; font-size: 12px; }
        }
    </style>
    @php
        $fsEnabledJs = $freeShippingType === 'product_weight'
            ? $freeShippingWeightThreshold > 0
            : ($freeShippingThreshold > 0 || in_array($freeShippingType, ['specific_products', 'specific_categories', 'specific_collections']));
    @endphp
    <script>
        const FREE_SHIPPING_CONFIG = {
            enabled: {{ $fsEnabledJs ? 'true' : 'false' }},
            type: @json($freeShippingType),
            threshold: {{ $freeShippingThreshold ?? 0 }},
            weightThreshold: {{ $freeShippingWeightThreshold ?? 0 }},
            productIds: @json($freeShippingProductIds ?? []),
            qualifies: {{ $freeShippingQualifies ? 'true' : 'false' }},
            symbol: @json($currencySymbol),
        };
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const currency = @json($currencySymbol);

        function setCheckState(box, checked) {
            if (checked) {
                box.style.backgroundColor = '#7C3AED';
                box.style.borderColor = '#7C3AED';
                box.querySelector('svg').classList.remove('hidden');
            } else {
                box.style.backgroundColor = '#FFFFFF';
                box.style.borderColor = '#D1D5DB';
                box.querySelector('svg').classList.add('hidden');
            }
        }

        function stepQty(btn, delta) {
            const input = btn.parentElement.querySelector('input[type="number"]');
            const row = btn.closest('.cart-row');
            const form = btn.closest('form');
            const newQty = Math.max(1, Math.min(99, parseInt(input.value) + delta));

            if (newQty === parseInt(input.value)) return;

            input.value = newQty;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: '_method=PATCH&quantity=' + newQty,
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    const unitPrice = parseFloat(row.dataset.unitPrice || 0);
                    row.querySelector('.row-subtotal').textContent = currency + (unitPrice * newQty).toLocaleString('en-US', { minimumFractionDigits: 0 });
                    updateSummary();
                    dispatchCartEvent(data.cart_count);
                }
            });
        }

        document.querySelectorAll('.cart-remove-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const row = this.closest('.cart-row');
                fetch(this.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                }).then(r => r.json()).then(data => {
                    row.style.transition = 'opacity 0.25s, transform 0.25s, max-height 0.25s, margin 0.25s, padding 0.25s';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(30px)';
                    row.style.maxHeight = row.offsetHeight + 'px';
                    requestAnimationFrame(() => {
                        row.style.maxHeight = '0';
                        row.style.margin = '0';
                        row.style.padding = '0';
                    });
                    setTimeout(() => row.remove(), 260);

                    setTimeout(() => {
                        updateSummary();
                        dispatchCartEvent(data.cart_count);
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Item removed from the cart', type: 'success' } }));
                    }, 280);
                });
            });
        });

        function updateSummary() {
            const rows = document.querySelectorAll('.cart-row');
            let totalQty = 0;
            let total = 0;
            let totalWeight = 0;
            const checkedRows = [];
            rows.forEach(r => {
                const cb = r.querySelector('.cart-checkbox');
                if (cb && cb.querySelector('svg').classList.contains('hidden')) return;
                const qty = parseInt(r.querySelector('input[name="quantity"]')?.value || 0);
                totalQty += qty;
                total += parseFloat(r.dataset.unitPrice || 0) * qty;
                totalWeight += parseFloat(r.dataset.weight || 0) * qty;
                checkedRows.push(r);
            });
            document.getElementById('sumSubtotal').textContent = currency + total.toLocaleString('en-US', { minimumFractionDigits: 0 });
            document.querySelectorAll('.summary-total').forEach(el => el.textContent = currency + total.toLocaleString('en-US', { minimumFractionDigits: 0 }));
            document.querySelector('.page-sub')?.replaceChildren(totalQty + ' item' + (totalQty !== 1 ? 's' : '') + ' in your cart');
            document.getElementById('selectedItemsText')?.replaceChildren(totalQty + ' selected item' + (totalQty !== 1 ? 's' : ''));

            const shippingLine = document.getElementById('shippingLine');
            const shippingHint = document.getElementById('shippingHint');
            let qualifies = false;

            if (FREE_SHIPPING_CONFIG.enabled) {
                if (FREE_SHIPPING_CONFIG.type === 'order_amount') {
                    qualifies = total >= FREE_SHIPPING_CONFIG.threshold;
                } else if (FREE_SHIPPING_CONFIG.type === 'product_weight') {
                    qualifies = FREE_SHIPPING_CONFIG.weightThreshold <= 0 || totalWeight <= FREE_SHIPPING_CONFIG.weightThreshold;
                } else if (FREE_SHIPPING_CONFIG.type === 'specific_products') {
                    qualifies = FREE_SHIPPING_CONFIG.productIds.length > 0
                        && checkedRows.length > 0
                        && checkedRows.every(r => FREE_SHIPPING_CONFIG.productIds.includes(parseInt(r.dataset.id)));
                } else {
                    qualifies = FREE_SHIPPING_CONFIG.qualifies;
                }
            }

            if (qualifies) {
                shippingLine.className = 'flex justify-between text-sm py-1.5';
                shippingLine.style.color = '#16A34A';
                shippingLine.innerHTML = '<span>Shipping</span><span class="font-medium">Free</span>';
                shippingHint.innerHTML = '<p class="text-xs font-medium" style="color: #16A34A;">Congratulations! You qualify for free shipping.</p>';
            } else {
                shippingLine.className = 'flex justify-between text-sm py-1.5 text-gray-500';
                shippingLine.style.color = '';
                shippingLine.innerHTML = '<span>Shipping</span><span>Calculated at checkout</span>';
                if (FREE_SHIPPING_CONFIG.type === 'order_amount' && FREE_SHIPPING_CONFIG.threshold > 0) {
                    const diff = Math.max(0, FREE_SHIPPING_CONFIG.threshold - total);
                    shippingHint.innerHTML = '<p class="text-xs text-gray-500">Add <span class="font-semibold text-black">' + currency + Math.round(diff).toLocaleString('en-US') + '</span> more for free shipping.</p>';
                } else if (FREE_SHIPPING_CONFIG.type === 'product_weight' && FREE_SHIPPING_CONFIG.weightThreshold > 0) {
                    const diff = Math.max(0, totalWeight - FREE_SHIPPING_CONFIG.weightThreshold);
                    shippingHint.innerHTML = '<p class="text-xs text-gray-500">Delivery charge applies — <span class="font-semibold text-black">' + diff.toFixed(2) + 'kg</span> over the free shipping limit.</p>';
                } else if (FREE_SHIPPING_CONFIG.type === 'specific_products') {
                    shippingHint.innerHTML = '<p class="text-xs text-gray-500">Delivery charge applies — remove non-free items from your cart for free shipping.</p>';
                } else {
                    shippingHint.innerHTML = '';
                }
            }

            const bar = document.getElementById('freeShippingBar');
            if (!bar) return;
            if (!FREE_SHIPPING_CONFIG.enabled) { bar.innerHTML = ''; return; }

            const flagSvgGreen = '<svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="29" height="29"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>';
            const flagSvgGray = '<svg viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="29" height="29"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>';
            const truckImg = '<div class="van-container"><div class="speed-line"></div><div class="speed-line"></div><div class="speed-line"></div><div class="smoke"><span></span><span></span><span></span></div><img src="https://merchant.pathao.com/assets/courier-truck-logo-animated.50cbdf0a.svg" alt="Delivery"></div>';
            const parkedVan = '<div class="van-container van-parked"><div class="speed-line"></div><div class="speed-line"></div><div class="speed-line"></div><div class="smoke"><span></span><span></span><span></span></div><img src="https://merchant.pathao.com/assets/courier-truck-logo-animated.50cbdf0a.svg" alt="Delivery"></div>';
            const pointSvg = qualifies ? flagSvgGreen : flagSvgGray;
            const pointLabelColor = qualifies ? ' style="color:#16A34A;"' : '';
            const pointOnly = '<div class="shipping-point' + (qualifies ? ' reached' : '') + '">' + pointSvg + '<div class="shipping-label"' + pointLabelColor + '>Free<br>Shipping</div></div>';

            if (FREE_SHIPPING_CONFIG.type === 'order_amount' && FREE_SHIPPING_CONFIG.threshold > 0) {
                const remaining = Math.max(0, FREE_SHIPPING_CONFIG.threshold - total);
                const pctVal = Math.min(100, (total / FREE_SHIPPING_CONFIG.threshold) * 100);
                const msg = qualifies
                    ? '<div class="shipping-message" style="font-size:15px;color:#16A34A;font-weight:600;">Congratulations, You\'ve unlocked <strong>FREE Shipping!</strong></div>'
                    : '<div class="shipping-message" style="font-size:15px;">Add items worth <strong>' + currency + Math.round(remaining).toLocaleString('en-US') + '</strong> more to unlock FREE Shipping</div>';
                if (qualifies) {
                    bar.innerHTML = '<div class="shipping-progress mb-4 px-2">' + msg + '<div class="progress-wrapper"><div class="progress-bar"><div class="progress-fill" style="width:100%;"></div></div>' + parkedVan + pointOnly + '</div></div>';
                } else {
                    bar.innerHTML = '<div class="shipping-progress mb-4 px-2">' + msg + '<div class="progress-wrapper" style="--target-pct:' + pctVal + '%;"><div class="progress-bar"><div class="progress-fill blue" style="width:' + pctVal + '%;"></div></div>' + truckImg + pointOnly + '</div></div>';
                }
            } else if (FREE_SHIPPING_CONFIG.type === 'product_weight' && FREE_SHIPPING_CONFIG.weightThreshold > 0) {
                const over = Math.max(0, totalWeight - FREE_SHIPPING_CONFIG.weightThreshold);
                const pctVal = FREE_SHIPPING_CONFIG.weightThreshold > 0 ? Math.min(100, (totalWeight / FREE_SHIPPING_CONFIG.weightThreshold) * 100) : 0;
                const msg = qualifies
                    ? '<div class="shipping-message" style="font-size:15px;color:#16A34A;font-weight:600;">Congratulations, You\'ve unlocked <strong>FREE Shipping!</strong></div>'
                    : '<div class="shipping-message" style="font-size:15px;">Your cart is over the free shipping limit — remove <strong>' + over.toFixed(1) + ' kg</strong> to get FREE Shipping</div>';
                if (qualifies) {
                    bar.innerHTML = '<div class="shipping-progress mb-4 px-2">' + msg + '<div class="progress-wrapper"><div class="progress-bar"><div class="progress-fill" style="width:100%;"></div></div>' + parkedVan + pointOnly + '</div></div>';
                } else {
                    bar.innerHTML = '<div class="shipping-progress mb-4 px-2">' + msg + '<div class="progress-wrapper" style="--target-pct:' + pctVal + '%;"><div class="progress-bar"><div class="progress-fill blue" style="width:' + pctVal + '%;"></div></div>' + truckImg + pointOnly + '</div></div>';
                }
            } else if (FREE_SHIPPING_CONFIG.type === 'specific_products') {
                const msg = qualifies
                    ? '<div class="shipping-message" style="font-size:15px;color:#16A34A;font-weight:600;">Congratulations, You\'ve unlocked <strong>FREE Shipping!</strong></div>'
                    : '<div class="shipping-message" style="font-size:15px;">Delivery charge applies — remove non-free items from your cart for free shipping</div>';
                if (qualifies) {
                    bar.innerHTML = '<div class="shipping-progress mb-4 px-2">' + msg + '<div class="progress-wrapper"><div class="progress-bar"><div class="progress-fill" style="width:100%;"></div></div>' + parkedVan + pointOnly + '</div></div>';
                } else {
                    bar.innerHTML = '<div class="shipping-progress mb-4 px-2">' + msg + '</div>';
                }
            } else if (qualifies) {
                bar.innerHTML = '<div class="shipping-progress mb-4 px-2"><div class="shipping-message" style="font-size:15px;color:#16A34A;font-weight:600;">Congratulations, You\'ve unlocked <strong>FREE Shipping!</strong></div><div class="progress-wrapper"><div class="progress-bar"><div class="progress-fill" style="width:100%;"></div></div>' + parkedVan + pointOnly + '</div></div>';
            } else {
                bar.innerHTML = '';
            }

            if (rows.length === 0) {
                document.getElementById('cartList').classList.add('hidden');
                document.querySelector('.select-all-row').classList.add('hidden');
                document.querySelector('.items-footer').classList.add('hidden');
                document.getElementById('emptyCart').classList.remove('hidden');
                document.getElementById('emptyCart').classList.add('flex');
            }
        }

        function dispatchCartEvent(count) {
            if (count !== undefined) {
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count } }));
            }
        }

        function bindCartInteractions() {
        document.querySelectorAll('.cart-checkbox').forEach(box => {
            box.addEventListener('click', function () {
                const svg = this.querySelector('svg');
                const isChecked = svg.classList.contains('hidden');
                setCheckState(this, isChecked);

                const total = document.querySelectorAll('.cart-checkbox').length;
                const checked = document.querySelectorAll('.cart-checkbox svg:not(.hidden)').length;
                document.getElementById('selectedCount').textContent = checked;

                const selectAll = document.getElementById('selectAllBox');
                if (checked === total) {
                    setCheckState(selectAll, true);
                } else if (checked === 0) {
                    setCheckState(selectAll, false);
                } else {
                    selectAll.style.backgroundColor = '#FFFFFF';
                    selectAll.style.borderColor = '#D1D5DB';
                    selectAll.style.backgroundColor = '#7C3AED';
                    selectAll.style.borderColor = '#7C3AED';
                    selectAll.querySelector('svg').classList.add('hidden');
                }
                updateSummary();
            });
        });

        document.getElementById('selectAllBox')?.addEventListener('click', function () {
            const allChecked = document.querySelectorAll('.cart-checkbox svg:not(.hidden)').length === document.querySelectorAll('.cart-checkbox').length;
            const newState = !allChecked;
            document.querySelectorAll('.cart-checkbox').forEach(box => setCheckState(box, newState));
            setCheckState(this, newState);
            document.getElementById('selectedCount').textContent = newState ? document.querySelectorAll('.cart-checkbox').length : 0;
            updateSummary();
        });
        }

        bindCartInteractions();

        async function refreshCartPanel() {
            try {
                const resp = await fetch('{{ route('cart.index') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                const data = await resp.json();
                if (!data.success) return;
                const panel = document.getElementById('cartPanel');
                if (!panel) return;
                panel.innerHTML = data.html;
                if (typeof data.free_shipping_qualifies === 'boolean') {
                    FREE_SHIPPING_CONFIG.qualifies = data.free_shipping_qualifies;
                }
                bindCartInteractions();
                updateSummary();
                document.querySelector('.page-sub')?.classList.toggle('hidden', document.querySelectorAll('.cart-row').length === 0);
            } catch (e) {
                console.error('refreshCartPanel failed:', e);
            }
        }

        window.addEventListener('cart-added', () => refreshCartPanel());
    </script>
    @endpush
</x-app-layout>
