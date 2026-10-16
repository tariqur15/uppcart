@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#EEF1F4;
    --card:#FFFFFF;
    --ink:#1C2024;
    --ink-soft:#7A828C;
    --border:#ECEEF1;
    --red:#A31E22;
    --red-dark:#5C0E12;
    --red-tint:#FBEAEA;
    --purple:#6C4EE8;
    --purple-tint:#F1EEFE;
    --green:#12B76A;
    --green-tint:#E6F8EE;
    --amber:#F5A623;
    --amber-tint:#FDF1D9;
    --blue:#3B82F6;
    --blue-tint:#EAF2FE;
    --teal:#0891B2;
    --teal-tint:#E1F4F8;
    --slate:#64748B;
    --slate-tint:#EEF0F3;
    --gray-tint:#F1F2F4;
    --radius:16px;
    --shadow:0 1px 2px rgba(20,20,30,0.03), 0 10px 24px -14px rgba(20,20,30,0.10);
  }
  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;}
  body, .main, .main *{
    font-family:'Inter',sans-serif !important;
    -webkit-font-smoothing:antialiased;
  }
  .mono{font-family:'JetBrains Mono',monospace !important;}

  .main{display:flex; flex-direction:column; gap:18px;}

  .topbar{display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;}
  .topbar h1{font-size:21px; font-weight:800; margin:0 0 4px;}
  .topbar .sub{font-size:13px; color:var(--ink-soft);}

  .view-tabs{display:flex; gap:6px; background:var(--card); border:1px solid var(--border); padding:5px; border-radius:12px; width:fit-content; box-shadow:var(--shadow);}
  .view-tabs a{
    font-size:13px; font-weight:600; padding:8px 18px; border-radius:9px; color:var(--ink-soft);
    text-decoration:none; transition:background .15s ease, color .15s ease;
  }
  .view-tabs a:hover{background:var(--gray-tint); color:var(--ink);}
  .view-tabs a.active{background:var(--red); color:#fff;}

  .stats-row{display:grid; grid-template-columns:repeat(auto-fit, minmax(148px,1fr)); gap:14px;}
  .stat-card{
    background:var(--card); border-radius:14px; box-shadow:var(--shadow);
    padding:15px 18px; display:flex; align-items:center; justify-content:space-between;
    cursor:pointer; border:1.5px solid transparent; transition:border-color .15s ease;
    text-decoration:none; color:var(--ink); user-select:none;
  }
  .stat-card:hover{border-color:var(--border);}
  .stat-card.active{border-color:var(--red); background:rgba(163,30,34,0.05);}
  .stat-label{font-size:11.5px; color:var(--ink-soft); margin-bottom:7px;}
  .stat-value{font-size:20px; font-weight:800;}
  .stat-revenue{font-size:12px; font-weight:600; color:var(--green); margin-top:4px;}
  .stat-icon{width:34px; height:34px; border-radius:9px; flex-shrink:0; display:flex; align-items:center; justify-content:center;}
  .stat-icon svg{width:16px; height:16px;}
  .stat-icon.all{background:var(--purple-tint); color:var(--purple);}
  .stat-icon.pending{background:var(--amber-tint); color:var(--amber);}
  .stat-icon.processing{background:var(--blue-tint); color:var(--blue);}
  .stat-icon.shipped{background:var(--teal-tint); color:var(--teal);}
  .stat-icon.delivered{background:var(--green-tint); color:var(--green);}
  .stat-icon.returned{background:var(--slate-tint); color:var(--slate);}
  .stat-icon.cancelled{background:var(--red-tint); color:var(--red);}
  .stat-icon.abandoned{background:var(--amber-tint); color:var(--amber);}

  .panel{background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow);}
  table.orders tbody td{position:relative;}
  .filter-bar{display:flex; align-items:center; gap:10px; flex-wrap:wrap; padding:16px 20px; border-bottom:1px solid var(--border);}
  .search-wrap{position:relative; flex:1; min-width:220px;}
  .search-wrap svg.icon-search{position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--ink-soft);}
  .search-clear{
    position:absolute; right:8px; top:50%; transform:translateY(-50%);
    width:22px; height:22px; border-radius:6px; border:none; background:var(--gray-tint);
    display:none; align-items:center; justify-content:center; cursor:pointer; color:var(--ink-soft);
    transition:background .15s, color .15s;
  }
  .search-clear:hover{background:var(--border); color:var(--ink);}
  .search-wrap.has-value .search-clear{display:flex;}
  .search-wrap input{
    width:100%; font-size:13.5px; padding:10px 12px 10px 36px;
    border-radius:9px; border:1.5px solid var(--border); background:#FBFAF9; color:var(--ink); outline:none;
  }
  .search-wrap input:focus{border-color:var(--red); background:#fff;}
  select.filter-select{
    font-size:13px; padding:9px 12px; border-radius:9px; border:1.5px solid var(--border);
    background:#fff; color:var(--ink); outline:none; font-family:'Inter',sans-serif;
  }
  select.filter-select:focus{border-color:var(--red);}
  .date-input{
    font-size:13px; padding:9px 12px; border-radius:9px; border:1.5px solid var(--border);
    background:#fff; color:var(--ink); outline:none; font-family:'Inter',sans-serif;
  }
  .date-input:focus{border-color:var(--red);}
  .date-range{display:flex; align-items:center; gap:6px;}
  .date-range span{font-size:12px; color:var(--ink-soft);}
  .filter-btn{
    font-size:13px; font-weight:600; padding:9px 16px; border-radius:9px; border:1.5px solid var(--border);
    background:#fff; color:var(--ink); cursor:pointer; font-family:'Inter',sans-serif;
    display:inline-flex; align-items:center; gap:6px; transition:background .15s, border-color .15s;
  }
  .filter-btn:hover{background:var(--gray-tint);}
  .filter-btn.primary{background:var(--red); color:#fff; border-color:var(--red);}
  .filter-btn.primary:hover{background:#8B1A1E;}

  .items-tooltip-wrap{position:relative; display:inline-block;}
  .items-tooltip{
    display:none; position:absolute; bottom:calc(100% + 8px); left:0; z-index:40;
    background:#fff; border:1px solid var(--border); border-radius:12px;
    box-shadow:0 8px 24px rgba(0,0,0,0.12); padding:10px; min-width:260px; max-width:340px;
    font-family:'Inter',sans-serif; pointer-events:none;
  }
  .items-tooltip-wrap:hover .items-tooltip{display:block;}
  .items-tooltip::after{
    content:''; position:absolute; top:100%; left:24px;
    border:6px solid transparent; border-top-color:#fff;
  }
  .items-tooltip::before{
    content:''; position:absolute; top:calc(100% - 1px); left:23px;
    border:7px solid transparent; border-top-color:var(--border);
  }
  .it-row{display:flex; align-items:center; gap:10px; padding:6px 0; border-bottom:1px solid var(--border);}
  .it-row:last-child{border-bottom:none;}
  .it-thumb{width:36px; height:36px; border-radius:7px; flex-shrink:0; overflow:hidden; background:linear-gradient(135deg,var(--purple-tint),#fff); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:var(--purple);}
  .it-thumb img{width:100%; height:100%; object-fit:cover;}
  .it-info{flex:1; min-width:0;}
  .it-name{font-size:12px; font-weight:600; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
  .it-calc{font-size:11px; color:var(--ink-soft); margin-top:2px; font-family:'JetBrains Mono',monospace;}
  .it-calc span{color:var(--ink); font-weight:600;}

  table.orders{width:100%; border-collapse:collapse;}
  table.orders thead th{
    text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.04em;
    color:var(--ink-soft); font-weight:700; padding:12px 20px; background:#FAFAF9;
    border-bottom:1px solid var(--border); white-space:nowrap;
  }
  table.orders thead th.num{text-align:right;}
  table.orders tbody td{padding:14px 20px; border-bottom:1px solid var(--border); font-size:13.5px; vertical-align:middle;}
  table.orders tbody tr:last-child td{border-bottom:none;}
  table.orders tbody tr{transition:background .12s ease;}
  table.orders tbody tr:hover{background:#FAF8F7;}
  table.orders tbody tr.abandoned-row-ignored{background:#F4F4F2;}
  table.orders tbody tr.abandoned-row-ignored:hover{background:#EFEFEC;}
  .icon-btn.ignored{color:var(--red); border-color:var(--red); background:var(--red-tint);}
  td.num{text-align:right;}

  .order-id-cell .order-id{font-weight:700; font-size:13px;}
  .order-id-cell .order-date{font-size:11px; color:var(--ink-soft); margin-top:2px;}

  .cust-cell{display:flex; align-items:center; gap:10px;}
  .cust-avatar{
    width:32px; height:32px; border-radius:50%; background:var(--red); color:#fff;
    display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12.5px; flex-shrink:0;
  }
  .cust-name{font-weight:600; font-size:13px;}
  .cust-seg{font-size:10.5px; font-weight:700; letter-spacing:0.02em; vertical-align:middle; white-space:nowrap;}
  .cust-seg.new{color:var(--green);}
  .cust-seg.repeated{color:var(--purple);}
  .cust-phone{font-size:11.5px; color:var(--ink-soft); margin-top:1px;}

  .date-cell .date-main{font-size:13px;}
  .date-cell .date-sub{font-size:11px; color:var(--ink-soft); margin-top:1px;}

  .pay-chip{display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600;}
  .pay-dot{width:7px; height:7px; border-radius:50%; flex-shrink:0;}
  .pay-dot.bkash{background:#E2136E;}
  .pay-dot.nagad{background:#F6921E;}
  .pay-dot.cod{background:var(--green);}

  .courier-pill{font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; display:inline-block;}
  .courier-pill.pathao{background:#FBEAEA; color:#ED1C24;}
  .courier-pill.redx{background:var(--purple-tint); color:var(--purple);}
  .courier-pill.steadfast{background:#E1F4F8; color:#32A386;}
  .courier-pill.none{background:var(--gray-tint); color:var(--ink-soft);}

  .fraud-pct{font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; display:inline-block;}
  .fraud-pct.high{background:var(--green-tint); color:var(--green);}
  .fraud-pct.medium{background:var(--amber-tint); color:var(--amber);}
  .fraud-pct.low{background:var(--red-tint); color:var(--red);}
  .fraud-pct.loading{background:var(--gray-tint); color:var(--ink-soft);}

  .fraud-wrap{position:relative; display:inline-block;}
  .fraud-tooltip{
    display:none; position:absolute; left:50%; bottom:calc(100% + 8px); transform:translateX(-50%);
    background:#fff; border:1px solid var(--border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.12);
    padding:12px 14px; z-index:50; min-width:260px; white-space:nowrap;
  }
  .fraud-wrap:hover .fraud-tooltip{display:block;}
  .fraud-tooltip-row{display:flex; align-items:center; gap:8px; padding:4px 0; font-size:11.5px;}
  .fraud-tooltip-header{display:flex; align-items:center; gap:8px; padding:6px 0; font-size:11.5px; border-bottom:1px solid var(--border); margin-bottom:2px;}
  .fraud-tooltip-row + .fraud-tooltip-row{border-top:1px solid var(--border);}
  .fraud-tooltip-name{font-weight:700; color:var(--ink); min-width:70px;}
  .fraud-tooltip-detail{color:var(--ink-soft);}
  .fraud-tooltip-delivered{color:var(--green); font-weight:600;}
  .fraud-tooltip-cancel{color:var(--red); font-weight:600;}
  .fraud-tooltip-ratio{margin-left:auto; font-weight:800; padding:2px 8px; border-radius:999px; font-size:11px;}
  .fraud-tooltip-ratio.high{background:var(--green-tint); color:var(--green);}
  .fraud-tooltip-ratio.medium{background:var(--amber-tint); color:var(--amber);}
  .fraud-tooltip-ratio.low{background:var(--red-tint); color:var(--red);}
  .pay-dot.card{background:#2A2E43;}

  .status-pill{font-size:11px; font-weight:700; padding:5px 11px; border-radius:999px; text-transform:capitalize; display:inline-block;}
  .status-pill.delivered{background:var(--green-tint); color:var(--green);}
  .status-pill.completed{background:var(--green-tint); color:var(--green);}
  .status-pill.pending{background:var(--amber-tint); color:var(--amber);}
  .status-pill.confirmed{background:var(--blue-tint); color:var(--blue);}
  .status-pill.processing{background:var(--blue-tint); color:var(--blue);}
  .status-pill.shipped{background:var(--teal-tint); color:var(--teal);}
  .status-pill.returned{background:var(--slate-tint); color:var(--slate);}
  .status-pill.cancelled{background:var(--red-tint); color:var(--red);}

  .row-total{font-weight:700; color:var(--ink);}

  .action-cell{display:flex; gap:6px; justify-content:flex-end;}
  .icon-btn{
    width:30px; height:30px; border-radius:8px; border:1.5px solid var(--border); background:#fff;
    display:flex; align-items:center; justify-content:center; color:var(--ink-soft); cursor:pointer;
    transition:background .15s ease, color .15s ease; text-decoration:none;
  }
  .icon-btn:hover{background:var(--gray-tint); color:var(--ink);}
  .icon-btn.edit:hover{color:var(--blue); border-color:var(--blue);}
  .icon-btn.delete:hover{color:var(--red); border-color:var(--red);}
  .icon-btn.whatsapp{color:#25D366; border-color:#25D366; background:#f0fbf4;}
  .icon-btn.whatsapp:hover{background:#25D366; color:#fff; border-color:#25D366;}
  .icon-btn.view:hover{color:var(--teal); border-color:var(--teal);}

  .wa-menu{
    position:fixed; z-index:100; min-width:190px; background:#fff; border:1px solid var(--border);
    border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.14); padding:5px;
  }
  .wa-menu-item{
    display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:7px;
    font-size:12.5px; font-weight:600; color:var(--ink); text-decoration:none; cursor:pointer;
  }
  .wa-menu-item:hover{background:var(--gray-tint); color:var(--ink);}
  .wa-menu-icon{color:#25D366; flex-shrink:0;}

  [x-cloak]{display:none!important;}
  .cart-modal-overlay{
    position:fixed; inset:0; z-index:1000; background:rgba(15,15,15,0.45);
    display:flex; align-items:center; justify-content:center; padding:20px;
  }
  .cart-modal{
    background:#fff; border-radius:14px; width:100%; max-width:460px; max-height:82vh;
    display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.25);
  }
  .cart-modal-head{display:flex; align-items:center; justify-content:space-between; gap:10px; padding:16px 18px; border-bottom:1px solid var(--border);}
  .cart-modal-title{font-size:15px; font-weight:800; color:var(--ink);}
  .cart-modal-sub{font-size:12px; color:var(--ink-soft); margin-top:2px;}
  .cart-modal-items{padding:6px 18px; overflow-y:auto; flex:1;}
  .cart-modal-items .it-thumb{width:44px; height:44px; font-size:14px;}
  .cart-modal-items .it-name{font-size:13px;}
  .cart-modal-items .it-calc{font-size:11.5px; margin-top:2px;}
  .cart-modal-address{padding:12px 18px; border-bottom:1px solid var(--border); display:flex; gap:10px; align-items:flex-start;}
  .cart-modal-address svg{flex-shrink:0; margin-top:1px; color:var(--teal);}
  .cart-modal-address .addr-label{font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:var(--ink-soft); margin-bottom:3px;}
  .cart-modal-address .addr-text{font-size:13px; color:var(--ink); line-height:1.5;}
  .it-qty{font-size:12.5px; color:var(--ink-soft); font-weight:600; flex-shrink:0; font-family:'JetBrains Mono',monospace;}
  .it-amt{font-size:12.5px; font-weight:700; color:var(--ink); flex-shrink:0;}
  .cart-modal-foot{border-top:1px solid var(--border); padding:14px 18px;}
  .cart-modal-total{display:flex; align-items:center; justify-content:space-between; font-size:13px; font-weight:600; color:var(--ink-soft);}
  .cart-modal-total .mono{font-size:16px; font-weight:800; color:var(--ink);}
  .cart-modal-empty{padding:32px 18px; text-align:center; color:var(--ink-soft); font-size:13px;}

  .table-foot{display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:14px 20px;}
  .table-foot .count{font-size:12.5px; color:var(--ink-soft);}
  .pagination{display:flex; gap:6px;}
  .page-btn{
    width:30px; height:30px; border-radius:8px; border:1.5px solid var(--border); background:#fff;
    font-size:12.5px; font-weight:600; color:var(--ink-soft); cursor:pointer;
    display:flex; align-items:center; justify-content:center; text-decoration:none;
  }
  .page-btn.active{background:var(--red); border-color:var(--red); color:#fff;}
  .page-btn:hover:not(.active){background:var(--gray-tint);}

  .empty-state{
    display:none; flex-direction:column; align-items:center; justify-content:center; text-align:center;
    padding:60px 20px; color:var(--ink-soft);
  }
  .empty-state svg{color:#D8DCE1; margin-bottom:12px;}
  .empty-state p{font-size:13.5px; margin:0;}

  .bulk-bar{
    padding:12px 20px; border-bottom:1px solid var(--border);
    background:rgba(108,78,232,0.04); display:none; align-items:center; gap:12px; flex-wrap:wrap;
  }
  .bulk-bar.show{display:flex;}
  .bulk-text{font-size:13px; font-weight:600; color:var(--purple);}
  .bulk-select{
    font-size:12px; padding:7px 10px; border-radius:7px; border:1.5px solid var(--border);
    background:#fff; color:var(--ink); font-family:'Inter',sans-serif; outline:none;
  }
  .bulk-btn{
    padding:7px 14px; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer;
    font-family:'Inter',sans-serif; border:none; transition:opacity .15s;
  }
  .bulk-btn.primary{background:var(--purple); color:#fff;}
  .bulk-btn.primary:hover{opacity:0.9;}
  .bulk-btn.secondary{background:#fff; border:1.5px solid var(--border); color:var(--ink);}
  .bulk-btn.secondary:hover{background:var(--gray-tint);}

  .cb{width:16px; height:16px; border-radius:4px; border:1.5px solid var(--border); accent-color:var(--purple); cursor:pointer;}
  .row-selected{background:rgba(108,78,232,0.04) !important;}

  .loading-overlay{position:relative;}
  .loading-overlay::after{
    content:''; position:absolute; inset:0; background:rgba(255,255,255,0.6);
    display:none; align-items:center; justify-content:center; border-radius:var(--radius);
    z-index:10;
  }
  .loading-overlay.loading::after{display:flex;}

  @media (max-width:1050px){
    .stats-row{grid-template-columns:repeat(2,1fr);}
  }
  @media (max-width:700px){
    table.orders thead{display:none;}
    table.orders, table.orders tbody, table.orders tr, table.orders td{display:block; width:100%;}
    table.orders tbody tr{padding:12px 16px; border-bottom:1px solid var(--border);}
    table.orders td{padding:5px 0; border:none;}
    td.num{text-align:left;}
    .action-cell{justify-content:flex-start; margin-top:6px;}
  }
</style>
@endpush

<x-admin-layout>
    <div class="main" x-data="orderIndex()">

        {{-- Topbar --}}
        <div class="topbar">
            <div>
                <h1>Orders</h1>
                <div class="sub">Manage and track every order placed on your store</div>
            </div>
        </div>

        {{-- View Tabs --}}
        <div class="view-tabs">
            <a href="{{ route('admin.orders.index', ['view' => 'all']) }}" class="{{ $view === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.orders.index', ['view' => 'orders']) }}" class="{{ $view === 'orders' ? 'active' : '' }}">Orders</a>
            <a href="{{ route('admin.orders.index', ['view' => 'abandoned']) }}" class="{{ $view === 'abandoned' ? 'active' : '' }}">Abandoned Cart</a>
        </div>

        {{-- Stats --}}
        @php
            $currentStatus = request('status', 'all');
            $statCards = [
                ['key' => 'all', 'label' => 'All Orders', 'count' => $totalOrders, 'icon' => 'all'],
                ['key' => 'pending', 'label' => 'Pending', 'count' => $pendingOrders, 'icon' => 'pending'],
                ['key' => 'processing', 'label' => 'Processing', 'count' => $processingOrders + $confirmedOrders, 'icon' => 'processing'],
                ['key' => 'shipped', 'label' => 'Shipped', 'count' => $shippedOrders, 'icon' => 'shipped'],
                ['key' => 'delivered', 'label' => 'Delivered', 'count' => $deliveredOrders + $completedOrders, 'icon' => 'delivered'],
                ['key' => 'returned', 'label' => 'Returned', 'count' => $returnedOrders, 'icon' => 'returned'],
                ['key' => 'cancelled', 'label' => 'Cancelled', 'count' => $cancelledOrders, 'icon' => 'cancelled'],
            ];
        @endphp
        <div id="statsRow" class="stats-row">
            @foreach ($statCards as $card)
                <div class="stat-card {{ $currentStatus === $card['key'] ? 'active' : '' }}"
                     data-status="{{ $card['key'] }}" @click="filterByStatus('{{ $card['key'] }}')">
                    <div>
                        <div class="stat-label">{{ $card['label'] }}</div>
                        <div class="stat-value">{{ $card['count'] }}</div>
                        @if ($card['key'] === 'all')
                            <div class="stat-revenue">Total Revenue: {{ $currencySymbol }}{{ number_format($totalRevenue, 0) }}</div>
                        @endif
                    </div>
                    <div class="stat-icon {{ $card['icon'] }}">
                        @if ($card['icon'] === 'all')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18l-1.5 12.5a2 2 0 01-2 1.5H6.5a2 2 0 01-2-1.5L3 7z"/><path d="M8 7V5a4 4 0 018 0v2"/></svg>
                        @elseif ($card['icon'] === 'pending')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        @elseif ($card['icon'] === 'processing')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-3.5-7.1"/><path d="M21 4v5h-5"/></svg>
                        @elseif ($card['icon'] === 'shipped')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="7" width="15" height="11" rx="1"/><path d="M16 10h4l3 3v5h-7v-8z"/><circle cx="5.5" cy="20.5" r="1.5"/><circle cx="18.5" cy="20.5" r="1.5"/></svg>
                        @elseif ($card['icon'] === 'delivered')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                        @elseif ($card['icon'] === 'returned')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="11" width="12" height="10" rx="2"/>
                                <path d="M2 11l4-4"/>
                                <path d="M6 7h5"/>
                                <path d="M20 7v4a2 2 0 01-2 2h-3"/>
                                <polyline points="13 9 15 7 13 5"/>
                            </svg>
                        @elseif ($card['icon'] === 'cancelled')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                        @endif
                    </div>
                </div>
            @endforeach
            <a href="{{ route('admin.orders.index', ['view' => 'abandoned']) }}"
               class="stat-card {{ $view === 'abandoned' ? 'active' : '' }}"
               title="Carts not converted to an order in the last 24 hours">
                <div>
                    <div class="stat-label">Abandoned Carts</div>
                    <div class="stat-value">{{ $abandonedTotal }}</div>
                    <div class="stat-revenue" style="color:var(--amber);">Potential: {{ $currencySymbol }}{{ number_format($abandonedCarts->sum('total'), 0) }}</div>
                </div>
                <div class="stat-icon abandoned">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18v3a4 4 0 010 6v3H3v-3a4 4 0 010-6V6z"/><path d="M8 6V5a2 2 0 012-2h4a2 2 0 012 2v1"/><path d="M12 12v5"/><path d="M8 17h8"/></svg>
                </div>
            </a>
        </div>

        {{-- Table Panel --}}
        <div id="panelWrap" class="panel loading-overlay">

            @if ($view !== 'abandoned')
            {{-- Filter Bar --}}
            <div class="filter-bar">
                <div class="search-wrap" :class="{ 'has-value': searchQuery.length > 0 }">
                    <svg class="icon-search" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" id="searchInput" placeholder="Search by order ID, customer name or phone..."
                           value="{{ request('search') }}" x-model="searchQuery" @input.debounce.350ms="applyFilters()">
                    <button type="button" class="search-clear" @click="clearSearch()" title="Clear">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <select class="filter-select" id="paymentFilter" @change="applyFilters()">
                    <option value="">All payment methods</option>
                    <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                    <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                    <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                    <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                </select>
                <select class="filter-select" id="courierFilter" @change="applyFilters()">
                    <option value="">All couriers</option>
                    <option value="pathao" {{ request('courier') === 'pathao' ? 'selected' : '' }}>Pathao</option>
                    <option value="steadfast" {{ request('courier') === 'steadfast' ? 'selected' : '' }}>Steadfast</option>
                    <option value="redx" {{ request('courier') === 'redx' ? 'selected' : '' }}>RedX</option>
                    <option value="none" {{ request('courier') === 'none' ? 'selected' : '' }}>Self Delivery</option>
                </select>
                <select class="filter-select" id="sortFilter" @change="applyFilters()">
                    <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest first</option>
                    <option value="oldest" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>Oldest first</option>
                    <option value="high" {{ ($sort ?? '') === 'high' ? 'selected' : '' }}>Total: high to low</option>
                    <option value="low" {{ ($sort ?? '') === 'low' ? 'selected' : '' }}>Total: low to high</option>
                </select>
                <div class="date-range">
                    <input type="date" id="dateFrom" class="date-input" value="{{ request('date_from', $dateFrom ?? '') }}" @change="applyFilters()">
                    <span>to</span>
                    <input type="date" id="dateTo" class="date-input" value="{{ request('date_to', $dateTo ?? '') }}" @change="applyFilters()">
                </div>
                <select class="filter-select" id="perPageFilter" @change="applyFilters()">
                    @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ (int) $perPage === $size ? 'selected' : '' }}>{{ $size }} / page</option>
                    @endforeach
                </select>
                @if(request()->hasAny(['search','payment_method','courier','sort','per_page','date_from','date_to']) || request()->input('status'))
                    <a href="{{ route('admin.orders.index') }}" class="filter-btn" title="Clear all filters" style="color:var(--red); border-color:var(--red);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Clear
                    </a>
                @endif
                <a :href="buildExportUrl()" class="filter-btn" title="Export orders">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export
                </a>
                <a href="{{ route('admin.orders.create') }}" class="filter-btn primary" title="New order">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    New Order
                </a>
                <div style="position:relative;">
                    <button type="button" @click="showSettings = !showSettings"
                            class="icon-btn" title="Table settings">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                    <div x-show="showSettings" x-cloak
                         @click.outside="showSettings = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         style="position:absolute; right:0; top:calc(100% + 8px); background:#fff; border:1px solid var(--border); border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.12); padding:18px 20px; z-index:50; width:320px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                            <span style="font-size:13px; font-weight:700; color:var(--ink);">Columns</span>
                            <button type="button" @click="showSettings = false" style="background:none; border:none; cursor:pointer; color:var(--ink-soft); padding:2px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            @foreach ([
                                'profit' => 'Profit',
                                'items' => 'Items',
                                'address' => 'Customer Address',
                                'fraud' => 'Fraud Check',
                                'ip' => 'IP Address',
                                'courier' => 'Courier',
                            ] as $key => $label)
                                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                                    <input type="checkbox"
                                           :checked="columns.{{ $key }}"
                                           @change="toggleColumn('{{ $key }}')"
                                           style="width:16px; height:16px; border-radius:4px; border:1.5px solid var(--border); accent-color:var(--red);">
                                    <span style="font-size:13px; color:var(--ink);">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Bulk Action Bar --}}
            @if ($view !== 'abandoned')
            <div class="bulk-bar" :class="{ 'show': selected.length > 0 }">
                <span class="bulk-text" x-text="`${selected.length} order(s) selected`"></span>
                <div style="display:flex; gap:8px; margin-left:auto; align-items:center;">
                    <form method="POST" action="{{ route('admin.orders.bulk-status') }}" style="display:flex; gap:8px; align-items:center;">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <select name="status" required class="bulk-select">
                            <option value="">Set status...</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="returned">Returned</option>
                        </select>
                        <button type="submit" class="bulk-btn primary">Update</button>
                    </form>
                    <button type="button" @click="clearAll()" class="bulk-btn secondary">Cancel</button>
                </div>
            </div>

            {{-- Table --}}
            <div id="tableWrap" style="overflow-x:auto;">
                <table class="orders">
                    <thead>
                        <tr>
                            <th style="width:48px; padding-left:20px;">
                                <input type="checkbox" class="cb"
                                       :checked="selected.length === @js($orders->count()) && selected.length > 0"
                                       @change="toggleAll($event.target.checked)">
                            </th>
                            <th x-show="columns.order">Order</th>
                            <th x-show="columns.customer">Customer</th>
                            <th x-show="columns.items">Items</th>
                            <th x-show="columns.address">Address</th>
                            <th x-show="columns.payment">Payment</th>
                            <th x-show="columns.status">Status</th>
                            <th x-show="columns.ip">IP</th>
                            <th x-show="columns.fraud">Fraud</th>
                            <th x-show="columns.courier">Courier</th>
                            <th x-show="columns.total" class="num">Total</th>
                            <th x-show="columns.profit" class="num">Profit</th>
                            <th x-show="columns.actions" class="num">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersBody">
                        @forelse ($orders as $order)
                            @php
                                $displayName = $order->user->name ?? trim(($order->first_name ?? '').' '.($order->last_name ?? ''));
                                $initials = strtoupper(substr($displayName ?: 'U', 0, 1));
                                $payLabel = match($order->payment_method) {
                                    'bkash' => 'bKash',
                                    'nagad' => 'Nagad',
                                    'rocket' => 'Rocket',
                                    'cod' => 'COD',
                                    'card' => 'Card',
                                    'bank' => 'Bank',
                                    default => ucfirst($order->payment_method),
                                };
                                $profit = $order->items->sum(fn ($item) => ($item->price - ($item->product?->cost_price ?? 0)) * $item->quantity);
                                $courierClass = match($order->courier_name) {
                                    'pathao' => 'pathao',
                                    'redx' => 'redx',
                                    'steadfast' => 'steadfast',
                                    default => 'none',
                                };
                                $courierLabel = match($order->courier_name) {
                                    'pathao' => 'Pathao',
                                    'redx' => 'RedX',
                                    'steadfast' => 'Steadfast',
                                    default => '-',
                                };
                                $fullAddress = str_replace(', Bangladesh', '', str_replace(' Bangladesh', '', $order->address ?? ''));
                            @endphp
                            <tr :class="{ 'row-selected': selected.includes({{ $order->id }}) }">
                                <td style="padding-left:20px;">
                                    <input type="checkbox" class="cb"
                                           :checked="selected.includes({{ $order->id }})"
                                           @change="toggle({{ $order->id }})">
                                </td>
                                <td x-show="columns.order" class="order-id-cell">
                                    <div class="order-id mono">#{{ $order->order_number }}</div>
                                    <div class="order-date mono">{{ $order->created_at->format('M j, Y') }} {{ $order->created_at->format('g:i A') }}</div>
                                </td>
                                <td x-show="columns.customer">
                                    <div class="cust-cell">
                                        <div class="cust-avatar">{{ $initials }}</div>
                                        <div>
                                            <div class="cust-name">
                                                {{ $displayName ?: 'Guest' }}
                                                @if (($order->customer_segment ?? 'new') === 'repeated')
                                                    <span class="cust-seg repeated">(Repeated)</span>
                                                @else
                                                    <span class="cust-seg new">(New)</span>
                                                @endif
                                            </div>
                                            <div class="cust-phone mono">{{ $order->phone ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td x-show="columns.items">
                                    <div class="items-tooltip-wrap">
                                        <div style="font-size:12px; color:var(--ink-soft);">
                                            {{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}
                                        </div>
                                        <div class="items-tooltip">
                                            @foreach($order->items as $item)
                                                <div class="it-row">
                                                    <div class="it-thumb">
                                                        @if($item->product && $item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="">
                                                        @else
                                                            {{ strtoupper(substr($item->product->name ?? 'P', 0, 1)) }}
                                                        @endif
                                                    </div>
                                                    <div class="it-info">
                                                        <div class="it-name">{{ $item->product->name ?? $item->product_name ?? 'Deleted Product' }}</div>
                                                        <div class="it-calc">{{ $currencySymbol }}{{ number_format($item->price, 2) }} x {{ $item->quantity }} = <span>{{ $currencySymbol }}{{ number_format($item->price * $item->quantity, 2) }}</span></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td x-show="columns.address">
                                    <div style="font-size:12px; color:var(--ink-soft); max-width:160px; line-height:1.4;">
                                        {{ $fullAddress ?: '-' }}
                                    </div>
                                </td>
                                <td x-show="columns.payment">
                                    <span class="pay-chip">
                                        <span class="pay-dot {{ $order->payment_method }}"></span>
                                        {{ $payLabel }}
                                    </span>
                                </td>
                                <td x-show="columns.status">
                                    <span class="status-pill {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td x-show="columns.ip">
                                    <div class="mono" style="font-size:11px; color:var(--ink-soft); max-width:120px; overflow:hidden; text-overflow:ellipsis;" title="{{ $order->ip_address ?? '-' }}">
                                        {{ $order->ip_address ?? '-' }}
                                    </div>
                                </td>
                                <td x-show="columns.fraud">
                                    @if($order->fraudCheck)
                                        @php
                                            $ratio = $order->fraudCheck->success_ratio;
                                            $fraudData = $order->fraudCheck->data ?? [];
                                        @endphp
                                        <div class="fraud-wrap">
                                            <span class="fraud-pct {{ ($ratio >= 80) ? 'high' : (($ratio >= 50) ? 'medium' : 'low') }}">
                                                {{ $ratio }}%
                                            </span>
                                            <div class="fraud-tooltip">
                                                @php $summary = $fraudData['summary'] ?? []; @endphp
                                                <div class="fraud-tooltip-header">
                                                    <span class="fraud-tooltip-name" style="font-size:10px; text-transform:uppercase; letter-spacing:0.04em;">Overview</span>
                                                    <span class="fraud-tooltip-detail">{{ $summary['total_parcel'] ?? 0 }} parcels</span>
                                                    <span class="fraud-tooltip-delivered">{{ $summary['success_parcel'] ?? 0 }} Delivered</span>
                                                    <span class="fraud-tooltip-cancel">{{ $summary['cancelled_parcel'] ?? 0 }} cancelled</span>
                                                    <span class="fraud-tooltip-ratio {{ ($summary['success_ratio'] ?? 0) >= 80 ? 'high' : (($summary['success_ratio'] ?? 0) >= 50 ? 'medium' : 'low') }}">{{ $summary['success_ratio'] ?? 0 }}%</span>
                                                </div>
                                                @foreach($fraudData as $courier => $info)
                                                    @if($courier !== 'summary' && is_array($info) && isset($info['name']) && ($info['total_parcel'] ?? 0) > 0)
                                                        <div class="fraud-tooltip-row">
                                                            <span class="fraud-tooltip-name">{{ $info['name'] }}</span>
                                                            <span class="fraud-tooltip-detail">{{ $info['total_parcel'] ?? 0 }} parcels</span>
                                                            <span class="fraud-tooltip-delivered">{{ $info['success_parcel'] ?? 0 }} Delivered</span>
                                                            <span class="fraud-tooltip-cancel">{{ $info['cancelled_parcel'] ?? 0 }} cancelled</span>
                                                            <span class="fraud-tooltip-ratio {{ ($info['success_ratio'] ?? 0) >= 80 ? 'high' : (($info['success_ratio'] ?? 0) >= 50 ? 'medium' : 'low') }}">{{ $info['success_ratio'] ?? 0 }}%</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="fraud-pct loading">-</span>
                                    @endif
                                </td>
                                <td x-show="columns.courier">
                                    <span class="courier-pill {{ $courierClass }}">{{ $courierLabel }}</span>
                                </td>
                                <td x-show="columns.total" class="num">
                                    <span class="row-total mono">{{ $currencySymbol }}{{ number_format($order->total_price, 0) }}</span>
                                </td>
                                <td x-show="columns.profit" class="num">
                                    <span class="mono" style="font-size:12.5px; font-weight:600; color:{{ $profit >= 0 ? 'var(--green)' : 'var(--red)' }}">
                                        {{ $currencySymbol }}{{ number_format($profit, 0) }}
                                    </span>
                                </td>
                                <td x-show="columns.actions" class="num">
                                    <div class="action-cell">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="icon-btn" title="View">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="icon-btn edit" title="Edit">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <button type="button" class="icon-btn delete" title="Delete" @click="deleteOrder({{ $order->id }}, '{{ $order->order_number }}')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13">
                                    <div class="empty-state" style="display:flex;">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                                        <p>No orders match your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div id="tableFoot">
                @if ($orders->hasPages())
                    <div class="table-foot">
                        <div class="count">
                            Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders
                        </div>
                        <div class="pagination">
                            @if ($orders->onFirstPage())
                                <span class="page-btn disabled">&lsaquo;</span>
                            @else
                                <a href="#" class="page-btn" @click.prevent="loadPage('{{ $orders->previousPageUrl() }}')">&lsaquo;</a>
                            @endif

                            @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 2), min($orders->lastPage(), $orders->currentPage() + 2)) as $page => $url)
                                @if ($page == $orders->currentPage())
                                    <span class="page-btn active">{{ $page }}</span>
                                @else
                                    <a href="#" class="page-btn" @click.prevent="loadPage('{{ $url }}')">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($orders->hasMorePages())
                                <a href="#" class="page-btn" @click.prevent="loadPage('{{ $orders->nextPageUrl() }}')">&rsaquo;</a>
                            @else
                                <span class="page-btn disabled">&rsaquo;</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="table-foot">
                        <div class="count">Showing {{ $orders->count() }} of {{ $orders->count() }} orders</div>
                    </div>
                @endif
            </div>
            @endif

            {{-- Abandoned Carts Section --}}
            @if ($view !== 'orders')
                @if ($view === 'abandoned')
                <div class="filter-bar">
                    <div class="search-wrap">
                        <svg class="icon-search" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" id="searchInputAbandoned" placeholder="Search by customer name or phone..."
                               value="{{ request('search') }}" @keydown.enter="location.href = _buildSearchUrl()">
                    </div>
                    <div class="date-range">
                        <input type="date" id="abandonedDateFrom" class="date-input" value="{{ request('date_from', $dateFrom ?? '') }}" @change="location.href = _buildAbandonedFilterUrl()">
                        <span>to</span>
                        <input type="date" id="abandonedDateTo" class="date-input" value="{{ request('date_to', $dateTo ?? '') }}" @change="location.href = _buildAbandonedFilterUrl()">
                    </div>
                    <select id="abandonedReminderFilter" class="filter-select" @change="location.href = _buildAbandonedFilterUrl()">
                        <option value="">All reminders</option>
                        <option value="none" @selected(request('reminder_type') === 'none')>Not sent</option>
                        <option value="reminder" @selected(request('reminder_type') === 'reminder')>Reminder</option>
                        <option value="stock" @selected(request('reminder_type') === 'stock')>Stock limited</option>
                        <option value="discount" @selected(request('reminder_type') === 'discount')>5% discount</option>
                        <option value="ignored" @selected(request('reminder_type') === 'ignored')>Ignored</option>
                    </select>
                    <button type="button" class="filter-btn primary" @click="location.href = _buildSearchUrl()">Search</button>
                    <a href="{{ route('admin.orders.index', ['view' => 'abandoned']) }}" class="filter-btn" title="Clear filters">Clear</a>
                </div>
                @endif
                @if ($view === 'all')
                <div style="padding:16px 20px; border-bottom:1px solid var(--border);">
                    <div style="font-size:14px; font-weight:800; color:var(--ink);">Abandoned Carts</div>
                    <div style="font-size:12px; color:var(--ink-soft); margin-top:2px;">Carts left without an order in the last 24 hours</div>
                </div>
                @endif
                <div id="abandonedTableWrap" style="overflow-x:auto;">
                    <table class="orders">
                        <thead>
                            <tr>
                                <th style="padding-left:20px;">Customer</th>
                                <th>Items</th>
                                <th>Last Activity</th>
                                <th>Status</th>
                                <th class="num">Total</th>
                                <th class="num">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="abandonedBody">
                            @forelse ($abandonedCarts as $cart)
                                @php
                                    $displayName = $cart->name ?? $cart->user?->name ?? ($cart->phone ?? 'Guest');
                                    $initials = strtoupper(substr($displayName ?: 'G', 0, 1));
                                    $cartPayload = [
                                        'id' => $cart->id,
                                        'name' => $displayName,
                                        'phone' => $cart->phone ?? $cart->user?->phone ?? '',
                                        'address' => $cart->address,
                                        'item_count' => $cart->item_count,
                                        'total' => (float) $cart->total,
                                        'last_activity' => $cart->last_activity_at?->format('M j, Y g:i A'),
                                        'items' => collect($cart->items_data)->map(fn ($i) => [
                                            'name' => $i['name'] ?? 'Deleted Product',
                                            'image' => ! empty($i['image']) ? asset('storage/'.$i['image']) : null,
                                            'quantity' => $i['quantity'] ?? 1,
                                            'variant' => $i['variant'] ?? null,
                                            'subtotal' => (float) ($i['subtotal'] ?? 0),
                                        ])->all(),
                                    ];
                                    if (! $cart->coupon_code) {
                                        app(\App\Services\AbandonedCartService::class)->ensureCoupon($cart);
                                        $cart->refresh();
                                    }
                                    $waPlaceholders = [
                                        '{name}' => $displayName,
                                        '{phone}' => $cart->phone ?? $cart->user?->phone ?? '',
                                        '{total}' => $currencySymbol.number_format($cart->total, 0),
                                        '{item_count}' => $cart->item_count,
                                        '{store_name}' => $storeSettings['store_name'] ?? config('app.name', 'Store'),
                                        '{cart_link}' => \Illuminate\Support\Facades\URL::signedRoute('cart.restore', ['abandonedCart' => $cart->id]),
                                        '{url}' => url('/'),
                                        '{coupon_code}' => $cart->coupon_code ?? '',
                                    ];
                                    $waTemplates = [
                                        '1. Reminder' => $storeSettings['abandoned_whatsapp_reminder_message'] ?? '',
                                        '2. Stock limited' => $storeSettings['abandoned_whatsapp_stock_message'] ?? '',
                                        '3. 5% discount' => $storeSettings['abandoned_whatsapp_discount_message'] ?? '',
                                    ];
                                    $waDefaults = [
                                        '1. Reminder' => 'Hi {name}! You have {item_count} item(s) worth {total} in your cart at {store_name}. Ready to complete your order? {cart_link}',
                                        '2. Stock limited' => "Hi {name}! Some items in your cart are now in limited stock. Complete your order soon so they don't sell out: {cart_link}",
                                        '3. 5% discount' => 'Hi {name}! Complete your order today and get 5% off with code {coupon_code}. Shop now: {cart_link}',
                                    ];
                                    $waReminderTypes = [
                                        '1. Reminder' => 'reminder',
                                        '2. Stock limited' => 'stock',
                                        '3. 5% discount' => 'discount',
                                    ];
                                    $waLinkItems = [];
                                    if (($storeSettings['abandoned_whatsapp_enabled'] ?? '1') === '1' && $waPlaceholders['{phone}'] !== '') {
                                        foreach ($waTemplates as $waLabel => $waTemplate) {
                                            $waTemplate = $waTemplate !== '' ? $waTemplate : $waDefaults[$waLabel];
                                            $waLinkItems[] = [
                                                'label' => $waLabel,
                                                'type' => $waReminderTypes[$waLabel] ?? null,
                                                'url' => \App\Helpers\PhoneHelper::whatsappLink($waPlaceholders['{phone}'], $storeSettings['timezone'] ?? null)
                                                    .'?text='.rawurlencode(str_replace(array_keys($waPlaceholders), array_values($waPlaceholders), $waTemplate)),
                                            ];
                                        }
                                    }
                                    $reminderLabels = [
                                        'reminder' => 'Reminder',
                                        'stock' => 'Stock limited',
                                        'discount' => '5% discount',
                                    ];
                                    $isIgnored = (bool) $cart->ignored_at;
                                    $ignoreConfirm = $isIgnored ? 'Stop ignoring this abandoned cart?' : 'Ignore this abandoned cart?';
                                    $ignoreTitle = $isIgnored ? 'Un-ignore' : 'Ignore';
                                @endphp
                                <tr @class(['abandoned-row-ignored' => $cart->ignored_at])>
                                    <td style="padding-left:20px;">
                                        <div class="cust-cell">
                                            <div class="cust-avatar">{{ $initials }}</div>
                                            <div>
                                                <div class="cust-name">{{ $displayName }}</div>
                                                <div class="cust-phone mono">{{ $cart->phone ?? ($cart->user?->phone ?? '') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="items-tooltip-wrap">
                                            <div style="font-size:12px; color:var(--ink-soft);">
                                                {{ $cart->item_count }} item{{ $cart->item_count !== 1 ? 's' : '' }}
                                            </div>
                                            <div class="items-tooltip">
                                                @foreach (collect($cart->items_data)->take(10) as $item)
                                                    <div class="it-row">
                                                        <div class="it-thumb" style="font-size:11px;">
                                                            @if (!empty($item['image']))
                                                                <img src="{{ asset('storage/' . $item['image']) }}" alt="">
                                                            @else
                                                                {{ strtoupper(substr($item['name'] ?? 'P', 0, 1)) }}
                                                            @endif
                                                        </div>
                                                        <div class="it-info">
                                                            <div class="it-name">{{ $item['name'] ?? 'Deleted Product' }}</div>
                                                            <div class="it-calc">{{ $currencySymbol }}{{ number_format((float) ($item['subtotal'] ?? 0) / max((int) ($item['quantity'] ?? 1), 1), 2) }} x {{ $item['quantity'] ?? 1 }} = <span>{{ $currencySymbol }}{{ number_format((float) ($item['subtotal'] ?? 0), 2) }}</span></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if (collect($cart->items_data)->count() > 10)
                                                    <div class="it-row" style="justify-content:center;">
                                                        <span style="font-size:11px; color:var(--ink-soft);">+{{ collect($cart->items_data)->count() - 10 }} more</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <div class="date-main">{{ $cart->last_activity_at?->format('M j, Y') }}</div>
                                            <div class="date-sub">{{ $cart->last_activity_at?->diffForHumans() }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display:flex; flex-direction:column; gap:4px; align-items:flex-start;">
                                            <span class="status-pill {{ $cart->ignored_at ? 'cancelled' : 'returned' }}">{{ $cart->ignored_at ? 'Ignored' : 'Abandoned' }}</span>
                                            @if ($cart->reminder_type)
                                                <span class="status-pill pending" title="{{ ucfirst($reminderLabels[$cart->reminder_type] ?? $cart->reminder_type) }} sent {{ $cart->last_reminder_sent_at?->format('M j, Y g:i A') }} · {{ $cart->reminder_sent_count }}×">
                                                    {{ $reminderLabels[$cart->reminder_type] ?? $cart->reminder_type }} sent
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="num">
                                        <span class="row-total mono">{{ $currencySymbol }}{{ number_format($cart->total, 0) }}</span>
                                    </td>
                                    <td class="num">
                                        <div class="action-cell">
                                            <button type="button" class="icon-btn view" title="View cart" @click="openCart(@js($cartPayload))">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </button>
                                            @if ($cart->phone && ! empty($waLinkItems))
                                                <div x-data="waMenu(@js($waLinkItems), '{{ route('admin.abandoned-carts.reminder', $cart) }}')" @click.outside="open = false">
                                                    <button type="button" class="icon-btn whatsapp" title="WhatsApp" @click="toggle($event)">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm5.2 13.9c-.2.7-1.2 1.3-1.8 1.4-.5.1-1.1.2-3.6-.8-3-1.2-5-4.3-5.1-4.5-.2-.2-1.3-1.7-1.3-3.2s.8-2.3 1.1-2.6c.3-.3.6-.4.8-.4h.6c.2 0 .4 0 .6.5.2.5.7 1.8.8 1.9.1.1.1.3 0 .5-.1.2-.2.3-.3.5-.1.2-.3.3-.2.5.1.2.6 1 1.3 1.6.9.8 1.6 1 1.8 1.1.2.1.4.1.5-.1l.8-.9c.2-.2.3-.2.6-.1l1.7.8c.2.1.4.2.4.3.1.2.1.7-.1 1.2z"/></svg>
                                                    </button>
                                                    <div x-show="open" x-cloak :style="menuStyle" class="wa-menu">
                                                        <template x-for="item in items" :key="item.label">
                                                            <a :href="item.url" target="_blank" @click="trackReminder(item)" class="wa-menu-item">
                                                                <svg class="wa-menu-icon" width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm5.2 13.9c-.2.7-1.2 1.3-1.8 1.4-.5.1-1.1.2-3.6-.8-3-1.2-5-4.3-5.1-4.5-.2-.2-1.3-1.7-1.3-3.2s.8-2.3 1.1-2.6c.3-.3.6-.4.8-.4h.6c.2 0 .4 0 .6.5.2.5.7 1.8.8 1.9.1.1.1.3 0 .5-.1.2-.2.3-.3.5-.1.2-.3.3-.2.5.1.2.6 1 1.3 1.6.9.8 1.6 1 1.8 1.1.2.1.4.1.5-.1l.8-.9c.2-.2.3-.2.6-.1l1.7.8c.2.1.4.2.4.3.1.2.1.7-.1 1.2z"/></svg>
                                                                <span x-text="item.label"></span>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                            @elseif ($cart->phone)
                                                <a href="{{ \App\Helpers\PhoneHelper::whatsappLink($cart->phone, $storeSettings['timezone'] ?? null) }}" target="_blank" class="icon-btn whatsapp" title="WhatsApp">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm5.2 13.9c-.2.7-1.2 1.3-1.8 1.4-.5.1-1.1.2-3.6-.8-3-1.2-5-4.3-5.1-4.5-.2-.2-1.3-1.7-1.3-3.2s.8-2.3 1.1-2.6c.3-.3.6-.4.8-.4h.6c.2 0 .4 0 .6.5.2.5.7 1.8.8 1.9.1.1.1.3 0 .5-.1.2-.2.3-.3.5-.1.2-.3.3-.2.5.1.2.6 1 1.3 1.6.9.8 1.6 1 1.8 1.1.2.1.4.1.5-.1l.8-.9c.2-.2.3-.2.6-.1l1.7.8c.2.1.4.2.4.3.1.2.1.7-.1 1.2z"/></svg>
                                                </a>
                                            @endif
                                            @if ($cart->user_id)
                                                <a href="{{ route('admin.customers.show', $cart->user_id) }}" class="icon-btn" title="View customer">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </a>
                                            @endif
                                            <form method="POST" action="{{ route('admin.abandoned-carts.ignore', $cart) }}" class="inline" onsubmit="return confirm('{{ $ignoreConfirm }}')">
                                                @csrf
                                                <button type="submit" class="icon-btn {{ $isIgnored ? 'ignored' : 'delete' }}" title="{{ $ignoreTitle }}">
                                                    @if ($isIgnored)
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 0115.5-6.4M21 12a9 9 0 01-15.5 6.4"/><path d="M3 4l4 4M17 16l4 4"/></svg>
                                                    @else
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><line x1="5.6" y1="5.6" x2="18.4" y2="18.4"/></svg>
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state" style="display:flex;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 6h18v3a4 4 0 010 6v3H3v-3a4 4 0 010-6V6z"/><path d="M8 6V5a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>
                                            <p>No abandoned carts match your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="abandonedFoot">
                    @if ($abandonedCarts->hasPages())
                        <div class="table-foot">
                            <div class="count">
                                Showing {{ $abandonedCarts->firstItem() }}–{{ $abandonedCarts->lastItem() }} of {{ $abandonedCarts->total() }} abandoned carts
                            </div>
                            <div class="pagination">
                                {{ $abandonedCarts->appends(request()->except('page'))->links() }}
                            </div>
                        </div>
                    @else
                        <div class="table-foot">
                            <div class="count">Showing {{ $abandonedCarts->count() }} of {{ $abandonedCarts->count() }} abandoned carts</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="cart-modal-overlay" x-show="viewCart" x-cloak x-transition.opacity @click.self="closeCart()">
            <div class="cart-modal">
                <template x-if="viewCart">
                    <div>
                        <div class="cart-modal-head">
                            <div>
                                <div class="cart-modal-title">Abandoned Cart</div>
                                <div class="cart-modal-sub">
                                    <span x-text="viewCart?.name"></span>
                                    <template x-if="viewCart?.phone">
                                        <span> &middot; <span class="mono" x-text="viewCart?.phone"></span></span>
                                    </template>
                                    <template x-if="viewCart?.last_activity">
                                        <span> &middot; Last activity <span x-text="viewCart?.last_activity"></span></span>
                                    </template>
                                </div>
                            </div>
                            <button type="button" class="icon-btn" title="Close" @click="closeCart()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div class="cart-modal-address" x-show="viewCart?.address">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div>
                                <div class="addr-label">Delivery address</div>
                                <div class="addr-text" x-text="viewCart?.address"></div>
                            </div>
                        </div>
                        <div class="cart-modal-items">
                            <template x-for="(item, idx) in (viewCart?.items || [])" :key="idx">
                                <div class="it-row">
                                    <div class="it-thumb">
                                        <template x-if="item.image">
                                            <img :src="item.image" alt="">
                                        </template>
                                        <template x-if="!item.image">
                                            <span x-text="item.name ? item.name.charAt(0).toUpperCase() : 'P'"></span>
                                        </template>
                                    </div>
                                    <div class="it-info">
                                        <div class="it-name" x-text="item.name"></div>
                                        <div class="it-calc" x-show="item.variant && item.variant !== 'Default'" x-text="item.variant"></div>
                                    </div>
                                    <div class="it-qty" x-text="'x' + (item.quantity || 1)"></div>
                                    <div class="it-amt mono" x-text="'{{ $currencySymbol }}' + (item.subtotal || 0).toFixed(2)"></div>
                                </div>
                            </template>
                        </div>
                        <div class="cart-modal-foot">
                            <div class="cart-modal-total">
                                <span x-text="(viewCart?.item_count || 0) + ' item' + (viewCart?.item_count === 1 ? '' : 's')"></span>
                                <span class="mono" x-text="'{{ $currencySymbol }}' + (viewCart?.total || 0).toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
    <script>
        function waMenu(items, recordUrl) {
            return {
                items: items,
                recordUrl: recordUrl || '',
                open: false,
                menuStyle: { left: '0px', top: '0px' },
                toggle(event) {
                    const rect = event.currentTarget.getBoundingClientRect();
                    this.menuStyle = {
                        left: Math.max(6, Math.min(rect.left, window.innerWidth - 206)) + 'px',
                        top: (rect.bottom + 6) + 'px',
                    };
                    this.open = !this.open;
                },
                async trackReminder(item) {
                    this.open = false;
                    if (!this.recordUrl || !item.type) return;
                    try {
                        await fetch(this.recordUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ type: item.type }),
                        });
                    } catch (e) {
                        console.error('Reminder track error:', e);
                    }
                },
            };
        }

        function orderIndex() {
            return {
                selected: [],
                searchQuery: '{{ request("search") }}',
                loading: false,
                showSettings: false,
                viewCart: null,
                columns: {
                    order: true, customer: true, date: false, payment: true,
                    status: true, total: true, actions: true,
                    profit: false, items: false, address: false,
                    fraud: false, ip: false, courier: false,
                },
                init() {
                    const saved = localStorage.getItem('admin_order_columns');
                    if (saved) { try { Object.assign(this.columns, JSON.parse(saved)); } catch (e) {} }
                    this.$watch('columns', (val) => {
                        localStorage.setItem('admin_order_columns', JSON.stringify(val));
                    }, { deep: true });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') { this.showSettings = false; this.closeCart(); }
                    });
                },
                openCart(cart) { this.viewCart = cart; },
                closeCart() { this.viewCart = null; },
                toggleColumn(key) { this.columns[key] = !this.columns[key]; },
                toggle(id) {
                    const idx = this.selected.indexOf(id);
                    if (idx === -1) { this.selected.push(id); } else { this.selected.splice(idx, 1); }
                },
                toggleAll(checked) {
                    const checkboxes = document.querySelectorAll('#ordersBody .cb');
                    this.selected = checked ? JSON.parse('{{ $orders->pluck("id")->toJson() }}') : [];
                },
                clearAll() { this.selected = []; },

                clearSearch() {
                    this.searchQuery = '';
                    this.applyFilters();
                },

                buildExportUrl() {
                    return this._buildUrl('{{ route("admin.orders.export") }}');
                },

                async deleteOrder(id, orderNumber) {
                    if (!confirm(`Delete order #${orderNumber}? This cannot be undone.`)) return;
                    const url = '{{ route("admin.orders.index") }}/' + id;
                    try {
                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (res.redirected || res.ok) {
                            location.reload();
                        }
                    } catch (e) {
                        console.error('Delete error:', e);
                    }
                },

                _buildUrl(pageUrl) {
                    const params = new URLSearchParams();
                    const view = '{{ $view }}';
                    const status = this._getActiveStatus();
                    const search = this.searchQuery.trim();
                    const payment = document.getElementById('paymentFilter').value;
                    const courier = document.getElementById('courierFilter').value;
                    const sort = document.getElementById('sortFilter').value;
                    const perPage = document.getElementById('perPageFilter').value;
                    const dateFrom = document.getElementById('dateFrom').value;
                    const dateTo = document.getElementById('dateTo').value;

                    if (view && view !== 'orders') params.set('view', view);
                    if (status && status !== 'all') params.set('status', status);
                    if (search) params.set('search', search);
                    if (payment) params.set('payment_method', payment);
                    if (courier) params.set('courier', courier);
                    if (dateFrom) params.set('date_from', dateFrom);
                    if (dateTo) params.set('date_to', dateTo);
                    if (sort && sort !== 'newest') params.set('sort', sort);
                    if (perPage && perPage !== '10') params.set('per_page', perPage);

                    const base = pageUrl || '{{ route("admin.orders.index") }}';
                    const sep = base.includes('?') ? '&' : '?';
                    return base + sep + params.toString();
                },

                _buildSearchUrl() {
                    const search = document.getElementById('searchInputAbandoned').value.trim();
                    const params = new URLSearchParams();
                    if (search) params.set('search', search);
                    const dateFrom = document.getElementById('abandonedDateFrom').value;
                    const dateTo = document.getElementById('abandonedDateTo').value;
                    if (dateFrom) params.set('date_from', dateFrom);
                    if (dateTo) params.set('date_to', dateTo);
                    const reminder = document.getElementById('abandonedReminderFilter').value;
                    if (reminder) params.set('reminder_type', reminder);
                    const base = '{{ route("admin.orders.index", ["view" => "abandoned"]) }}';
                    const sep = base.includes('?') ? '&' : '?';
                    return base + (params.toString() ? sep + params.toString() : '');
                },

                _buildAbandonedFilterUrl() {
                    const search = document.getElementById('searchInputAbandoned').value.trim();
                    const params = new URLSearchParams();
                    if (search) params.set('search', search);
                    const dateFrom = document.getElementById('abandonedDateFrom').value;
                    const dateTo = document.getElementById('abandonedDateTo').value;
                    if (dateFrom) params.set('date_from', dateFrom);
                    if (dateTo) params.set('date_to', dateTo);
                    const reminder = document.getElementById('abandonedReminderFilter').value;
                    if (reminder) params.set('reminder_type', reminder);
                    const base = '{{ route("admin.orders.index", ["view" => "abandoned"]) }}';
                    const sep = base.includes('?') ? '&' : '?';
                    return base + (params.toString() ? sep + params.toString() : '');
                },

                _getActiveStatus() {
                    const card = document.querySelector('.stat-card.active');
                    return card ? card.dataset.status : 'all';
                },

                _setActiveStatus(status) {
                    document.querySelectorAll('.stat-card').forEach(c => {
                        c.classList.toggle('active', c.dataset.status === status);
                    });
                },

                _showLoading() {
                    const el = document.getElementById('panelWrap');
                    el.style.opacity = '0.6';
                    el.style.pointerEvents = 'none';
                },
                _hideLoading() {
                    const el = document.getElementById('panelWrap');
                    el.style.opacity = '1';
                    el.style.pointerEvents = '';
                },

                async _fetchAndReplace(url) {
                    this._showLoading();
                    this.selected = [];
                    try {
                        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        const html = await res.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');

                        const newBody = doc.querySelector('#ordersBody');
                        const newFoot = doc.querySelector('#tableFoot');
                        if (newBody) document.getElementById('ordersBody').replaceWith(newBody);
                        if (newFoot) document.getElementById('tableFoot').replaceWith(newFoot);

                        history.pushState({}, '', url);
                    } catch (e) {
                        console.error('Fetch error:', e);
                    } finally {
                        this._hideLoading();
                    }
                },

                filterByStatus(status) {
                    this._setActiveStatus(status);
                    const url = this._buildUrl();
                    this._fetchAndReplace(url);
                },

                applyFilters() {
                    const url = this._buildUrl();
                    this._fetchAndReplace(url);
                },

                loadPage(pageUrl) {
                    const url = this._buildUrl(pageUrl);
                    this._fetchAndReplace(url);
                },
            };
        }

        window.addEventListener('popstate', function() {
            location.reload();
        });
    </script>
    @endPushOnce
</x-admin-layout>
