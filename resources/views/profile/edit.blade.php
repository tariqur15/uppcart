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
    .d-panel{background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:20px 24px;}
    .d-panel-head{display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;}
    .d-panel-head h2{font-size:16.5px; font-weight:700; margin:0; font-family:'Inter',sans-serif;}

    .p-hint{font-size:12.5px; color:var(--ink-soft); line-height:1.6; margin:-6px 0 18px;}
    .p-form{display:flex; flex-direction:column; gap:16px;}
    .p-field-group{display:flex; flex-direction:column; gap:6px;}
    .p-label{font-size:12.5px; font-weight:600; color:#4A5058; font-family:'Inter',sans-serif;}
    .p-field{width:100%; height:42px; border:1px solid var(--border); border-radius:10px; padding:0 13px; font-family:'Inter',sans-serif; font-size:13.5px; color:var(--ink); background:#fff; outline:none; transition:border-color .15s ease, box-shadow .15s ease;}
    .p-field::placeholder{color:#B8BEC5;}
    .p-field:focus{border-color:var(--red); box-shadow:0 0 0 3px rgba(163,30,34,0.08);}
    .p-error{list-style:none; padding:0; margin:0; font-size:12px; color:#DC2626;}
    .p-success{color:var(--green); font-size:12.5px; margin-top:6px;}
    .p-link{border:none; background:none; color:var(--red); text-decoration:underline; cursor:pointer; font-size:12.5px; padding:0; font-family:'Inter',sans-serif;}
    .p-actions{display:flex; align-items:center; gap:14px; margin-top:4px;}
    .p-btn{height:42px; padding:0 26px; border:none; border-radius:10px; background:var(--red); color:#fff; font-family:'Inter',sans-serif; font-size:13.5px; font-weight:600; cursor:pointer; transition:background .15s ease, transform .15s ease;}
    .p-btn:hover{background:var(--red-dark);}
    .p-btn:active{transform:scale(0.98);}
    .p-saved{color:var(--green); font-size:13px; font-weight:500;}
    @media (max-width:980px){
        .d-shell{grid-template-columns:1fr;}
        .d-sidebar{position:static;}
    }
</style>

<x-app-layout>
    <div style="background:var(--bg); margin:-2rem -1.5rem; padding:20px;">
        <div class="d-shell">
            {{-- SIDEBAR --}}
            <aside class="d-sidebar">
                <div class="d-sidebar-title">Dashboard</div>
                <nav class="d-nav-group">
                    <a href="{{ route('dashboard') }}" class="d-nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                        Home
                    </a>
                    <a href="{{ route('profile.edit') }}" class="d-nav-item active">
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
                    <h1>Your Profile</h1>
                    <p>Manage your account information, password and security</p>
                </div>

                <div class="d-panel">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="d-panel">
                    @include('profile.partials.update-password-form')
                </div>
</div>
        </div>
    </div>
</x-app-layout>