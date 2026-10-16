<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }} — {{ $storeName }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Helvetica,Arial,sans-serif;color:#1a1a1a;font-size:13px;line-height:1.5}
        .invoice{max-width:700px;margin:0 auto;padding:40px}
        .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:32px;border-bottom:2px solid #6366f1;padding-bottom:24px}
        .brand h1{font-size:22px;font-weight:700;color:#6366f1}
        .brand p{font-size:12px;color:#666;margin-top:2px}
        .invoice-meta{text-align:right}
        .invoice-meta h2{font-size:18px;color:#1a1a1b}
        .invoice-meta p{font-size:12px;color:#666}
        .parties{display:flex;justify-content:space-between;margin-bottom:32px;gap:24px}
        .party{flex:1}
        .party h3{font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:#999;margin-bottom:6px}
        .party p{font-size:13px;color:#333}
        table{width:100%;border-collapse:collapse;margin-bottom:24px}
        thead{background:#f8f9fa}
        th{padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;color:#666;border-bottom:2px solid #e5e7eb}
        td{padding:10px 12px;border-bottom:1px solid #f0f0f0;font-size:13px}
        .text-right{text-align:right}
        .text-center{text-align:center}
        .totals{margin-left:auto;width:260px}
        .totals .row{display:flex;justify-content:space-between;padding:6px 0;font-size:13px}
        .totals .row.total{border-top:2px solid #1a1a1a;margin-top:6px;padding-top:10px;font-size:16px;font-weight:700}
        .footer{margin-top:40px;padding-top:20px;border-top:1px solid #e5e7eb;text-align:center;font-size:11px;color:#999}
    </style>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <div class="brand">
                <h1>{{ $storeName }}</h1>
                @if ($storeSettings['store_email'] ?? null)
                    <p>{{ $storeSettings['store_email'] }}</p>
                @endif
                @if ($storeSettings['store_phone'] ?? null)
                    <p>{{ $storeSettings['store_phone'] }}</p>
                @endif
            </div>
            <div class="invoice-meta">
                <h2>INVOICE</h2>
                <p><strong>#{{ $order->order_number }}</strong></p>
                <p>{{ $order->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="parties">
            <div class="party">
                <h3>Bill To</h3>
                <p>
                    {{ $order->first_name }} {{ $order->last_name }}<br>
                    @if ($order->email)
                        {{ $order->email }}<br>
                    @endif
                    {{ $order->phone }}<br>
                    {{ $order->address }}
                </p>
            </div>
            <div class="party" style="text-align:right">
                <h3>Order Details</h3>
                <p>
                    Status: <strong>{{ ucfirst($order->status) }}</strong><br>
                    Payment: <strong>{{ ucfirst($order->payment_method) }}</strong><br>
                    Delivery: <strong>{{ ucfirst(str_replace('_', ' ', $order->delivery_option)) }}</strong>
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ $currencySymbol }}{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ $currencySymbol }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="row">
                <span>Subtotal</span>
                <span>{{ $currencySymbol }}{{ number_format($order->total_price - $order->delivery_charge + $order->discount_amount, 2) }}</span>
            </div>
            <div class="row">
                <span>Delivery</span>
                <span>{{ $currencySymbol }}{{ number_format($order->delivery_charge, 2) }}</span>
            </div>
            @if ($order->discount_amount > 0)
                <div class="row" style="color:#16a34a">
                    <span>Discount</span>
                    <span>-{{ $currencySymbol }}{{ number_format($order->discount_amount, 2) }}</span>
                </div>
            @endif
            <div class="row total">
                <span>Total</span>
                <span>{{ $currencySymbol }}{{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your order! &mdash; {{ $storeName }}</p>
        </div>
    </div>
</body>
</html>
