<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    :root{
        --bg:#EEF1F4;
        --card:#FFFFFF;
        --ink:#1C2024;
        --ink-soft:#7A828C;
        --border:#ECEEF1;
        --red:#A31E22;
        --red-dark:#5C0E12;
        --purple:#6C4EE8;
        --green:#12B76A;
        --green-tint:#E6F8EE;
        --amber:#F5A623;
        --amber-tint:#FDF1D9;
        --peach:#FBEFE3;
        --radius:16px;
        --shadow:0 1px 2px rgba(20,20,30,0.03), 0 10px 24px -14px rgba(20,20,30,0.10);
    }
    .d-shell{display:grid; grid-template-columns:236px 1fr; gap:20px; max-width:1240px; margin:0 auto; align-items:start;}
    .d-sidebar{background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:18px 14px; position:sticky; top:20px;}
    .d-sidebar-title{font-size:15px; font-weight:700; color:var(--red); padding:6px 10px 14px; font-family:'Inter',sans-serif;}
    .d-nav-group{display:flex; flex-direction:column; gap:3px; font-family:'Inter',sans-serif;}
    .d-nav-item{display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; font-size:13.5px; font-weight:500; color:#4A5058; cursor:pointer; position:relative; transition:background .15s ease, color .15s ease; text-decoration:none;}
    .d-nav-item:hover{background:#F5F1EF;}
    .d-nav-item.active{background:var(--red); color:#fff; font-weight:600; box-shadow:0 6px 14px -6px rgba(163,30,34,0.55);}
    .d-nav-item svg{width:16px; height:16px; flex-shrink:0;}
    .d-nav-badge{margin-left:8px; background:var(--red); color:#fff; font-size:10px; font-weight:700; min-width:16px; height:16px; border-radius:999px; display:flex; align-items:center; justify-content:center; padding:0 4px;}
    .d-nav-divider{height:1px; background:var(--border); margin:12px 4px;}
    .d-main{display:flex; flex-direction:column; gap:20px; font-family:'Inter',sans-serif;}
    .d-banner{background:linear-gradient(135deg, #9C1C20 0%, #6E1215 55%, #4C0D10 100%); border-radius:var(--radius); padding:26px 30px; color:#fff; position:relative; overflow:hidden;}
    .d-banner::after{content:''; position:absolute; right:-40px; top:-60px; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,0.05);}
    .d-banner h1{font-size:22px; font-weight:700; margin:0 0 6px; position:relative;}
    .d-banner p{font-size:13.5px; margin:0; color:rgba(255,255,255,0.85); position:relative;}
    .d-stats-row{display:grid; grid-template-columns:repeat(4,1fr); gap:16px;}
    .d-stat-card{background:var(--card); border-radius:14px; box-shadow:var(--shadow); padding:16px 18px; display:flex; align-items:center; justify-content:space-between;}
    .d-stat-label{font-size:12px; color:var(--ink-soft); margin-bottom:8px;}
    .d-stat-value{font-size:22px; font-weight:700;}
    .d-stat-icon{width:38px; height:38px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center;}
    .d-stat-icon.bag{background:var(--peach); color:var(--red);}
    .d-stat-icon.wallet{background:var(--peach); color:var(--purple);}
    .d-stat-icon.clock{background:var(--amber-tint); color:var(--amber);}
    .d-stat-icon.check{background:var(--green-tint); color:var(--green);}
    .d-panel{background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:20px 24px;}
    .d-panel-head{display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;}
    .d-panel-head h2{font-size:16.5px; font-weight:700; margin:0; font-family:'Inter',sans-serif;}
    .d-view-all{font-size:12.5px; color:var(--red); font-weight:600; text-decoration:none;}
    .d-view-all:hover{opacity:.8;}
    .d-order-list{display:flex; flex-direction:column;}
    .d-order-row{display:flex; align-items:center; gap:14px; padding:14px 4px; border-bottom:1px solid var(--border);}
    .d-order-row:last-child{border-bottom:none;}
    .d-order-check{width:26px; height:26px; border-radius:50%; background:var(--green-tint); color:var(--green); display:flex; align-items:center; justify-content:center; flex-shrink:0;}
    .d-order-info{flex:1; min-width:0;}
    .d-order-id{font-size:13.5px; font-weight:600;}
    .d-order-date{font-size:11.5px; color:var(--ink-soft); margin-top:2px;}
    .d-order-right{display:flex; align-items:center; gap:14px;}
    .d-status-pill{background:var(--green-tint); color:var(--green); font-size:11px; font-weight:600; padding:4px 12px; border-radius:999px;}
    .d-order-price{font-size:13.5px; font-weight:700; color:var(--purple); min-width:60px; text-align:right; font-family:'JetBrains Mono',monospace;}
    .d-eye-btn{color:#B8BEC5; display:flex;}
    .d-product-grid{display:grid; grid-template-columns:repeat(4,1fr); gap:16px;}
    .d-product-card{border:1px solid var(--border); border-radius:14px; overflow:hidden; display:flex; flex-direction:column;}
    .d-product-media{position:relative; height:150px; display:flex; align-items:center; justify-content:center;}
    .d-discount-badge{position:absolute; top:8px; left:8px; z-index:2; background:var(--red); color:#fff; font-size:10.5px; font-weight:700; padding:3px 8px; border-radius:5px;}
    .d-media-1{background:linear-gradient(135deg,#F6B45A,#E8801F);}
    .d-media-2{background:linear-gradient(160deg,#7A1620,#C21C2A);}
    .d-media-3{background:linear-gradient(160deg,#F4E9D8,#E7CB8F);}
    .d-media-4{background:linear-gradient(160deg,#3A2E12,#8A6A1E);}
    .d-bottle{width:34px; height:78px; border-radius:6px 6px 3px 3px; background:rgba(255,255,255,0.85); position:relative; box-shadow:0 8px 18px rgba(0,0,0,0.18);}
    .d-bottle::before{content:''; position:absolute; top:-14px; left:50%; transform:translateX(-50%); width:10px; height:16px; background:rgba(255,255,255,0.9); border-radius:2px;}
    .d-lamp-icon{color:#fff; opacity:0.9;}
    .d-product-body{padding:12px 13px 14px; display:flex; flex-direction:column; gap:8px; font-family:'Inter',sans-serif;}
    .d-product-name{font-size:12.5px; font-weight:600; line-height:1.35; min-height:34px;}
    .d-rating{display:flex; align-items:center; gap:5px;}
    .d-stars{color:#E3E5E8; display:flex; gap:1px;}
    .d-rating-count{font-size:11px; color:var(--ink-soft);}
    .d-price-row{display:flex; align-items:baseline; gap:7px;}
    .d-price-now{font-size:14.5px; font-weight:700; font-family:'JetBrains Mono',monospace;}
    .d-price-old{font-size:11.5px; color:#B8BEC5; text-decoration:line-through; font-family:'JetBrains Mono',monospace;}
    .d-btn-row{display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:2px;}
    .d-btn-cart, .d-btn-buy{border:none; border-radius:8px; padding:8px 0; display:flex; align-items:center; justify-content:center; gap:5px; font-size:12px; font-weight:600; cursor:pointer; color:#fff; font-family:'Inter',sans-serif;}
    .d-btn-cart{background:#191D22;}
    .d-btn-buy{background:#0F9D4F;}
    .d-btn-cart:hover{background:#000;}
    .d-btn-buy:hover{background:#0C7F40;}
    @media (max-width:980px){
        .d-shell{grid-template-columns:1fr;}
        .d-sidebar{position:static;}
        .d-stats-row{grid-template-columns:repeat(2,1fr);}
        .d-product-grid{grid-template-columns:repeat(2,1fr);}
    }
</style>

<x-app-layout>
    <div style="background:var(--bg); margin:-2rem -1.5rem; padding:20px;">
        <div class="d-shell">
            {{-- SIDEBAR --}}
            <aside class="d-sidebar">
                <div class="d-sidebar-title">Dashboard</div>
                <nav class="d-nav-group">
                    <a href="{{ route('dashboard') }}" class="d-nav-item active">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                        Home
                    </a>
                    <a href="{{ route('profile.edit') }}" class="d-nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/></svg>
                        Profile
                    </a>
                    <a href="{{ route('track-order') }}" class="d-nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 7h12l1 13H5L6 7z"/><path d="M9 7V5a3 3 0 016 0v2"/></svg>
                        Orders
                        <span class="d-nav-badge">1</span>
                    </a>
                    <a href="#" class="d-nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Address
                    </a>
                    <a href="#" class="d-nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
                        Notifications
                    </a>
                    <a href="#" class="d-nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12V8a2 2 0 00-2-2H6a2 2 0 00-2 2v4"/><path d="M2 12h20v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6z"/><path d="M12 12v4"/></svg>
                        Vouchers
                    </a>
                    <div class="d-nav-divider"></div>
                    @if (Auth::user()->is_admin ?? false)
                        <a href="{{ route('admin.dashboard') }}" class="d-nav-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/></svg>
                            Admin Panel
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="d-nav-item" style="width:100%; border:none; background:none; cursor:pointer;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                            Logout
                        </button>
                    </form>
                </nav>
            </aside>

            {{-- MAIN --}}
            <div class="d-main">
                <div class="d-banner">
                    <h1>Welcome back, {{ $user->name }}!</h1>
                    <p>View your orders and account overview</p>
                </div>

                <div class="d-stats-row">
                    <div class="d-stat-card">
                        <div><div class="d-stat-label">Total Orders</div><div class="d-stat-value" style="color:var(--ink);">{{ $stats['total_orders'] }}</div></div>
                        <div class="d-stat-icon bag">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 7h12l1 13H5L6 7z"/><path d="M9 7V5a3 3 0 016 0v2"/></svg>
                        </div>
                    </div>
                    <div class="d-stat-card">
                        <div><div class="d-stat-label">Total Spent</div><div class="d-stat-value" style="color:var(--purple); font-family:'JetBrains Mono',monospace;">{{ $currencySymbol }}{{ number_format($stats['total_spent'], 0) }}</div></div>
                        <div class="d-stat-icon wallet">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M2 10h20"/><circle cx="17" cy="15" r="1.3"/></svg>
                        </div>
                    </div>
                    <div class="d-stat-card">
                        <div><div class="d-stat-label">Pending</div><div class="d-stat-value" style="color:var(--amber);">{{ $stats['pending'] + $stats['processing'] }}</div></div>
                        <div class="d-stat-icon clock">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        </div>
                    </div>
                    <div class="d-stat-card">
                        <div><div class="d-stat-label">Delivered</div><div class="d-stat-value" style="color:var(--green);">{{ $stats['delivered'] }}</div></div>
                        <div class="d-stat-icon check">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6L9 17l-5-5"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Recent Orders --}}
                <div class="d-panel">
                    <div class="d-panel-head">
                        <h2>Recent Orders</h2>
                        @if ($stats['total_orders'] > 0)
                            <a class="d-view-all" href="{{ route('track-order') }}">View All</a>
                        @endif
                    </div>
                    @if ($recentOrders->isEmpty())
                        <div style="padding:48px 0; text-align:center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" style="margin:0 auto 12px; display:block;"><path d="M6 7h12l1 13H5L6 7z"/><path d="M9 7V5a3 3 0 016 0v2"/></svg>
                            <p style="font-size:13.5px; color:var(--ink-soft); margin:0 0 12px;">You haven't placed any orders yet.</p>
                            <a href="{{ route('products.index') }}" style="font-size:13.5px; font-weight:600; color:var(--red); text-decoration:none;">Start shopping</a>
                        </div>
                    @else
                        @php
                            $statusStyles = [
                                'pending'    => ['pill_bg' => '#FDF1D9', 'pill_text' => '#B45309'],
                                'processing' => ['pill_bg' => '#EEF2FF', 'pill_text' => '#4338CA'],
                                'shipped'    => ['pill_bg' => '#F0F4FF', 'pill_text' => '#1D4ED8'],
                                'delivered'  => ['pill_bg' => '#E6F8EE', 'pill_text' => '#047857'],
                                'cancelled'  => ['pill_bg' => '#FEE2E2', 'pill_text' => '#B91C1C'],
                            ];
                        @endphp
                        <div class="d-order-list">
                            @foreach ($recentOrders as $order)
                                @php
                                    $s = $statusStyles[$order->status] ?? ['pill_bg' => '#E6F8EE', 'pill_text' => '#047857'];
                                @endphp
                                <a href="{{ route('orders.show', $order) }}" class="d-order-row" style="text-decoration:none; color:inherit;">
                                    <div class="d-order-check">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5"/></svg>
                                    </div>
                                    <div class="d-order-info">
                                        <div class="d-order-id">Order #{{ $order->order_number }}</div>
                                        <div class="d-order-date">{{ $order->created_at->format('M d, Y') }}</div>
                                    </div>
                                    <div class="d-order-right">
                                        <span class="d-status-pill" style="background:{{ $s['pill_bg'] }}; color:{{ $s['pill_text'] }};">{{ $order->status }}</span>
                                        <span class="d-order-price">{{ $currencySymbol }}{{ number_format($order->total_price, 0) }}</span>
                                        <span class="d-eye-btn">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Products You May Like --}}
                <div class="d-panel">
                    <div class="d-panel-head">
                        <h2>Products You May Like</h2>
                        <a class="d-view-all" href="{{ route('products.index') }}">View All</a>
                    </div>
                    <div class="d-product-grid" id="d-product-grid"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function(){
        var cs = @json($currencySymbol);
        var products = @json($products ?? []);
        var grid = document.getElementById('d-product-grid');
        if(!grid || !products.length) return;

        var starSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        var cartIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.6a2 2 0 002-1.6L23 6H6"/></svg>';
        var boltIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>';
        var lampMedia = '<svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6" class="d-lamp-icon"><path d="M9 18h6"/><path d="M12 18v-5"/><path d="M6 8l6-5 6 5-2 5H8L6 8z"/></svg>';
        var medias = ['d-media-1','d-media-2','d-media-3','d-media-4'];

        grid.innerHTML = products.map(function(p, i){
            var discount = p.original_price ? Math.round((1 - p.price / p.original_price) * 100) : 0;
            return '<div class="d-product-card">' +
                '<div class="d-product-media ' + medias[i % 4] + '">' +
                    (discount > 0 ? '<span class="d-discount-badge">-' + discount + '%</span>' : '') +
                    (i === 0 ? lampMedia : '<div class="d-bottle"></div>') +
                '</div>' +
                '<div class="d-product-body">' +
                    '<div class="d-product-name">' + p.name + '</div>' +
                    '<div class="d-rating"><div class="d-stars">' + starSvg.repeat(5) + '</div><span class="d-rating-count">(0)</span></div>' +
                    '<div class="d-price-row">' +
                        '<span class="d-price-now">' + cs + Number(p.price).toLocaleString() + '</span>' +
                        (p.original_price ? '<span class="d-price-old">' + cs + Number(p.original_price).toLocaleString() + '</span>' : '') +
                    '</div>' +
                    '<div class="d-btn-row">' +
                        '<button class="d-btn-cart">' + cartIcon + '</button>' +
                        '<button class="d-btn-buy">' + boltIcon + ' Buy</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');
    })();
    </script>
    @endpush
</x-app-layout>
