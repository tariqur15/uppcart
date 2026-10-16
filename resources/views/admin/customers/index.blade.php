@php
    $avatarColors = ['#A31E22','#6C4EE8','#0891B2','#12B76A','#F5A623','#3B82F6','#64748B'];
    $initialsHelper = fn ($name) => collect(explode(' ', $name ?? '?'))->filter()->map(fn ($w) => strtoupper($w[0]))->take(2)->join('');
@endphp

<x-admin-layout>
    @push('styles')
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devchauhann/fonts@v1.1.0/cdn/v1/css/all.css">
    @endpush

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

        .customers-page *{box-sizing:border-box;}
        .customers-page{
            background:var(--bg);
            color:var(--ink);
            font-family:'Anthropic Sans', 'Inter', sans-serif;
            -webkit-font-smoothing:antialiased;
        }
        .customers-page .mono{font-family:'JetBrains Mono', monospace;}

        .customers-page .topbar{ display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
        .customers-page .topbar h1{font-size:23px; font-weight:800; margin:0 0 4px;}
        .customers-page .topbar .sub{font-size:14px; color:var(--ink-soft);}
        .customers-page .topbar-actions{display:flex; gap:10px;}
        .customers-page .btn{
            display:inline-flex; align-items:center; gap:7px;
            font-size:14px; font-weight:600; padding:10px 16px; border-radius:10px;
            cursor:pointer; border:1.5px solid var(--border); background:#fff; color:var(--ink);
            text-decoration:none; transition:background .15s ease, border-color .15s ease;
        }
        .customers-page .btn:hover{background:#F7F5F3;}
        .customers-page .btn.primary{background:var(--red); border-color:var(--red); color:#fff;}
        .customers-page .btn.primary:hover{background:var(--red-dark); border-color:var(--red-dark);}

        .customers-page .stats-row{display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:14px;}
        .customers-page .stat-card{
            background:var(--card); border-radius:14px; box-shadow:var(--shadow);
            padding:15px 18px; display:flex; align-items:center; justify-content:space-between;
            cursor:pointer; border:1.5px solid transparent; transition:border-color .15s ease;
        }
        .customers-page .stat-card.active{border-color:var(--red);}
        .customers-page .stat-label{font-size:12px; color:var(--ink-soft); margin-bottom:7px;}
        .customers-page .stat-value{font-size:22px; font-weight:800;}
        .customers-page .stat-icon{
            width:34px; height:34px; border-radius:9px; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
        }
        .customers-page .stat-icon.all{background:var(--purple-tint); color:var(--purple);}
        .customers-page .stat-icon.new{background:var(--blue-tint); color:var(--blue);}
        .customers-page .stat-icon.repeat{background:var(--green-tint); color:var(--green);}
        .customers-page .stat-icon.vip{background:var(--amber-tint); color:var(--amber);}
.customers-page .stat-icon.flagged{background:var(--red-tint); color:var(--red);}
.customers-page .stat-icon.blocked{background:var(--slate-tint); color:var(--slate);}
.customers-page .stat-icon.chat{background:var(--teal-tint); color:var(--teal);}

.customers-page .view-tabs{display:flex; gap:6px; background:var(--card); border:1px solid var(--border); padding:5px; border-radius:12px; width:fit-content; box-shadow:var(--shadow);}
.customers-page .view-tabs a{
    font-size:13px; font-weight:600; padding:8px 18px; border-radius:9px; color:var(--ink-soft);
    text-decoration:none; transition:background .15s ease, color .15s ease;
}
.customers-page .view-tabs a:hover{background:var(--gray-tint); color:var(--ink);}
.customers-page .view-tabs a.active{background:var(--red); color:#fff;}

.customers-page .panel{ background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
        .customers-page .filter-bar{
            display:flex; align-items:center; gap:10px; flex-wrap:wrap;
            padding:16px 20px; border-bottom:1px solid var(--border);
        }
        .customers-page .search-wrap{ position:relative; flex:1; min-width:220px; }
        .customers-page .search-wrap svg{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--ink-soft); }
        .customers-page .search-wrap input{
            width:100%; font-size:14px; padding:10px 12px 10px 36px;
            border-radius:9px; border:1.5px solid var(--border); background:#FBFAF9; color:var(--ink); outline:none;
        }
        .customers-page .search-wrap input:focus{border-color:var(--red); background:#fff;}
        .customers-page select.filter-select{
            font-size:14px; padding:9px 12px; border-radius:9px; border:1.5px solid var(--border);
            background:#fff; color:var(--ink); outline:none; font-family:'Anthropic Sans', 'Inter',sans-serif;
        }
        .customers-page select.filter-select:focus{border-color:var(--red);}
        .customers-page .filter-clear{
            font-size:13px; font-weight:600; padding:9px 16px; border-radius:9px; border:1.5px solid var(--border);
            background:#fff; color:var(--ink-soft); cursor:pointer; font-family:'Anthropic Sans', 'Inter',sans-serif;
            transition:all .12s ease; white-space:nowrap;
        }
        .customers-page .filter-clear:hover{border-color:var(--red); color:var(--red); background:var(--red-tint);}
        .customers-page input.filter-date{
            font-size:13px; padding:8px 10px; border-radius:9px; border:1.5px solid var(--border);
            background:#fff; color:var(--ink); outline:none; font-family:'Anthropic Sans', 'Inter',sans-serif; width:140px;
        }
        .customers-page input.filter-date:focus{border-color:var(--red);}

        .customers-page table.customers{width:100%; border-collapse:collapse;}
        .customers-page table.customers thead th{
            text-align:left; font-size:12px; text-transform:uppercase; letter-spacing:0.04em;
            color:var(--ink-soft); font-weight:700; padding:12px 20px; background:#FAFAF9;
            border-bottom:1px solid var(--border); white-space:nowrap;
        }
        .customers-page table.customers thead th.num{text-align:right;}
        .customers-page table.customers tbody td{
            padding:14px 20px; border-bottom:1px solid var(--border); font-size:14px; vertical-align:middle;
        }
        .customers-page table.customers tbody tr:last-child td{border-bottom:none;}
        .customers-page table.customers tbody tr{transition:background .12s ease;}
        .customers-page table.customers tbody tr:hover{background:#FAF8F7;}
        .customers-page td.num{text-align:right;}

        .customers-page .cust-cell{display:flex; align-items:center; gap:11px;}
        .customers-page .cust-avatar{
            width:36px; height:36px; border-radius:50%; color:#fff;
            display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; flex-shrink:0;
        }
        .customers-page .cust-name{font-weight:600; font-size:14px;}
        .customers-page .cust-email{font-size:12px; color:var(--ink-soft); margin-top:1px;}

        .customers-page .tag{
            font-size:12px; font-weight:700; padding:3px 9px; border-radius:999px; text-transform:capitalize;
        }
        .customers-page .tag.new{background:var(--blue-tint); color:var(--blue);}
        .customers-page .tag.repeat{background:var(--green-tint); color:var(--green);}
        .customers-page .tag.vip{background:var(--amber-tint); color:var(--amber);}
        .customers-page .tag.flagged{background:var(--red-tint); color:var(--red);}
        .customers-page .tag.chat{background:var(--teal-tint); color:var(--teal);}

        .customers-page .location-cell{font-size:13px; color:var(--ink-soft); display:flex; align-items:center; gap:6px;}
        .customers-page .location-cell svg{flex-shrink:0; color:var(--ink-soft);}

        .customers-page .row-orders{font-weight:600;}
        .customers-page .row-orders .cancel-note{font-size:12px; color:var(--ink-soft); font-weight:400; margin-top:1px;}
        .customers-page .row-spent{font-weight:700; color:var(--red);}

        .customers-page .last-order .date-main{font-size:14px;}
        .customers-page .last-order .date-sub{font-size:12px; color:var(--ink-soft); margin-top:1px;}

        .customers-page .action-cell{display:flex; gap:6px; justify-content:flex-end;}
        .customers-page .icon-btn{
            width:30px; height:30px; border-radius:8px; border:1.5px solid var(--border); background:#fff;
            display:flex; align-items:center; justify-content:center; color:var(--ink-soft); cursor:pointer;
            text-decoration:none; transition:background .15s ease, color .15s ease; position:relative;
        }
        .customers-page .icon-btn:hover{background:var(--gray-tint); color:var(--ink);}
        .customers-page .icon-btn.msg:hover{color:var(--green); border-color:var(--green);}
        .customers-page .msg-badge{
            position:absolute; top:-6px; right:-6px; min-width:15px; height:15px; padding:0 3px;
            border-radius:999px; background:var(--red); color:#fff; font-size:9px; font-weight:700;
            display:flex; align-items:center; justify-content:center; border:1.5px solid #fff; line-height:1;
        }
        .customers-page .icon-btn.block{color:var(--red); border-color:var(--red);}
        .customers-page .icon-btn.blocked{background:var(--red); color:#fff; border-color:var(--red);}
        .customers-page .icon-btn.blocked:hover{background:var(--red-dark); border-color:var(--red-dark);}
        .customers-page .icon-btn.restore:hover{color:var(--green); border-color:var(--green);}
        .customers-page .inline{display:inline;}

        .customers-page .table-foot{
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
            padding:14px 20px;
        }
        .customers-page .table-foot .count{font-size:14px; color:var(--ink-soft);}
        .customers-page .pagination{display:flex; gap:6px;}

        .customers-page .empty-state{
            display:none; flex-direction:column; align-items:center; justify-content:center; text-align:center;
            padding:60px 20px; color:var(--ink-soft);
        }
        .customers-page .empty-state svg{color:#D8DCE1; margin-bottom:12px;}
        .customers-page .empty-state p{font-size:14px; margin:0;}
    </style>

    <div class="customers-page">
        <div class="topbar">
            <div>
                <h1>Customers</h1>
                <div class="sub">View and manage everyone who's shopped with you</div>
            </div>
            <div class="topbar-actions">
                <a href="{{ route('admin.customers.export') }}" class="btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
                    Export
                </a>
            </div>
        </div>

        <div class="mt-6 mb-4">
            <div class="view-tabs">
                <a href="{{ route('admin.customers.index', ['view' => 'all']) }}" class="{{ $view === 'all' ? 'active' : '' }}">All</a>
                <a href="{{ route('admin.customers.index', ['view' => 'ordering']) }}" class="{{ $view === 'ordering' ? 'active' : '' }}">With Orders</a>
                <a href="{{ route('admin.customers.index', ['view' => 'chat']) }}" class="{{ $view === 'chat' ? 'active' : '' }}">Live Chat</a>
            </div>
        </div>

        <div class="mt-6 mb-4">
            <div class="stats-row" id="statsRow">
                <div class="stat-card active" data-tag="all">
                    <div><div class="stat-label">All Customers</div><div class="stat-value" id="countAll">{{ $stats['all'] }}</div></div>
                    <div class="stat-icon all">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/><circle cx="18" cy="8" r="2.5"/><path d="M16 14.2c2.7.4 5 2.4 5 5.8"/></svg>
                    </div>
                </div>
                @if ($view === 'chat' || $view === 'all')
                <div class="stat-card" data-tag="chat">
                    <div><div class="stat-label">Live chat</div><div class="stat-value" id="countChat">{{ $stats['chat'] }}</div></div>
                    <div class="stat-icon chat">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                </div>
                @endif
                <div class="stat-card" data-tag="new">
                    <div><div class="stat-label">New this month</div><div class="stat-value" id="countNew">{{ $stats['new'] }}</div></div>
                    <div class="stat-icon new">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                    </div>
                </div>
                <div class="stat-card" data-tag="repeat">
                    <div><div class="stat-label">Repeat buyers</div><div class="stat-value" id="countRepeat">{{ $stats['repeat'] }}</div></div>
                    <div class="stat-icon repeat">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-3.5-7.1"/><path d="M21 4v5h-5"/></svg>
                    </div>
                </div>
                <div class="stat-card" data-tag="vip">
                    <div><div class="stat-label">VIP customers</div><div class="stat-value" id="countVip">{{ $stats['vip'] }}</div></div>
                    <div class="stat-icon vip">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                </div>
                <div class="stat-card" data-tag="flagged">
                    <div><div class="stat-label">Flagged</div><div class="stat-value" id="countFlagged">{{ $stats['flagged'] }}</div></div>
                    <div class="stat-icon flagged">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
                    </div>
                </div>
                <div class="stat-card" data-tag="blocked">
                    <div><div class="stat-label">Blocked</div><div class="stat-value" id="countBlocked">{{ $stats['blocked'] }}</div></div>
                    <div class="stat-icon blocked">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M5.5 5.5l13 13"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="filter-bar">
                <div class="search-wrap">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" id="searchInput" placeholder="Search by name, phone or email...">
                </div>
                <select class="filter-select" id="cityFilter">
                    <option value="all">All cities</option>
                    @foreach ($cities as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
                <select class="filter-select" id="districtFilter">
                    <option value="all">All districts</option>
                    @foreach ($districts as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
                <select class="filter-select" id="thanaFilter">
                    <option value="all">All upazilas</option>
                    @foreach ($thanas as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                <input type="date" class="filter-date" id="dateFrom" title="Last order from">
                <input type="date" class="filter-date" id="dateTo" title="Last order to">
                <select class="filter-select" id="sortFilter">
                    <option value="recent">Most recent order</option>
                    <option value="orders">Most orders</option>
                    <option value="spend">Highest spend</option>
                    <option value="newest">Newest customer</option>
                </select>
                <button class="filter-clear" id="clearFilter">Clear</button>
            </div>

            <table class="customers">
                <thead>
                <tr>
                    <th>Customer</th>
                    <th>Location</th>
                    <th class="num">Orders</th>
                    <th class="num">Total Spent</th>
                    <th>Last Order</th>
                    <th>Tag</th>
                    <th class="num">Actions</th>
                </tr>
                </thead>
                <tbody id="customersBody">
                @forelse ($customers as $customer)
                    <tr data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}" data-email="{{ $customer->email }}" data-city="{{ $customer->city ?? '' }}" data-district="{{ $customer->district ?? '' }}" data-thana="{{ $customer->thana ?? '' }}" data-tag="{{ $customer->tag }}" data-last-order="{{ $customer->last_order_iso ?? '' }}" data-blocked="{{ in_array($customer->phone, $blockedPhones) ? '1' : '0' }}">
                        <td>
                            <div class="cust-cell">
                                <div class="cust-avatar" style="background:{{ $avatarColors[$customer->id % 7] }}">{{ $initialsHelper($customer->name) }}</div>
                                <div>
                                    <div class="cust-name">{{ $customer->name ?? 'Guest' }}</div>
                                    <div class="cust-email">{{ $customer->email ?? 'N/A' }} · <span class="mono">{{ $customer->phone }}</span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="location-cell">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $customer->city ? $customer->city.', ' : '' }}{{ $customer->district ?? 'N/A' }}{{ $customer->thana ? ', '.$customer->thana : '' }}
                            </div>
                        </td>
                        <td class="num row-orders">
                            {{ $customer->total_orders }}
                            @if ($customer->cancelled_orders > 0)
                                <div class="cancel-note">{{ $customer->cancelled_orders }} cancelled</div>
                            @endif
                        </td>
                        <td class="num row-spent mono">৳{{ number_format($customer->total_spent ?? 0) }}</td>
                        <td class="last-order">
                            <div class="date-main">{{ $customer->last_order_date ?? 'N/A' }}</div>
                            @if ($customer->last_order_time)
                                <div class="date-sub">{{ $customer->last_order_time }}</div>
                            @endif
                        </td>
                        <td><span class="tag {{ $customer->tag }}">{{ $customer->tag }}</span></td>
                        <td class="num">
                            <div class="action-cell">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="icon-btn" title="View profile">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ \App\Helpers\PhoneHelper::whatsappLink($customer->phone, $storeSettings['timezone'] ?? null) }}" target="_blank" class="icon-btn msg" title="{{ $customer->unread_message_count > 0 ? $customer->unread_message_count.' unread message(s) from customer' : 'Message on WhatsApp' }}">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                    @if ($customer->unread_message_count > 0)
                                        <span class="msg-badge">{{ $customer->unread_message_count > 99 ? '99+' : $customer->unread_message_count }}</span>
                                    @endif
                                </a>
                                @if (in_array($customer->phone, $blockedPhones))
                                    <form action="{{ route('admin.customers.restore', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Unblock {{ $customer->name }}?')">
                                        @csrf
                                        <button class="icon-btn blocked" title="Unblock customer">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M5.5 5.5l13 13"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.customers.block', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Block {{ $customer->name }}? Their phone and latest IP will be blocked.')">
                                        @csrf
                                        <button class="icon-btn block" title="Block customer">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M5.5 5.5l13 13"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="emptyRow">
                        <td colspan="7">
                            <div class="empty-state" style="display:flex">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                                <p>No customers match your filters</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="table-foot">
                <div class="count" id="resultCount">Showing {{ $customers->total() }} customers</div>
                <div class="pagination">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const avatarColors = ['#A31E22','#6C4EE8','#0891B2','#12B76A','#F5A623','#3B82F6','#64748B'];
            const initials = (name) => name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();

            const customersBody = document.getElementById('customersBody');
            const emptyRow = document.getElementById('emptyRow');
            const searchInput = document.getElementById('searchInput');
            const cityFilter = document.getElementById('cityFilter');
            const districtFilter = document.getElementById('districtFilter');
            const thanaFilter = document.getElementById('thanaFilter');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const sortFilter = document.getElementById('sortFilter');
            const clearFilter = document.getElementById('clearFilter');
            const statsRow = document.getElementById('statsRow');

            let activeTag = 'all';

            function render() {
                const q = searchInput.value.trim().toLowerCase();
                const cityVal = cityFilter.value;
                const districtVal = districtFilter.value;
                const thanaVal = thanaFilter.value;
                const sortVal = sortFilter.value;

                const rows = customersBody.querySelectorAll('tr');
                let visible = 0;

                rows.forEach(row => {
                    const name = (row.dataset.name || '').toLowerCase();
                    const phone = (row.dataset.phone || '').toLowerCase();
                    const email = (row.dataset.email || '').toLowerCase();
                    const city = row.dataset.city || 'all';
                    const district = row.dataset.district || 'all';
                    const thana = row.dataset.thana || 'all';
                    const tag = row.dataset.tag || 'all';

                    const matchTag = activeTag === 'all' || (activeTag === 'blocked' ? row.dataset.blocked === '1' : tag === activeTag);
                    const matchCity = cityVal === 'all' || city === cityVal;
                    const matchDistrict = districtVal === 'all' || district === districtVal;
                    const matchThana = thanaVal === 'all' || thana === thanaVal;
                    const matchSearch = !q || name.includes(q) || phone.includes(q) || email.includes(q);

                    const lo = row.dataset.lastOrder || '';
                    const df = dateFrom.value;
                    const dt = dateTo.value;
                    let matchDate = true;
                    if (df && lo < df) matchDate = false;
                    if (dt && lo > dt) matchDate = false;

                    if (matchTag && matchCity && matchDistrict && matchThana && matchSearch && matchDate) {
                        row.style.display = '';
                        visible++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (visible === 0) {
                    const empty = customersBody.querySelector('.empty-state');
                    if (empty) empty.style.display = 'flex';
                }
            }

            statsRow.addEventListener('click', (e) => {
                const card = e.target.closest('.stat-card');
                if (!card) return;
                statsRow.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                activeTag = card.dataset.tag;
                render();
            });

            if (searchInput) searchInput.addEventListener('input', render);
            if (cityFilter) cityFilter.addEventListener('change', render);
            if (districtFilter) districtFilter.addEventListener('change', render);
            if (thanaFilter) thanaFilter.addEventListener('change', render);
            if (sortFilter) sortFilter.addEventListener('change', render);
            if (dateFrom) dateFrom.addEventListener('change', render);
            if (dateTo) dateTo.addEventListener('change', render);

            if (clearFilter) clearFilter.addEventListener('click', () => {
                searchInput.value = '';
                cityFilter.value = 'all';
                districtFilter.value = 'all';
                thanaFilter.value = 'all';
                if (dateFrom) dateFrom.value = '';
                if (dateTo) dateTo.value = '';
                sortFilter.value = 'recent';
                statsRow.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active'));
                activeTag = 'all';
                render();
            });
        </script>
    @endpush
</x-admin-layout>
