@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    body, .oe-card h2, .oe-field label, .oe-btn, .oe-search, .oe-textarea, .oe-input, .oe-select, .oe-btn {
        font-family: 'Inter', sans-serif !important;
    }
    .oe-mono, table.oe-items td.mono, table.oe-items th.num, .oe-page-head .order-ref .mono, .oe-summary .val, .oe-row-total {
        font-family: 'JetBrains Mono', monospace !important;
    }
    :root {
        --bg: #EEF1F4;
        --card: #FFFFFF;
        --ink: #1C2024;
        --ink-soft: #7A828C;
        --border: #ECEEF1;
        --red: #A31E22;
        --red-dark: #5C0E12;
        --green: #12B76A;
        --purple: #6C4EE8;
        --radius: 16px;
        --shadow: 0 1px 2px rgba(20,20,30,0.03), 0 10px 24px -14px rgba(20,20,30,0.10);
    }

    .oe-wrap { max-width: 1080px; margin: 0 auto; }

    .oe-page-head { margin-bottom: 20px; }
    .oe-page-head h1 { font-size: 20px; font-weight: 700; margin: 0 0 4px; font-family: 'JetBrains Mono', monospace; }
    .oe-page-head .order-ref { font-size: 12px; color: var(--ink-soft); }

    .oe-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px; }

    .oe-card {
        background: var(--card); border-radius: var(--radius);
        box-shadow: var(--shadow); padding: 20px 24px;
    }
    .oe-card h2 { font-size: 15.5px; font-weight: 700; margin: 0 0 16px; }

    .oe-field { margin-bottom: 14px; }
    .oe-field:last-child { margin-bottom: 0; }
    .oe-field label { display: block; font-size: 11px; color: var(--ink-soft); margin-bottom: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; }

    .oe-input {
        width: 100%; font-family: 'Inter', sans-serif; font-size: 13px;
        padding: 9px 12px; border-radius: 9px; border: 1.5px solid var(--border);
        background: #fff; color: var(--ink); outline: none; transition: border-color .15s ease;
    }
    .oe-input:focus { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(108,78,232,0.08); }
    .oe-textarea { resize: vertical; min-height: 76px; font-family: 'Inter', sans-serif; }

    .oe-select {
        width: 100%; font-family: 'Inter', sans-serif; font-size: 13px;
        padding: 9px 12px; border-radius: 9px; border: 1.5px solid var(--border);
        background: #fff; color: var(--ink); outline: none;
    }
    .oe-select:focus { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(108,78,232,0.08); }

    .oe-panel {
        background: var(--card); border-radius: var(--radius);
        box-shadow: var(--shadow); margin-bottom: 18px; overflow: hidden;
    }
    .oe-panel-inner { padding: 20px 24px; }
    .oe-items-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0; }
    .oe-items-head h2 { font-size: 15.5px; font-weight: 700; margin: 0; }
    .oe-search {
        width: 320px; font-size: 13px; padding: 9px 14px; border-radius: 9px;
        border: 1.5px solid var(--border); background: #fff; color: var(--ink); outline: none;
    }
    .oe-search:focus { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(108,78,232,0.08); }
    .oe-search::placeholder { color: var(--ink-soft); }

    .oe-search-dropdown {
        position: absolute; top: 100%; left: 0; right: 0; z-index: 20;
        background: #fff; border: 1.5px solid var(--border); border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12); max-height: 240px; overflow-y: auto;
        display: none; margin-top: 4px;
    }
    .oe-search-dropdown.open { display: block; }
    .oe-search-item {
        display: flex; align-items: center; gap: 10px; padding: 10px 14px;
        cursor: pointer; font-size: 13px; border-bottom: 1px solid var(--border);
        transition: background .1s;
    }
    .oe-search-item:last-child { border-bottom: none; }
    .oe-search-item:hover { background: var(--bg); }
    .oe-search-item img { width: 32px; height: 32px; border-radius: 8px; object-fit: cover; }
    .oe-search-item .si-name { font-weight: 600; }
    .oe-search-item .si-sku { font-size: 11px; color: var(--ink-soft); }
    .oe-search-item .si-price { margin-left: auto; font-weight: 600; color: var(--purple); font-family: 'JetBrains Mono', monospace; }

    table.oe-items { width: 100%; border-collapse: collapse; }
    table.oe-items thead th {
        text-align: left; font-size: 11px; font-weight: 600; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.04em;
        background: var(--bg); padding: 11px 24px; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
    }
    table.oe-items thead th.num { text-align: left; width: 90px; }
    table.oe-items tbody td {
        padding: 12px 24px; border-bottom: 1px solid var(--border); vertical-align: middle; font-size: 13.5px;
    }
    .oe-product-cell { display: flex; align-items: center; gap: 12px; }
    .oe-product-thumb {
        width: 40px; height: 40px; border-radius: 9px; flex-shrink: 0; overflow: hidden;
        background: linear-gradient(135deg, var(--purple-tint, #F1EEFE), #fff);
        display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; color: var(--purple);
    }
    .oe-product-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .oe-cell-input {
        width: 90px; font-size: 13px; padding: 8px 10px; border-radius: 7px;
        border: 1.5px solid var(--border); background: #fff; color: var(--ink); outline: none; text-align: left;
    }
    .oe-cell-input:focus { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(108,78,232,0.08); }
    .oe-row-total { color: var(--purple); font-weight: 600; font-family: 'JetBrains Mono', monospace; }
    .oe-delete-btn {
        background: none; border: none; color: var(--red); cursor: pointer; padding: 6px;
        display: flex; align-items: center; justify-content: center; border-radius: 6px;
    }
    .oe-delete-btn:hover { background: rgba(163,30,34,0.06); }

    .oe-totals-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
    .oe-totals-left .trow {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; gap: 14px;
    }
    .oe-totals-left .trow label { font-size: 13px; color: var(--ink); font-weight: 500; white-space: nowrap; }
    .oe-totals-left .trow input {
        width: 150px; text-align: left; font-size: 13px; padding: 8px 12px;
        border-radius: 9px; border: 1.5px solid var(--border); font-family: 'JetBrains Mono', monospace;
    }
    .oe-totals-left .trow input:focus { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(108,78,232,0.08); }
    .oe-summary { margin-top: 6px; }
    .oe-summary-line { display: flex; justify-content: space-between; font-size: 13px; color: var(--ink-soft); padding: 5px 8px; }
    .oe-summary-line .val { color: var(--ink); font-weight: 600; }
    .oe-summary-line.grand { font-size: 15.5px; font-weight: 700; color: var(--ink); padding-top: 10px; margin-top: 4px; border-top: 1px dashed var(--border); }
    .oe-summary-line.grand .val { color: var(--red); font-weight: 700; }
    .oe-notes-col label { display: block; font-size: 11px; color: var(--ink-soft); margin-bottom: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; }
    .oe-notes-col textarea { min-height: 130px; }

    .oe-action-row { display: flex; gap: 10px; }
    .oe-btn {
        font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 600; padding: 10px 16px;
        border-radius: 10px; cursor: pointer; border: 1.5px solid var(--border); text-decoration: none;
        display: inline-flex; align-items: center; gap: 7px; transition: background .15s ease;
    }
    .oe-btn-save { background: var(--red); color: #fff; border-color: var(--red); }
    .oe-btn-save:hover { background: var(--red-dark); border-color: var(--red-dark); }
    .oe-btn-cancel { background: #fff; color: var(--ink); }
    .oe-btn-cancel:hover { background: #F7F5F3; }

    @media (max-width: 800px) {
        .oe-row2, .oe-totals-grid { grid-template-columns: 1fr; }
        .oe-items-head { flex-direction: column; align-items: flex-start; gap: 10px; }
        .oe-search { width: 100%; }
    }
</style>
@endpush

<x-admin-layout>
    <div class="oe-wrap oe-mono">
        <div class="oe-page-head">
            <h1>Edit Order</h1>
            <div class="order-ref">Order #: <span class="mono">{{ $order->order_number }}</span></div>
        </div>

        <form action="{{ route('admin.orders.update', $order) }}" method="POST" id="orderForm">
            @csrf
            @method('PUT')

            <div class="oe-row2">
                {{-- Billing --}}
                <div class="oe-card">
                    <h2>Billing</h2>
                    <div class="oe-field">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="oe-input" value="{{ old('first_name', $order->first_name) }}">
                    </div>
                    <div class="oe-field">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="oe-input" value="{{ old('last_name', $order->last_name) }}">
                    </div>
                    <div class="oe-field">
                        <label>Phone</label>
                        <input type="text" name="phone" class="oe-input" value="{{ old('phone', $order->phone) }}">
                    </div>
                    <div class="oe-field">
                        <label>Email</label>
                        <input type="text" name="email" class="oe-input" value="{{ old('email', $order->email) }}">
                    </div>
                    <div class="oe-field">
                        <label>Address</label>
                        <textarea name="address" class="oe-input oe-textarea">{{ old('address', $order->address) }}</textarea>
                    </div>
                </div>

                {{-- Shipping / Details --}}
                <div class="oe-card">
                    <h2>Shipping & Details</h2>
                    <div class="oe-field">
                        <label>Division</label>
                        <input type="text" name="division" class="oe-input" value="{{ old('division', $order->division) }}">
                    </div>
                    <div class="oe-field">
                        <label>District</label>
                        <input type="text" name="district" class="oe-input" value="{{ old('district', $order->district) }}">
                    </div>
                    <div class="oe-field">
                        <label>Thana</label>
                        <input type="text" name="thana" class="oe-input" value="{{ old('thana', $order->thana) }}">
                    </div>
                    <div class="oe-field">
                        <label>Landmark</label>
                        <input type="text" name="landmark" class="oe-input" value="{{ old('landmark', $order->landmark) }}">
                    </div>
                    <div class="oe-field">
                        <label>Payment Method</label>
                        <select name="payment_method" class="oe-select">
                            @foreach (['cod' => 'COD', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'card' => 'Card', 'bank' => 'Bank'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $order->payment_method === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="oe-field">
                        <label>Payment Status</label>
                        <select name="payment_status" class="oe-select">
                            <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="oe-panel">
                <div class="oe-panel-inner" style="padding-bottom: 16px;">
                    <div class="oe-items-head">
                        <h2>Order items</h2>
                        <div style="position: relative; width: 320px;">
                            <input type="text" class="oe-search" id="productSearch" placeholder="Search product to add..." autocomplete="off">
                            <div class="oe-search-dropdown" id="searchDropdown"></div>
                        </div>
                    </div>
                </div>
                <table class="oe-items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="num">Qty</th>
                            <th class="num">Price</th>
                            <th class="num">Total</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach ($order->items as $item)
                            <tr data-item-id="{{ $item->id }}" data-product-id="{{ $item->product_id }}">
                                <td>
                                    <div class="oe-product-cell">
                                        <div class="oe-product-thumb">
                                            @if ($item->product && $item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($item->product->name ?? 'P', 0, 1)) }}
                                            @endif
                                        </div>
                                        <span>{{ $item->product->name ?? 'Deleted Product' }}</span>
                                    </div>
                                </td>
                                <td><input type="number" class="oe-cell-input qty-input" name="items[{{ $item->id }}][quantity]" value="{{ $item->quantity }}" min="0"></td>
                                <td><input type="number" step="0.01" class="oe-cell-input price-input" name="items[{{ $item->id }}][price]" value="{{ $item->price }}" min="0"></td>
                                <td class="oe-row-total mono">{{ $currencySymbol }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                                <td>
                                    <button type="button" class="oe-delete-btn" title="Remove">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals & Notes --}}
            <div class="oe-panel">
                <div class="oe-panel-inner">
                    <h2 style="margin-bottom: 20px;">Totals & notes</h2>
                    <div class="oe-totals-grid">
                        <div class="oe-totals-left">
                            <div class="trow">
                                <label>Delivery charge</label>
                                <input type="number" step="0.01" name="delivery_charge" id="shippingCharge" value="{{ $order->delivery_charge }}">
                            </div>
                            <div class="trow">
                                <label>Discount</label>
                                <input type="number" step="0.01" name="discount_amount" id="discount" value="{{ $order->discount_amount }}">
                            </div>
                            <div class="oe-summary">
                                <div class="oe-summary-line"><span>Subtotal</span><span class="val mono" id="subtotalVal">{{ $currencySymbol }}{{ number_format($order->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</span></div>
                                <div class="oe-summary-line"><span>Delivery</span><span class="val mono" id="deliveryVal">{{ $currencySymbol }}{{ number_format($order->delivery_charge, 2) }}</span></div>
                                <div class="oe-summary-line"><span>Discount</span><span class="val mono" id="discountVal">-{{ $currencySymbol }}{{ number_format($order->discount_amount, 2) }}</span></div>
                                <div class="oe-summary-line grand"><span>Total</span><span class="val mono" id="grandTotalVal">{{ $currencySymbol }}{{ number_format($order->total_price, 2) }}</span></div>
                            </div>
                        </div>
                        <div class="oe-notes-col">
                            <label>Customer note</label>
                            <textarea name="order_note" class="oe-input oe-textarea" style="min-height:100px" placeholder="Delivery instructions from customer...">{{ old('order_note', $order->order_note) }}</textarea>
                            <label style="margin-top:14px">Internal note</label>
                            <textarea name="internal_note" class="oe-input oe-textarea" style="min-height:100px" placeholder="Private admin notes...">{{ old('internal_note', $order->internal_note) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="oe-action-row">
                <button type="submit" class="oe-btn oe-btn-save">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save changes
                </button>
                <a href="{{ route('admin.orders.show', $order) }}" class="oe-btn oe-btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        const currencySymbol = @js($currencySymbol);
        const searchUrl = @js(route('admin.orders.search-product', $order));
        const itemsBody = document.getElementById('itemsBody');
        const shippingInput = document.getElementById('shippingCharge');
        const discountInput = document.getElementById('discount');
        const searchInput = document.getElementById('productSearch');
        const dropdown = document.getElementById('searchDropdown');
        let searchTimeout = null;
        let newItemCount = 0;

        function fmt(n) { return currencySymbol + parseFloat(n).toFixed(2); }

        function recalc() {
            let subtotal = 0;
            itemsBody.querySelectorAll('tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
                const lineTotal = qty * price;
                const totalCell = row.querySelector('.oe-row-total');
                if (totalCell) totalCell.textContent = fmt(lineTotal);
                subtotal += lineTotal;
            });
            const shipping = parseFloat(shippingInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const grand = subtotal + shipping - discount;

            document.getElementById('subtotalVal').textContent = fmt(subtotal);
            document.getElementById('deliveryVal').textContent = fmt(shipping);
            document.getElementById('discountVal').textContent = '-' + fmt(discount);
            document.getElementById('grandTotalVal').textContent = fmt(grand);
        }

        itemsBody.addEventListener('input', recalc);
        shippingInput.addEventListener('input', recalc);
        discountInput.addEventListener('input', recalc);

        itemsBody.addEventListener('click', e => {
            const btn = e.target.closest('.oe-delete-btn');
            if (!btn) return;
            const row = btn.closest('tr');
            const itemId = row.dataset.itemId;
            if (itemId) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'items[' + itemId + '][quantity]';
                input.value = '0';
                row.appendChild(input);
                row.style.opacity = '0.4';
                row.querySelectorAll('input').forEach(i => i.disabled = true);
                btn.disabled = true;
            } else {
                row.remove();
            }
            recalc();
        });

        // Product search
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) { dropdown.classList.remove('open'); return; }
            searchTimeout = setTimeout(() => {
                fetch(searchUrl + '?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(products => {
                        if (!products.length) { dropdown.classList.remove('open'); return; }
                        dropdown.innerHTML = products.map(p =>
                            '<div class="oe-search-item" data-id="' + p.id + '" data-name="' + (p.name||'').replace(/"/g, '&quot;') + '" data-price="' + p.price + '" data-image="' + (p.image || '') + '">' +
                                '<div class="oe-product-thumb" style="width:32px;height:32px;font-size:12px;">' +
                                    (p.image ? '<img src="/storage/' + p.image + '" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">' : p.name.charAt(0).toUpperCase()) +
                                '</div>' +
                                '<div><div class="si-name">' + p.name + '</div>' + (p.sku ? '<div class="si-sku">SKU: ' + p.sku + '</div>' : '') + '</div>' +
                                '<div class="si-price">' + fmt(p.price) + '</div>' +
                            '</div>'
                        ).join('');
                        dropdown.classList.add('open');
                    });
            }, 300);
        });

        dropdown.addEventListener('click', e => {
            const item = e.target.closest('.oe-search-item');
            if (!item) return;
            const pid = item.dataset.id;
            const name = item.dataset.name;
            const price = item.dataset.price;
            const image = item.dataset.image;
            const key = 'new_' + (++newItemCount);
            const row = document.createElement('tr');
            row.dataset.productId = pid;
            row.innerHTML =
                '<td><div class="oe-product-cell">' +
                    '<div class="oe-product-thumb">' +
                        (image ? '<img src="/storage/' + image + '" style="width:100%;height:100%;object-fit:cover;">' : name.charAt(0).toUpperCase()) +
                    '</div>' +
                    '<span>' + name + '</span>' +
                '</div></td>' +
                '<td><input type="number" class="oe-cell-input qty-input" name="items[' + key + '][quantity]" value="1" min="0"></td>' +
                '<td><input type="number" step="0.01" class="oe-cell-input price-input" name="items[' + key + '][price]" value="' + price + '" min="0"></td>' +
                '<td class="oe-row-total mono">' + fmt(price) + '</td>' +
                '<td><button type="button" class="oe-delete-btn" title="Remove"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></button></td>';
            const pidInput = document.createElement('input');
            pidInput.type = 'hidden';
            pidInput.name = 'items[' + key + '][product_id]';
            pidInput.value = pid;
            row.appendChild(pidInput);
            itemsBody.appendChild(row);
            recalc();
            dropdown.classList.remove('open');
            searchInput.value = '';
        });

        document.addEventListener('click', e => {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });

        recalc();
    </script>
</x-admin-layout>
