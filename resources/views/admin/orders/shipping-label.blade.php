<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shipping Label — {{ $order->order_number }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Helvetica,Arial,sans-serif;color:#1a1a1a;font-size:12px;line-height:1.4}
        .label{width:4in;margin:0 auto;padding:16px;border:2px solid #000}
        .top-row{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;padding-bottom:10px;border-bottom:1px dashed #999}
        .store-name{font-size:16px;font-weight:700}
        .order-num{font-size:11px;color:#555;margin-top:2px}
        .tracking-box{background:#f5f5f5;border:1px solid #ccc;padding:8px 10px;border-radius:4px;text-align:center;font-size:11px}
        .tracking-box .id{font-size:14px;font-weight:700;font-family:'Courier New',monospace;letter-spacing:0.5px}
        .section{margin-bottom:12px}
        .section-title{font-size:9px;text-transform:uppercase;letter-spacing:0.1em;color:#888;margin-bottom:4px;font-weight:600}
        .recipient-name{font-size:15px;font-weight:700;margin-bottom:3px}
        .address-line{font-size:12px;color:#333}
        .phone-line{font-size:12px;margin-top:4px;font-weight:600}
        .items-section{margin-top:12px;padding-top:10px;border-top:1px dashed #999}
        .items-section table{width:100%;border-collapse:collapse}
        .items-section th{text-align:left;font-size:9px;text-transform:uppercase;letter-spacing:0.08em;color:#888;padding:3px 0;border-bottom:1px solid #ddd}
        .items-section td{padding:4px 0;font-size:11px;border-bottom:1px solid #eee}
        .text-right{text-align:right}
        .cod-row{margin-top:10px;padding:8px 10px;background:#fff3cd;border:1px solid #ffc107;border-radius:4px;display:flex;justify-content:space-between;align-items:center}
        .cod-label{font-size:10px;text-transform:uppercase;letter-spacing:0.08em;color:#856404;font-weight:600}
        .cod-amount{font-size:18px;font-weight:700;color:#856404}
        .footer{margin-top:12px;padding-top:8px;border-top:1px dashed #999;text-align:center;font-size:9px;color:#999}
    </style>
</head>
<body>
    <div class="label">
        <div class="top-row">
            <div>
                <div class="store-name">{{ $storeName }}</div>
                <div class="order-num">#{{ $order->order_number }}</div>
            </div>
            @if ($order->consignment_id)
                <div class="tracking-box">
                    <div style="font-size:9px;text-transform:uppercase;letter-spacing:0.1em;color:#888;margin-bottom:3px;font-weight:600;">Consignment ID</div>
                    @if ($barcodeHtml)
                        <div style="margin-bottom:3px;">{!! $barcodeHtml !!}</div>
                    @endif
                    <div class="id">{{ $order->consignment_id }}</div>
                </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Ship To</div>
            <div class="recipient-name">{{ $order->first_name }} {{ $order->last_name ?? '' }}</div>
            <div class="phone-line">{{ $order->phone }}</div>
        </div>

        <div class="section">
            <div class="section-title">Address</div>
            @if ($order->division)
                <div class="address-line">{{ $order->division }}</div>
            @endif
            @if ($order->district)
                <div class="address-line">{{ $order->district }}</div>
            @endif
            @if ($order->thana)
                <div class="address-line">{{ $order->thana }}</div>
            @endif
            @if ($order->address)
                <div class="address-line">{{ $order->address }}</div>
            @endif
            @if ($order->landmark)
                <div class="address-line">Landmark: {{ $order->landmark }}</div>
            @endif
        </div>

        @if ($order->items->count())
            <div class="items-section">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-right">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Product' }}</td>
                                <td class="text-right">{{ $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="cod-row">
            <div class="cod-label">Cash on Delivery</div>
            <div class="cod-amount">Tk {{ number_format($order->total_price, 2) }}</div>
        </div>

        <div class="footer">
            @if ($courierLabel)
                Courier: {{ $courierLabel }}
                @if ($order->courier_pushed_at)
                    &middot; Pushed: {{ $order->courier_pushed_at->format('M j, Y g:i A') }}
                @endif
            @endif
        </div>
    </div>
</body>
</html>
