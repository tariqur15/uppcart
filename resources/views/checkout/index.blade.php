<x-app-layout>
    @php
        $checkoutStoreName = $storeSettings['store_name'] ?? config('app.name', 'Store');
        $checkoutStoreLogo = $storeSettings['store_logo'] ?? null;
        $freeShippingThreshold = (float) ($storeSettings['free_shipping_threshold'] ?? 0);
        $freeShippingWeightThreshold = (float) ($storeSettings['free_shipping_weight_threshold'] ?? 0);
        $isDigitalOnly = $items->every(fn ($item) => ($item->product ?? \App\Models\Product::find($item->product_id))?->type === 'digital');

        $enabledDelivery = [];
        if (($storeSettings['delivery_inside_dhaka'] ?? '1') === '1') {
            $enabledDelivery[] = [
                'id' => 'inside_dhaka',
                'name' => $storeSettings['delivery_inside_dhaka_name'] ?? 'Inside Dhaka',
                'desc' => $storeSettings['delivery_inside_dhaka_desc'] ?? 'Arrives within 1–2 business days',
                'price' => (float) ($storeSettings['delivery_inside_dhaka_charge'] ?? 60),
            ];
        }
        if (($storeSettings['delivery_outside_dhaka'] ?? '1') === '1') {
            $enabledDelivery[] = [
                'id' => 'outside_dhaka',
                'name' => $storeSettings['delivery_outside_dhaka_name'] ?? 'Outside Dhaka',
                'desc' => $storeSettings['delivery_outside_dhaka_desc'] ?? 'Arrives within 3–5 business days',
                'price' => (float) ($storeSettings['delivery_outside_dhaka_charge'] ?? 120),
            ];
        }
        if (($storeSettings['delivery_express'] ?? '1') === '1') {
            $enabledDelivery[] = [
                'id' => 'express',
                'name' => $storeSettings['delivery_express_name'] ?? 'Express delivery',
                'desc' => $storeSettings['delivery_express_desc'] ?? 'Within 24 hours (inside Dhaka only)',
                'price' => (float) ($storeSettings['delivery_express_charge'] ?? 150),
            ];
        }

        $enabledPayments = [];
        if (($storeSettings['payment_bkash_enabled'] ?? '1') === '1') {
            $enabledPayments[] = ['id' => 'bkash', 'badge' => 'bKash', 'name' => 'bKash', 'sub' => $storeSettings['payment_bkash_description'] ?? 'Send Money / Payment', 'css' => 'bkash', 'fullWidth' => false];
        }
        if (($storeSettings['payment_nagad_enabled'] ?? '1') === '1') {
            $enabledPayments[] = ['id' => 'nagad', 'badge' => 'Nagad', 'name' => 'Nagad', 'sub' => $storeSettings['payment_nagad_description'] ?? 'Instant Pay', 'css' => 'nagad', 'fullWidth' => false];
        }
        if (($storeSettings['payment_rocket_enabled'] ?? '1') === '1') {
            $enabledPayments[] = ['id' => 'rocket', 'badge' => 'Rkt', 'name' => 'Rocket', 'sub' => $storeSettings['payment_rocket_description'] ?? 'Dutch-Bangla', 'css' => 'rocket', 'fullWidth' => false];
        }
        if (($storeSettings['payment_card_enabled'] ?? '1') === '1') {
            $enabledPayments[] = ['id' => 'card', 'badge' => 'VISA', 'name' => 'Card', 'sub' => $storeSettings['payment_card_description'] ?? 'Visa / Mastercard', 'css' => 'card-pay', 'fullWidth' => false];
        }
        if (($storeSettings['payment_cod_enabled'] ?? '1') === '1') {
            $enabledPayments[] = ['id' => 'cod', 'badge' => 'COD', 'name' => 'Cash on Delivery', 'sub' => 'Pay when you receive the product', 'css' => 'cod', 'fullWidth' => true];
        }
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
      :root{
        --paper:#FAF6EE;
        --card:#FFFFFF;
        --ink:#1F2420;
        --ink-soft:#5B6058;
        --green:#0B4F3C;
        --green-dark:#083D2F;
        --green-tint:#E7F0EB;
        --red:#E4404A;
        --red-tint:#FCE9EA;
        --gold:#C89B3C;
        --border:#E4DFD3;
        --border-soft:#EDE9DD;
        --radius:14px;
        --shadow: 0 1px 2px rgba(31,36,32,0.04), 0 8px 24px -12px rgba(31,36,32,0.12);
      }
      #checkout-page{
        background:var(--paper);
        color:var(--ink);
        font-family:'Inter', sans-serif;
        -webkit-font-smoothing:antialiased;
        border-radius:16px;
        margin-left:calc(50% - 50vw);
        margin-right:calc(50% - 50vw);
        margin-top:-1.5rem;
        margin-bottom:-1.5rem;
        padding-bottom:1.5rem;
        min-height:100vh;
      }
      @media (min-width:640px){
        #checkout-page{
          margin-top:-2rem;
          margin-bottom:-2rem;
          padding-bottom:2rem;
          min-height:calc(100vh - 1rem);
        }
      }
      @media (min-width:1024px){
        #checkout-page{
          margin-top:-2.5rem;
          margin-bottom:-2.5rem;
          padding-bottom:2.5rem;
          min-height:calc(100vh - 2rem);
        }
      }
      #checkout-page h1, #checkout-page h2, #checkout-page h3{
        font-family:'Fraunces', serif;
        font-weight:600;
        margin:0;
        letter-spacing:-0.01em;
      }
      #checkout-page .mono{font-family:'JetBrains Mono', monospace;}

      /* ---------- Steps ---------- */
      #checkout-page .steps-wrap{
        max-width:1400px; margin:0 auto; padding:26px 40px 0;
      }
      #checkout-page .steps{
        display:flex; align-items:center; justify-content:center; gap:0;
        max-width:520px; margin:0 auto;
      }
      #checkout-page .step{
        display:flex; align-items:center; gap:10px; color:var(--ink-soft); font-size:14px;
      }
      #checkout-page .step .num{
        width:28px;height:28px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;
        background:var(--card); border:1.5px solid var(--border);
        font-family:'Fraunces',serif; font-size:14px; font-weight:600; color:var(--ink-soft);
      }
      #checkout-page .step.done .num{background:var(--green); border-color:var(--green); color:#fff;}
      #checkout-page .step.active .num{background:var(--green); border-color:var(--green); color:#fff;}
      #checkout-page .step.active span{color:var(--ink); font-weight:600;}
      #checkout-page .stitch-connector{
        flex:1; height:2px; margin:0 10px;
        background-image: radial-gradient(circle, var(--gold) 1.4px, transparent 1.6px);
        background-size: 8px 2px;
        background-repeat: repeat-x;
        background-position: center;
        align-self:center;
      }

      /* ---------- Layout ---------- */
      #checkout-page .layout{
        max-width:1400px; margin:0 auto; padding:28px 40px 80px;
        display:grid; grid-template-columns:1fr 440px; gap:32px;
        align-items:start;
      }
      #checkout-page .card{
        background:var(--card);
        border:1px solid var(--border);
        border-radius:var(--radius);
        box-shadow:var(--shadow);
        padding:28px 30px;
        margin-bottom:22px;
      }
      #checkout-page .card-head{
        display:flex; align-items:baseline; justify-content:space-between; margin-bottom:20px;
      }
      #checkout-page .card-head h2{font-size:21px;}
      #checkout-page .card-head .eyebrow{
        font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:var(--gold); font-weight:600;
      }

      #checkout-page label{
        display:block; font-size:13px; color:var(--ink-soft); margin-bottom:6px; font-weight:500;
      }
      #checkout-page .field{margin-bottom:16px;}
      #checkout-page .row2{display:grid; grid-template-columns:1fr 1fr; gap:14px;}
      #checkout-page .row3{display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;}

      #checkout-page input[type=text], #checkout-page input[type=tel], #checkout-page input[type=email], #checkout-page select, #checkout-page textarea{
        width:100%;
        font-family:'Inter', sans-serif;
        font-size:15px;
        padding:12px 14px;
        border-radius:10px;
        border:1.5px solid var(--border);
        background:#FDFCFA;
        color:var(--ink);
        outline:none;
        transition:border-color .15s ease, background .15s ease;
      }
      #checkout-page textarea{resize:vertical; min-height:64px; font-family:inherit;}
      #checkout-page input:focus, #checkout-page select:focus, #checkout-page textarea:focus{
        border-color:var(--green);
        background:#fff;
      }
      #checkout-page input::placeholder{color:#B7B2A3;}
      #checkout-page .phone-field{position:relative;}
      #checkout-page .phone-prefix{
        position:absolute; left:13px; top:50%; transform:translateY(-50%);
        color:var(--ink-soft); font-size:14.5px; font-family:'JetBrains Mono',monospace;
        pointer-events:none;
      }
      #checkout-page .phone-field input{padding-left:44px;}

      #checkout-page .helper{font-size:12px; color:var(--ink-soft); margin-top:5px;}
      #checkout-page .field-error{font-size:12px; color:var(--red); margin-top:4px;}

      /* address type chips */
      #checkout-page .chip-row{display:flex; gap:10px; margin-bottom:18px;}
      #checkout-page .chip{
        padding:8px 16px; border-radius:999px; border:1.5px solid var(--border);
        font-size:13px; color:var(--ink-soft); cursor:pointer; background:#fff;
        transition:all .15s ease; user-select:none;
      }
      #checkout-page .chip:hover{border-color:var(--green); color:var(--green);}
      #checkout-page .chip.selected{background:var(--green-tint); border-color:var(--green); color:var(--green-dark); font-weight:600;}

      /* ---------- Delivery options ---------- */
      #checkout-page .option-list{display:flex; flex-direction:column; gap:10px;}
      #checkout-page .option{
        display:flex; align-items:center; justify-content:space-between;
        border:1.5px solid var(--border); border-radius:11px; padding:13px 16px;
        cursor:pointer; transition:border-color .15s ease, background .15s ease;
      }
      #checkout-page .option:hover{border-color:var(--green);}
      #checkout-page .option.selected{border-color:var(--green); background:var(--green-tint);}
      #checkout-page .option-left{display:flex; align-items:center; gap:12px;}
      #checkout-page .radio-dot{
        width:18px;height:18px;border-radius:50%; border:1.5px solid var(--border);
        flex-shrink:0; position:relative; background:#fff;
      }
      #checkout-page .option.selected .radio-dot{border-color:var(--green);}
      #checkout-page .option.selected .radio-dot::after{
        content:''; position:absolute; inset:3px; border-radius:50%; background:var(--green);
      }
      #checkout-page .option-title{font-size:14.5px; font-weight:600;}
      #checkout-page .option-sub{font-size:12.5px; color:var(--ink-soft); margin-top:2px;}
      #checkout-page .option-price{font-family:'JetBrains Mono',monospace; font-size:14px; font-weight:500;}

      /* ---------- Payment methods ---------- */
      #checkout-page .pay-grid{display:grid; grid-template-columns:1fr 1fr; gap:10px;}
      #checkout-page .pay-card{
        border:1.5px solid var(--border); border-radius:11px; padding:13px 14px;
        display:flex; align-items:center; gap:10px; cursor:pointer;
        transition:border-color .15s ease, background .15s ease;
        background:#fff;
      }
      #checkout-page .pay-card:hover{border-color:var(--green);}
      #checkout-page .pay-card.selected{border-color:var(--green); background:var(--green-tint);}
      #checkout-page .pay-badge{
        width:38px; height:26px; border-radius:6px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-size:10.5px; font-weight:700; color:#fff; letter-spacing:0.02em;
      }
      #checkout-page .pay-name{font-size:13.5px; font-weight:600;}
      #checkout-page .pay-sub{font-size:11px; color:var(--ink-soft);}
      #checkout-page .bkash{background:#E2136E;}
      #checkout-page .nagad{background:#F6921E;}
      #checkout-page .rocket{background:#8C3494;}
      #checkout-page .cod{background:var(--green);}
      #checkout-page .card-pay{background:#2A2E43;}
      #checkout-page .cod-note{
        margin-top:12px; padding:11px 13px; background:var(--red-tint);
        border:1px dashed var(--red); border-radius:9px; font-size:12.5px; color:#8E2A31; display:none;
      }

      /* ---------- Stitch divider ---------- */
      #checkout-page .stitch-hr{
        height:2px; margin:22px 0;
        background-image: radial-gradient(circle, var(--gold) 1.4px, transparent 1.6px);
        background-size: 9px 2px; background-repeat:repeat-x; background-position:center;
      }

      /* ---------- Summary ---------- */
      #checkout-page .summary{position:sticky; top:20px;}
      #checkout-page .summary .card{padding:0; overflow:hidden;}
      #checkout-page .summary-top{padding:24px 26px 8px;}
      #checkout-page .summary-top h2{font-size:21px; margin-bottom:2px;}
      #checkout-page .summary-top .count{font-size:12.5px; color:var(--ink-soft);}

      #checkout-page .item-list{padding:8px 26px;}
      #checkout-page .item{
        display:flex; gap:14px; padding:16px 0; border-bottom:1px solid var(--border-soft);
      }
      #checkout-page .item:last-child{border-bottom:none;}
      #checkout-page .thumb{
        width:64px; height:64px; border-radius:10px; flex-shrink:0;
        background:linear-gradient(135deg, var(--green-tint), #fff);
        border:1px solid var(--border-soft);
        display:flex; align-items:center; justify-content:center;
        font-family:'Fraunces',serif; color:var(--green); font-weight:600; font-size:20px;
        overflow:hidden;
      }
      #checkout-page .thumb img{width:100%;height:100%;object-fit:cover;}
      #checkout-page .item-info{flex:1; min-width:0;}
      #checkout-page .item-name{font-size:14.5px; font-weight:600; line-height:1.35;}
      #checkout-page .item-variant{font-size:12.5px; color:var(--ink-soft); margin-top:3px;}
      #checkout-page .item-bottom{display:flex; align-items:center; justify-content:space-between; margin-top:10px;}
      #checkout-page .qty-stepper{
        display:flex; align-items:center; border:1px solid var(--border); border-radius:8px; overflow:hidden;
      }
      #checkout-page .qty-stepper button{
        width:26px; height:26px; border:none; background:#F7F4EE; cursor:pointer;
        font-size:14px; color:var(--ink); display:flex; align-items:center; justify-content:center;
      }
      #checkout-page .qty-stepper button:hover{background:var(--green-tint);}
      #checkout-page .qty-val{width:28px; text-align:center; font-size:13.5px; font-family:'JetBrains Mono',monospace;}
      #checkout-page .item-price{font-family:'JetBrains Mono',monospace; font-size:14.5px; font-weight:600;}

      #checkout-page .promo-row{
        display:flex; gap:8px; padding:16px 26px; border-top:1px solid var(--border-soft);
      }
      #checkout-page .promo-row input{
        flex:1; padding:9px 12px; border:1.5px solid var(--border); border-radius:9px;
        font-size:13px; font-family:'Inter',sans-serif; outline:none;
        background:var(--card); color:var(--ink);
      }
      #checkout-page .promo-row input:focus{border-color:var(--green);}
      #checkout-page .promo-btn{
        padding:0 16px; border-radius:9px; border:1.5px solid var(--green); background:#fff;
        color:var(--green); font-weight:600; font-size:13px; cursor:pointer; white-space:nowrap;
        font-family:'Inter',sans-serif;
      }
      #checkout-page .promo-btn:hover{background:var(--green-tint);}
      #checkout-page .promo-msg{font-size:12px; padding:0 22px; margin-top:-8px; margin-bottom:8px; display:none;}

      #checkout-page .totals{padding:14px 26px 22px; border-top:1px solid var(--border-soft);}
      #checkout-page .total-row{
        display:flex; justify-content:space-between; font-size:13.5px; color:var(--ink-soft); padding:5px 0;
      }
      #checkout-page .total-row .tv{font-family:'JetBrains Mono',monospace;}
      #checkout-page .total-row.discount span:last-child{color:var(--green);}
      #checkout-page .total-row.grand{
        border-top:1px dashed var(--border); margin-top:8px; padding-top:12px;
        font-size:17px; color:var(--ink); font-weight:600;
      }
      #checkout-page .total-row.grand span:last-child{font-family:'Fraunces',serif; color:var(--green-dark);}

      #checkout-page .place-order{
        margin:0 26px 22px;
        background:var(--green); color:#fff; border:none; border-radius:11px;
        padding:16px; width:calc(100% - 52px); font-size:16px; font-weight:600;
        font-family:'Inter',sans-serif; cursor:pointer;
        display:flex; align-items:center; justify-content:center; gap:8px;
        transition:background .15s ease;
      }
      #checkout-page .place-order:hover{background:var(--green-dark);}

      #checkout-page .trust-row{
        display:flex; justify-content:space-between; padding:0 26px 26px; gap:8px;
      }
      #checkout-page .trust-item{
        flex:1; text-align:center; font-size:11px; color:var(--ink-soft);
        display:flex; flex-direction:column; align-items:center; gap:6px;
      }
      #checkout-page .trust-item svg{color:var(--gold);}

      /* Free shipping progress bar (compact) */
      #checkout-page .fs-bar-slot{padding:4px 18px 0;}
      #checkout-page .shipping-progress{width:100%; overflow:visible; margin-bottom:4px;}
      #checkout-page .shipping-message{text-align:center; font-size:13px; color:#333; margin-bottom:10px;}
      #checkout-page .shipping-message strong{font-weight:700;}
      #checkout-page .progress-wrapper{position:relative; height:58px; margin-right:22px; overflow:visible;}
      #checkout-page .progress-bar{position:absolute; left:0; right:0; top:24px; height:8px; border-radius:16px; background:#e1e4ed; overflow:visible;}
      #checkout-page .progress-fill{position:relative; height:100%; border-radius:16px; background:linear-gradient(90deg,#16a900,#28b91d,#16a900); background-size:200% 100%; animation:greenFlow 1.2s linear infinite;}
      #checkout-page .progress-fill.blue{background:linear-gradient(90deg,#3B82F6,#60A5FA,#3B82F6); animation-name:blueFlow;}
      @keyframes greenFlow{from{background-position:0 0;} to{background-position:200% 0;}}
      @keyframes blueFlow{from{background-position:0 0;} to{background-position:200% 0;}}
      #checkout-page .van-container{
        position:absolute;
        bottom:25px;
        width:64px;
        height:32px;
        z-index:5;
        left:-14px;
        animation:vanDrive 4s ease-in-out infinite, vanVibration 0.10s linear infinite;
      }
      #checkout-page .van-container.van-parked{
        left:calc(100% - 86px);
        animation:none;
        z-index:15;
      }
      #checkout-page .van-container img{display:block; width:100%; height:100%; object-fit:contain; user-select:none; pointer-events:none;}
      @keyframes vanDrive{
        0%   {left:-64px; opacity:1;}
        8%   {left:-30px;}
        45%  {left:calc((var(--target-pct,50%) - 64px) * 0.55);}
        80%  {left:calc(var(--target-pct,50%) - 68px);}
        88%  {left:calc(var(--target-pct,50%) - 64px); opacity:1;}
        94%  {left:calc(var(--target-pct,50%) - 64px); opacity:0;}
        95%  {left:-64px; opacity:0;}
        100% {left:-64px; opacity:1;}
      }
      @keyframes vanVibration{
        0%   {transform:translateY(0) rotate(0deg);}
        20%  {transform:translateY(-1px) rotate(-0.4deg);}
        40%  {transform:translateY(1px) rotate(0.3deg);}
        60%  {transform:translateY(-1px) rotate(-0.3deg);}
        80%  {transform:translateY(1px) rotate(0.4deg);}
        100% {transform:translateY(0) rotate(0deg);}
      }
      #checkout-page .smoke{position:absolute; left:-20px; bottom:3px; width:40px; height:22px; pointer-events:none;}
      #checkout-page .smoke span{position:absolute; display:block; border-radius:50%; background:rgba(170,170,170,0.55); filter:blur(2px); animation:smokeMove 1.1s ease-out infinite;}
      #checkout-page .smoke span:nth-child(1){width:12px; height:12px; left:20px; bottom:2px;}
      #checkout-page .smoke span:nth-child(2){width:9px; height:9px; left:10px; bottom:5px; animation-delay:.25s;}
      #checkout-page .smoke span:nth-child(3){width:7px; height:7px; left:2px; bottom:8px; animation-delay:.45s;}
      @keyframes smokeMove{
        0%   {transform:scale(.6) translate(0,0); opacity:.65;}
        50%  {transform:scale(1.1) translate(-8px,-5px); opacity:.45;}
        100% {transform:scale(1.7) translate(-22px,-12px); opacity:0;}
      }
      #checkout-page .speed-line{position:absolute; left:-14px; height:2px; background:#aaa; border-radius:10px; animation:speedLine .45s linear infinite;}
      #checkout-page .speed-line:nth-child(1){top:10px; width:14px;}
      #checkout-page .speed-line:nth-child(2){top:16px; width:10px; animation-delay:.15s;}
      #checkout-page .speed-line:nth-child(3){top:22px; width:16px; animation-delay:.3s;}
      @keyframes speedLine{
        from{opacity:.8; transform:translateX(0);}
        to  {opacity:0; transform:translateX(-12px);}
      }
      #checkout-page .shipping-point{position:absolute; right:-20px; top:-4px; width:42px; height:42px; border-radius:50%; background:#fff; border:2px solid #d6d9e0; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(0,0,0,.08); z-index:10; flex-direction:column;}
      #checkout-page .shipping-point.reached{border-color:#16A34A;}
      #checkout-page .fs-bar-slot .shipping-point svg{width:20px; height:20px;}
      #checkout-page .shipping-label{position:absolute; top:41px; width:70px; text-align:center; font-size:11px; line-height:13px; color:#333; left:50%; transform:translateX(-50%);}

      @media (max-width:1024px){
        #checkout-page .layout{grid-template-columns:1fr; padding:20px 16px 60px;}
        #checkout-page .row2, #checkout-page .row3, #checkout-page .pay-grid{grid-template-columns:1fr;}
        #checkout-page .summary{position:static;}
        #checkout-page .steps-wrap{padding:20px 18px 0;}
      }
      [x-cloak]{display:none!important;}
    </style>

    <div id="checkout-page" x-data="checkout()" x-init="init()">
      <div class="steps-wrap">
        <div class="steps">
          <div class="step done"><div class="num">1</div><span>Details</span></div>
          <div class="stitch-connector"></div>
          <div class="step active"><div class="num">2</div><span>Address &amp; Payment</span></div>
          <div class="stitch-connector"></div>
          <div class="step"><div class="num">3</div><span>Done</span></div>
        </div>
      </div>

      <div class="layout">
        <div class="left">
          <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
            @csrf

            <div class="card">
              <div class="card-head">
                <h2>Contact information</h2>
                <span class="eyebrow">Step 1</span>
              </div>
              <div class="row2">
                <div class="field">
                  <label for="fname">Full name</label>
                  <input type="text" id="fname" name="first_name" placeholder="e.g. Rahat Hasan" value="{{ old('first_name', auth()->user()?->name ?? '') }}" required x-model="customerName" x-on:input.debounce.800ms="trackAbandon()">
                  @error('first_name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                  <label for="phone">Mobile number</label>
                  <div class="phone-field">
                    <span class="phone-prefix">+88</span>
                    <input type="tel" id="phone" name="phone" placeholder="01XXX-XXXXXX" maxlength="11" value="{{ old('phone') }}" required x-model="customerPhone" x-on:input.debounce.800ms="trackAbandon()">
                  </div>
                  @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                </div>
              </div>
              <div class="row2">
                <div class="field">
                  <label for="email">Email <span style="font-weight:400;">(optional)</span></label>
                  <input type="email" id="email" name="email" placeholder="you@example.com" value="{{ old('email', auth()->user()?->email ?? '') }}">
                </div>
              </div>
            </div>

            @if (! $isDigitalOnly)
            <div class="card">
              <div class="card-head">
                <h2>Delivery address</h2>
                <span class="eyebrow">Step 2</span>
              </div>
              <input type="hidden" name="address_type" :value="addressType">
              <div class="chip-row">
                <div class="chip" :class="addressType === 'home' ? 'selected' : ''" @click="addressType = 'home'">Home</div>
                <div class="chip" :class="addressType === 'office' ? 'selected' : ''" @click="addressType = 'office'">Office</div>
                <div class="chip" :class="addressType === 'other' ? 'selected' : ''" @click="addressType = 'other'">Other</div>
              </div>

              <div class="row3">
                <div class="field">
                  <label for="division">Division</label>
                  <select id="division" name="division" x-model="division" required>
                    <option value="">Select</option>
                    <template x-for="d in divisions" :key="d">
                      <option :value="d" x-text="d"></option>
                    </template>
                  </select>
                  @error('division') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                  <label for="district">District</label>
                  <select id="district" name="district" x-model="district" required :disabled="!division">
                    <option value="">Select division first</option>
                    <template x-for="d in districtList" :key="d">
                      <option :value="d" x-text="d"></option>
                    </template>
                  </select>
                  @error('district') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                  <label for="upazila">Thana / Upazila</label>
                  <select id="upazila" name="thana" x-model="upazila" :disabled="!district">
                    <option value="">Select district first</option>
                    <template x-for="u in upazilaList" :key="u">
                      <option :value="u" x-text="u"></option>
                    </template>
                  </select>
                </div>
              </div>

              <div class="field">
                <label for="address">Full address</label>
                <textarea id="address" name="full_address" placeholder="House no., road, area name..." required x-model="fullAddress">{{ old('full_address') }}</textarea>
                @error('full_address') <p class="field-error">{{ $message }}</p> @enderror
              </div>
              <div class="field" style="margin-bottom:0;">
                <label for="landmark">Landmark <span style="font-weight:400;">(optional)</span></label>
                <input type="text" id="landmark" name="landmark" placeholder="A nearby well-known place, e.g. next to Al-Madina Mosque" value="{{ old('landmark') }}" x-model="landmark" x-on:input.debounce.800ms="trackAbandon()">
              </div>
            </div>
            @endif

            @if (! $isDigitalOnly)
            <div class="card">
              <div class="card-head">
                <h2>Delivery option</h2>
              </div>
              <input type="hidden" name="delivery_option" :value="deliveryOption">
              <div class="option-list" x-show="deliveryOptions.length > 0">
                <template x-for="opt in deliveryOptions" :key="opt.id">
                  <div class="option" :class="deliveryOption === opt.id ? 'selected' : ''" @click="deliveryOption = opt.id">
                    <div class="option-left">
                      <div class="radio-dot"></div>
                      <div>
                        <div class="option-title" x-text="opt.title"></div>
                        <div class="option-sub" x-text="opt.description"></div>
                      </div>
                    </div>
                    <div class="option-price" x-text="methodPrice(opt)"></div>
                  </div>
                </template>
              </div>
              <div x-show="shippingZones.length > 0 && deliveryOptions.length === 0" style="color:#8a8f98; font-size:12.5px; padding:8px 0;">No delivery options are available for your selected location yet.</div>
              @error('delivery_option') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            @endif

            <div class="card">
              <div class="card-head">
                <h2>Payment method</h2>
              </div>
              <input type="hidden" name="payment_method" :value="paymentMethod">
              <input type="hidden" name="promo_code" :value="promoApplied ? promoCode : ''">
              <input type="hidden" name="discount_amount" :value="promoDiscount">
              <div class="pay-grid">
                @foreach ($enabledPayments as $pay)
                  <div class="pay-card" :class="paymentMethod === '{{ $pay['id'] }}' ? 'selected' : ''" @click="paymentMethod = '{{ $pay['id'] }}'" @if ($pay['fullWidth'] ?? false) style="grid-column:1 / -1;" @endif>
                    <div class="pay-badge {{ $pay['css'] }}">{{ $pay['badge'] }}</div>
                    <div><div class="pay-name">{{ $pay['name'] }}</div><div class="pay-sub">{{ $pay['sub'] }}</div></div>
                  </div>
                @endforeach
              </div>
              <div class="cod-note" :style="paymentMethod === 'cod' ? 'display:block' : ''">No advance payment needed — pay the delivery agent when you receive the product.</div>
              @error('payment_method') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="card">
              <div class="card-head">
                <h2>Order note</h2>
              </div>
              <textarea name="order_note" placeholder="Any special delivery instructions (optional)">{{ old('order_note') }}</textarea>
            </div>
          </form>
        </div>

        <div class="summary">
          <div class="card">
            <div class="summary-top">
              <h2>Order summary</h2>
              <div class="count" x-text="totalQty + ' item' + (totalQty !== 1 ? 's' : '')"></div>
            </div>

            <div class="fs-bar-slot" x-html="fsBarHtml"></div>

            <div class="item-list">
              @foreach ($items as $item)
                <div class="item" id="cart-item-{{ $item->id }}">
                  <div class="thumb">
                    @if($item->product->image)
                      <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                    @else
                      {{ strtoupper(mb_substr($item->product->name, 0, 1)) }}
                    @endif
                  </div>
                  <div class="item-info">
                    <div class="item-name" style="display:flex; align-items:start; justify-content:space-between; gap:8px;">
                      {{ $item->product->name }}
                      <template x-if="items.length > 1">
                        <button type="button" @click="removeItem('{{ $item->id }}')" style="background:none; border:none; cursor:pointer; color:var(--red); font-size:16px; line-height:1; padding:0; flex-shrink:0;" title="Remove">&times;</button>
                      </template>
                    </div>
                    @if ($item->variant && is_array($item->variant) && count($item->variant) > 0)
                      <div class="item-variant">
                        @foreach($item->variant as $v)
                          <span style="display:inline-block; background:#F1EEFE; color:#6C4EE8; font-size:12px; padding:3px 8px; border-radius:5px; margin-right:4px; font-weight:500;">{{ $v }}</span>
                        @endforeach
                      </div>
                    @elseif ($item->variant && is_string($item->variant) && $item->variant !== '')
                      <div class="item-variant">
                        @foreach(explode(' / ', $item->variant) as $v)
                          <span style="display:inline-block; background:#F1EEFE; color:#6C4EE8; font-size:12px; padding:3px 8px; border-radius:5px; margin-right:4px; font-weight:500;">{{ $v }}</span>
                        @endforeach
                      </div>
                    @endif
                    @if ($item->custom_fields_data && count($item->custom_fields_data) > 0)
                      @foreach ($item->custom_fields_data as $label => $value)
                        <div class="item-variant">
                          <span style="color:#9CA3AF;">{{ $label }}:</span>
                          @if (is_array($value))
                            {{ basename($value['path'] ?? $value['name'] ?? json_encode($value)) }}
                          @elseif(is_string($value) && preg_match('/\.\w{2,5}$/', $value) && str_contains($value, '/'))
                            <a href="{{ asset('storage/' . $value) }}" target="_blank" style="color:#6C4EE8; text-decoration:underline;">{{ basename($value) }}</a>
                          @else
                            {{ $value }}
                          @endif
                        </div>
                      @endforeach
                    @endif
                    <div class="item-bottom">
                      <div class="qty-stepper">
                        <button type="button" @click="changeQty('{{ $item->id }}', -1)">&minus;</button>
                        <span class="qty-val" x-text="getItemQty('{{ $item->id }}')"></span>
                        <button type="button" @click="changeQty('{{ $item->id }}', 1)">+</button>
                      </div>
                      <div class="item-price mono" x-text="formatPrice(getItemPrice('{{ $item->id }}'))"></div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="promo-row">
              <input type="text" id="promoInput" placeholder="Enter promo code" x-model="promoInput" :disabled="promoApplied">
              <button type="button" class="promo-btn" id="promoBtn" @click="applyPromo()">Apply</button>
            </div>
            <template x-if="promoMsg">
              <div class="promo-msg" :style="'display:block; color:' + (promoMsgType === 'success' ? 'var(--green)' : 'var(--red)')" x-text="promoMsg"></div>
            </template>

            <div class="totals">
              <div class="total-row"><span>Subtotal</span><span class="tv mono" x-text="formatPrice(subtotal)"></span></div>
              @if (! $isDigitalOnly)
              <div class="total-row"><span>Delivery charge</span><span class="tv mono" x-text="formatPrice(deliveryCharge)"></span></div>
              @endif
              <div class="total-row discount" :style="promoApplied ? 'display:flex' : 'display:none'">
                <span x-text="'Discount (' + promoCode + ')'"></span>
                <span class="tv mono" x-text="'\u2212 ' + formatPrice(promoDiscount)"></span>
              </div>
              <div class="stitch-hr" style="margin:10px 0;"></div>
              <div class="total-row grand"><span>Total</span><span x-text="formatPrice(grandTotal)"></span></div>
            </div>

            <button type="button" class="place-order" @click="validateAndSubmit()">
              Confirm order
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>

            <div class="trust-row">
              <div class="trust-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z"/></svg>
                100% authentic
              </div>
              <div class="trust-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                7-day return
              </div>
              <div class="trust-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="13" rx="2"/><path d="M16 3v4M8 3v4M2 11h20"/></svg>
                Cash on delivery
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
      function checkout() {
        return {
          addressType: 'home',
          customerName: '{{ old('first_name', auth()->user()?->name ?? '') }}',
          customerPhone: '{{ old('phone') }}',
          division: '',
          district: '',
          fullAddress: '',
          landmark: '',
          deliveryOption: '',
          paymentMethod: '{{ $enabledPayments[0]['id'] ?? 'bkash' }}',
          promoInput: '',
          promoApplied: false,
          promoCode: '',
          promoDiscount: 0,
          promoMsg: '',
          promoMsgType: '',

          items: [
            @foreach ($items as $item)
            {
              cartItemId: @js((string) $item->id),
              id: @js((string) $item->product_id),
              name: @js($item->product->name),
              variant: @js(is_array($item->variant) ? $item->variant : ($item->variant ? explode(' / ', $item->variant) : [])),
              price: {{ (float)($item->product->sale_price ?? $item->product->price) }},
              qty: {{ $item->quantity }},
              weight: {{ (float)($item->product->weight ?? 0) }},
            },
            @endforeach
          ],

          upazila: '',

          divisions: @json(array_column($geoData, 'name')),
          geoData: @json($geoData),

          shippingZones: @json($shippingZones),
          legacyDeliveryOptions: @json($enabledDelivery),

          isDigitalOnly: @json($isDigitalOnly),

          paymentMethods: @json($enabledPayments),

          freeShippingThreshold: {{ $freeShippingThreshold }},
          freeShippingType: @json($freeShippingType),
          freeShippingWeightThreshold: {{ $freeShippingWeightThreshold }},
          freeShippingQualifies: {{ $freeShippingQualifies ? 'true' : 'false' }},

          get districtList() {
            if (!this.division) return [];
            const div = this.geoData.find(d => d.name === this.division);
            return div ? div.districts.map(d => d.name) : [];
          },

          get upazilaList() {
            if (!this.division || !this.district) return [];
            const div = this.geoData.find(d => d.name === this.division);
            if (!div) return [];
            const dist = div.districts.find(d => d.name === this.district);
            return dist ? dist.upazilas : [];
          },

          get subtotal() {
            return this.items.reduce((sum, i) => sum + i.price * i.qty, 0);
          },

          get fsEnabled() {
            return (this.freeShippingType === 'product_weight' && this.freeShippingWeightThreshold > 0)
              || this.freeShippingThreshold > 0
              || ['specific_products', 'specific_categories', 'specific_collections'].includes(this.freeShippingType);
          },

          get subtotalWeight() {
            return this.items.reduce((sum, i) => sum + (i.weight || 0) * i.qty, 0);
          },

          get fsQualifies() {
            if (!this.fsEnabled) return false;
            if (this.freeShippingType === 'order_amount') return this.subtotal >= this.freeShippingThreshold;
            if (this.freeShippingType === 'product_weight') return this.subtotalWeight <= this.freeShippingWeightThreshold;
            return this.freeShippingQualifies;
          },

          get _matchingZone() {
            if (!this.shippingZones.length) return null;
            const upazila = (this.upazila || '').toLowerCase();
            const district = (this.district || '').toLowerCase();
            const division = (this.division || '').toLowerCase();
            for (let zi = 0; zi < this.shippingZones.length; zi++) {
              const zone = this.shippingZones[zi];
              const regions = (zone.regions || []).map(r => String(r).toLowerCase());
              if (regions.includes('all')) return { zone, index: zi };
              if (upazila && regions.includes(upazila)) return { zone, index: zi };
              if (district && regions.includes(district)) return { zone, index: zi };
              if (division && regions.includes(division)) return { zone, index: zi };
            }
            return null;
          },

          get deliveryOptions() {
            if (!this.shippingZones.length) {
              return (this.legacyDeliveryOptions || []).map(o => ({
                id: o.id,
                type: o.type || 'flat_rate',
                title: o.title || o.name,
                description: o.description || o.desc || '',
                amount: +o.amount || +(o.price || 0),
                per_kg: +o.per_kg || 0,
              }));
            }
            const matched = this._matchingZone;
            if (!matched) return [];
            const list = [];
            (matched.zone.methods || []).forEach((m, mi) => {
              if (!m.enabled) return;
              list.push({
                id: 'z' + matched.index + 'm' + mi,
                type: m.type,
                title: m.title,
                description: m.description,
                amount: +m.amount || 0,
                per_kg: +m.per_kg || 0,
              });
            });
            return list;
          },

          methodPrice(opt) {
            if (this.isDigitalOnly || this.fsQualifies) return this.formatPrice(0);
            if (opt.type === 'free') return this.formatPrice(0);
            if (opt.type === 'weight_based') return this.formatPrice((+opt.amount || 0) + (+opt.per_kg || 0) * this.subtotalWeight);
            return this.formatPrice(+opt.amount || 0);
          },

          _syncDeliveryOption() {
            const list = this.deliveryOptions;
            if (list.length && !list.some(o => o.id === this.deliveryOption)) {
              this.deliveryOption = list[0].id;
            } else if (!list.length) {
              this.deliveryOption = '';
            }
          },

          get deliveryCharge() {
            if (this.isDigitalOnly) return 0;
            if (this.fsQualifies) return 0;
            const opt = this.deliveryOptions.find(o => o.id === this.deliveryOption);
            if (!opt) return 0;
            if (opt.type === 'free') return 0;
            if (opt.type === 'weight_based') return (+opt.amount || 0) + (+opt.per_kg || 0) * this.subtotalWeight;
            return +opt.amount || 0;
          },

          get totalQty() {
            return this.items.reduce((sum, i) => sum + i.qty, 0);
          },

          get grandTotal() {
            return Math.max(0, this.subtotal + this.deliveryCharge - this.promoDiscount);
          },

          get fsBarHtml() {
            if (!this.fsEnabled) return '';

            const sym = '{{ addslashes($currencySymbol) }}';
            const fmt = n => Math.round(n).toLocaleString('en-US');
            const congrats = '<div class="shipping-message" style="font-size:13px;color:#16A34A;font-weight:600;">Congratulations, You\'ve unlocked <strong>FREE Shipping!</strong></div>';
            const flagSvg = reached => '<svg viewBox="0 0 24 24" fill="none" stroke="' + (reached ? '#16A34A' : '#6B7280') + '" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="29" height="29"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>';
            const truck = parked => '<div class="van-container' + (parked ? ' van-parked' : '') + '"><div class="speed-line"></div><div class="speed-line"></div><div class="speed-line"></div><div class="smoke"><span></span><span></span><span></span></div><img src="https://merchant.pathao.com/assets/courier-truck-logo-animated.50cbdf0a.svg" alt="Delivery"></div>';
            const pointOnly = reached => '<div class="shipping-point' + (reached ? ' reached' : '') + '">' + flagSvg(reached) + '<div class="shipping-label"' + (reached ? ' style="color:#16A34A;"' : '') + '>Free<br>Shipping</div></div>';
            const reachedInner = '<div class="progress-bar"><div class="progress-fill" style="width:100%;"></div></div>' + truck(true) + pointOnly(true);
            let inner;

            if (this.freeShippingType === 'order_amount' && this.freeShippingThreshold > 0) {
              const remaining = Math.max(0, this.freeShippingThreshold - this.subtotal);
              const pctVal = Math.min(100, (this.subtotal / this.freeShippingThreshold) * 100);
              const msg = this.fsQualifies
                ? congrats
                : '<div class="shipping-message" style="font-size:13px;">Add items worth <strong>' + sym + fmt(remaining) + '</strong> more to unlock FREE Shipping</div>';
              inner = this.fsQualifies
                ? '<div class="progress-wrapper">' + reachedInner + '</div>'
                : '<div class="progress-wrapper" style="--target-pct:' + pctVal + '%;"><div class="progress-bar"><div class="progress-fill blue" style="width:' + pctVal + '%;"></div></div>' + truck(false) + pointOnly(false) + '</div>';
              return '<div class="shipping-progress">' + msg + inner + '</div>';
            }

            if (this.freeShippingType === 'product_weight' && this.freeShippingWeightThreshold > 0) {
              const over = Math.max(0, this.subtotalWeight - this.freeShippingWeightThreshold);
              const pctVal = Math.min(100, (this.subtotalWeight / this.freeShippingWeightThreshold) * 100);
              const msg = this.fsQualifies
                ? congrats
                : '<div class="shipping-message" style="font-size:13px;">Your cart is over the free shipping limit — remove <strong>' + over.toFixed(1) + ' kg</strong> to get FREE Shipping</div>';
              inner = this.fsQualifies
                ? '<div class="progress-wrapper">' + reachedInner + '</div>'
                : '<div class="progress-wrapper" style="--target-pct:' + pctVal + '%;"><div class="progress-bar"><div class="progress-fill blue" style="width:' + pctVal + '%;"></div></div>' + truck(false) + pointOnly(false) + '</div>';
              return '<div class="shipping-progress">' + msg + inner + '</div>';
            }

            if (this.fsQualifies) {
              return '<div class="shipping-progress">' + congrats + '<div class="progress-wrapper">' + reachedInner + '</div></div>';
            }

            return '';
          },

          init() {
            this._syncDeliveryOption();
            this.$watch('division', () => { this.district = ''; this.upazila = ''; this._syncDeliveryOption(); this.trackAbandon(); });
            this.$watch('district', () => { this.upazila = ''; this._syncDeliveryOption(); this.trackAbandon(); });
            this.$watch('upazila', () => this.trackAbandon());
            this.trackAbandon();
          },

          changeQty(cartItemId, delta) {
            const item = this.items.find(i => i.cartItemId === cartItemId);
            if (!item) return;
            const newQty = item.qty + delta;
            if (newQty >= 1 && newQty <= 9) {
              item.qty = newQty;
            }
          },

          removeItem(cartItemId) {
            const idx = this.items.findIndex(i => i.cartItemId === cartItemId);
            if (idx === -1) return;
            const item = this.items[idx];
            const isSession = String(cartItemId).startsWith('session_');
            const url = isSession
              ? '/cart/session/' + item.id
              : '/cart/' + cartItemId;
            fetch(url, {
              method: 'DELETE',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
              },
            }).then(r => r.json()).then(data => {
              if (data.success) {
                this.items.splice(idx, 1);
                const el = document.getElementById('cart-item-' + cartItemId);
                if (el) el.remove();
                this.trackAbandon();
              }
            });
          },

          getItemQty(cartItemId) {
            const item = this.items.find(i => i.cartItemId === cartItemId);
            return item ? item.qty : 0;
          },

          getItemPrice(cartItemId) {
            const item = this.items.find(i => i.cartItemId === cartItemId);
            return item ? item.price * item.qty : 0;
          },

          formatPrice(n) {
            return '{{ $currencySymbol }}' + Math.round(n).toLocaleString('en-US');
          },

          applyPromo() {
            const code = this.promoInput.trim().toUpperCase();
            if (!code) return;
            this.promoMsg = '';
            this.promoApplied = false;
            this.promoDiscount = 0;
            fetch('{{ route("checkout.validate-promo") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
              },
              body: JSON.stringify({ code: code, subtotal: this.subtotal })
            })
            .then(r => r.json())
            .then(data => {
              if (data.valid) {
                this.promoApplied = true;
                this.promoCode = data.code;
                this.promoDiscount = data.discount;
                this.promoMsg = '✓ ' + data.message;
                this.promoMsgType = 'success';
              } else {
                this.promoApplied = false;
                this.promoCode = '';
                this.promoDiscount = 0;
                this.promoMsg = data.message;
                this.promoMsgType = 'error';
              }
            })
            .catch(() => {
              this.promoMsg = 'Something went wrong. Please try again.';
              this.promoMsgType = 'error';
            });
          },

          trackAbandon() {
            const name = this.customerName.trim();
            const phone = this.customerPhone.trim();
            if (!name || phone.length < 11) return;
            const address = [
              this.fullAddress,
              this.landmark,
              this.upazila,
              this.district,
              this.division,
            ].filter(v => v && v.trim()).map(v => v.trim()).join(', ');
            fetch('{{ route("checkout.track") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
              },
              body: JSON.stringify({ name: name, phone: phone, address: address })
            });
          },

          validateAndSubmit() {
            const name = document.querySelector('input[name="first_name"]').value.trim();
            const phone = document.querySelector('input[name="phone"]').value.trim();
            if (!name || phone.length < 11) {
              alert('Please enter your name and a valid mobile number');
              return;
            }
            document.getElementById('checkout-form').submit();
          },
        };
      }
    </script>

    @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const firstError = document.querySelector('.field-error');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      });
    </script>
    @endif
    @endpush
</x-app-layout>
