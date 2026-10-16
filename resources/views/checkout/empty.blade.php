<x-app-layout>
    @php
        $checkoutStoreName = $storeSettings['store_name'] ?? config('app.name', 'Store');
        $checkoutStoreLogo = $storeSettings['store_logo'] ?? null;
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
      :root{
        --paper:#FAF6EE;
        --card:#FFFFFF;
        --ink:#1F2420;
        --ink-soft:#5B6058;
        --green:#0B4F3C;
        --green-dark:#083D2F;
        --green-tint:#E7F0EB;
        --gold:#C89B3C;
        --border:#E4DFD3;
        --border-soft:#EDE9DD;
        --radius:16px;
        --shadow: 0 1px 2px rgba(31,36,32,0.04), 0 12px 32px -16px rgba(31,36,32,0.18);
      }
      #checkout-empty{
        background:var(--paper);
        color:var(--ink);
        font-family:'Inter', sans-serif;
        -webkit-font-smoothing:antialiased;
        margin-left:calc(50% - 50vw);
        margin-right:calc(50% - 50vw);
        margin-top:-1.5rem;
        margin-bottom:-1.5rem;
        padding-bottom:1.5rem;
        min-height:100vh;
      }
      @media (min-width:640px){
        #checkout-empty{
          margin-top:-2rem;
          margin-bottom:-2rem;
          padding-bottom:2rem;
          min-height:calc(100vh - 1rem);
        }
      }
      @media (min-width:1024px){
        #checkout-empty{
          margin-top:-2.5rem;
          margin-bottom:-2.5rem;
          padding-bottom:2.5rem;
          min-height:calc(100vh - 2rem);
        }
      }
      #checkout-empty h1, #checkout-empty h2, #checkout-empty h3{
        font-family:'Fraunces', serif;
        font-weight:600;
        margin:0;
        letter-spacing:-0.01em;
      }

      #checkout-empty .empty-wrap{
        max-width:1400px; margin:0 auto; padding:72px 40px 96px;
        display:flex; justify-content:center; align-items:center; min-height:60vh;
      }
      #checkout-empty .empty-card{
        background:var(--card);
        border:1px solid var(--border);
        border-radius:var(--radius);
        box-shadow:var(--shadow);
        padding:64px 56px 56px;
        max-width:520px;
        width:100%;
        text-align:center;
        position:relative;
        overflow:hidden;
      }
      #checkout-empty .empty-card::before{
        content:"";
        position:absolute;
        top:0; left:0; right:0;
        height:5px;
        background:linear-gradient(90deg, var(--green), #1a8a5f 50%, var(--gold));
      }
      #checkout-empty .icon-wrap{
        width:96px; height:96px;
        margin:0 auto 28px;
        border-radius:50%;
        background:var(--green-tint);
        display:flex; align-items:center; justify-content:center;
        position:relative;
      }
      #checkout-empty .icon-wrap::after{
        content:"";
        position:absolute;
        inset:-10px;
        border-radius:50%;
        border:1.5px dashed rgba(11,79,60,0.18);
      }
      #checkout-empty .icon-wrap svg{
        width:40px; height:40px; color:var(--green);
      }
      #checkout-empty .empty-card .eyebrow{
        font-size:11.5px;
        font-weight:700;
        letter-spacing:0.14em;
        text-transform:uppercase;
        color:var(--gold);
        margin-bottom:12px;
      }
      #checkout-empty .empty-card h1{
        font-size:30px; margin:0 0 12px; color:var(--ink);
      }
      #checkout-empty .empty-card .lead{
        font-size:15px; color:var(--ink-soft); margin:0 auto 36px; line-height:1.7;
        max-width:360px;
      }
      #checkout-empty .actions{
        display:flex; flex-direction:column; gap:12px; align-items:center;
      }
      #checkout-empty .go-cart{
        display:inline-flex; align-items:center; justify-content:center; gap:10px;
        background:var(--green); color:#fff;
        border:none; border-radius:12px;
        padding:16px 40px; font-size:15px; font-weight:600;
        text-decoration:none; cursor:pointer;
        box-shadow:0 8px 20px -8px rgba(11,79,60,0.5);
        transition:background .15s ease, transform .15s ease;
        width:100%;
      }
      #checkout-empty .go-cart:hover{background:var(--green-dark); transform:translateY(-1px);}
      #checkout-empty .go-cart svg{width:17px;height:17px;}
      #checkout-empty .go-home{
        display:inline-flex; align-items:center; gap:8px;
        font-size:14px; font-weight:600; color:var(--ink-soft);
        text-decoration:none; padding:6px 10px;
        border-radius:8px;
        transition:color .15s ease;
      }
      #checkout-empty .go-home:hover{color:var(--green);}
      #checkout-empty .go-home svg{width:16px;height:16px;}
      #checkout-empty .divider{
        height:1px; background:var(--border-soft);
        margin:36px 0 28px;
        position:relative;
      }
      #checkout-empty .trust-row{
        display:flex; justify-content:center; gap:28px; flex-wrap:wrap;
      }
      #checkout-empty .trust-item{
        display:flex; align-items:center; gap:8px;
        font-size:12.5px; font-weight:600; color:var(--ink-soft);
      }
      #checkout-empty .trust-item svg{
        width:16px; height:16px; color:var(--green); flex-shrink:0;
      }
    </style>

    <div id="checkout-empty">
      <div class="empty-wrap">
        <div class="empty-card">
          <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
              <path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.6a2 2 0 002-1.6L23 6H6"/>
            </svg>
          </div>
          <p class="eyebrow">Shopping Cart</p>
          <h1>Your cart is empty</h1>
          <p class="lead">Looks like you haven't added anything yet. Explore our collection and find something you'll love.</p>

          <div class="actions">
            <a href="{{ route('home') }}" class="go-cart">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4H6z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 01-8 0"/></svg>
              Start Shopping
            </a>
            <a href="{{ route('cart.loading') }}" class="go-home">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
              Go to your cart
            </a>
          </div>

          <div class="divider"></div>

          <div class="trust-row">
            <div class="trust-item">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
              Secure checkout
            </div>
            <div class="trust-item">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
              Fast delivery
            </div>
            <div class="trust-item">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 019-9 9.75 9.75 0 016.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 01-9 9 9.75 9.75 0 01-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
              Easy returns
            </div>
          </div>
        </div>
      </div>
    </div>
</x-app-layout>
