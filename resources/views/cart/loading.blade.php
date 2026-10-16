<x-app-layout>
    @php
        $cartRedirectUrl = route('cart.index');
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
      #cart-loading{
        --brand-indigo:#4f46e5;
        --brand-indigo-light:#eef2ff;
        --green-success:#10b981;
        --text-dark:#111827;
        --text-muted:#6b7280;
        --bg-gray:#f9fafb;
        background:var(--bg-gray);
        color:var(--text-dark);
        font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
        margin-left:calc(50% - 50vw);
        margin-right:calc(50% - 50vw);
        margin-top:-1.5rem;
        margin-bottom:-1.5rem;
        padding-bottom:1.5rem;
        min-height:100vh;
        display:flex;
        justify-content:center;
        align-items:center;
      }
      @media (min-width:640px){
        #cart-loading{
          margin-top:-2rem;
          margin-bottom:-2rem;
          padding-bottom:2rem;
          min-height:calc(100vh - 1rem);
        }
      }
      @media (min-width:1024px){
        #cart-loading{
          margin-top:-2.5rem;
          margin-bottom:-2.5rem;
          padding-bottom:2.5rem;
          min-height:calc(100vh - 2rem);
        }
      }

      #cart-loading .transition-card{
        background:#ffffff;
        border-radius:20px;
        box-shadow:0 10px 30px -5px rgba(0,0,0,0.08);
        width:100%;
        max-width:440px;
        padding:2.5rem 2rem;
        box-sizing:border-box;
        text-align:center;
      }

      #cart-loading .icon-wrapper{
        position:relative;
        width:80px;
        height:80px;
        margin:0 auto 1.5rem auto;
        background:var(--brand-indigo-light);
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
      }
      #cart-loading .cart-icon{
        width:36px;
        height:36px;
        color:var(--brand-indigo);
        animation:cart-float 2s ease-in-out infinite;
      }
      #cart-loading .pulse-ring{
        position:absolute;
        width:100%;
        height:100%;
        border-radius:50%;
        border:2px solid var(--brand-indigo);
        animation:cart-pulse 2s cubic-bezier(0.45,0,0.55,1) infinite;
      }

      #cart-loading .title{
        font-size:1.35rem;
        font-weight:700;
        color:var(--text-dark);
        margin:0 0 0.25rem 0;
      }
      #cart-loading .subtitle{
        font-size:0.9rem;
        color:var(--text-muted);
        margin:0 0 1.75rem 0;
      }

      #cart-loading .status-list{
        display:flex;
        flex-direction:column;
        gap:12px;
        background:var(--bg-gray);
        padding:1.25rem;
        border-radius:12px;
        margin-bottom:2rem;
        text-align:left;
      }
      #cart-loading .status-item{
        display:flex;
        align-items:center;
        gap:10px;
        font-size:0.875rem;
        color:var(--text-muted);
        transition:color 0.3s ease;
      }
      #cart-loading .status-item.active{
        color:var(--text-dark);
        font-weight:600;
      }
      #cart-loading .status-icon{
        width:18px;
        height:18px;
        border-radius:50%;
        border:2px solid #d1d5db;
        display:flex;
        align-items:center;
        justify-content:center;
        transition:all 0.3s ease;
        flex-shrink:0;
      }
      #cart-loading .status-item.active .status-icon{
        border-color:var(--brand-indigo);
        border-top-color:transparent;
        animation:cart-spin 0.8s linear infinite;
      }
      #cart-loading .status-item.done .status-icon{
        background:var(--green-success);
        border-color:var(--green-success);
        animation:none;
      }
      #cart-loading .status-item.done .status-icon svg{
        display:block;
      }
      #cart-loading .status-icon svg{
        display:none;
        width:12px;
        height:12px;
        color:#ffffff;
      }

      #cart-loading .progress-track{
        height:4px;
        width:100%;
        background:#e5e7eb;
        border-radius:2px;
        overflow:hidden;
        position:relative;
      }
      #cart-loading .progress-bar{
        position:absolute;
        left:0;
        height:100%;
        width:0%;
        background:linear-gradient(90deg,var(--brand-indigo),#818cf8);
        border-radius:2px;
        transition:width 0.4s ease;
      }

      @keyframes cart-float{
        0%,100%{transform:translateY(0);}
        50%{transform:translateY(-6px);}
      }
      @keyframes cart-pulse{
        0%{transform:scale(1);opacity:0.8;}
        100%{transform:scale(1.35);opacity:0;}
      }
      @keyframes cart-spin{
        100%{transform:rotate(360deg);}
      }
    </style>

    <div id="cart-loading">
      <div class="transition-card">
        <div class="icon-wrapper">
          <div class="pulse-ring"></div>
          <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
        </div>

        <h1 class="title">Reviewing your cart</h1>
        <p class="subtitle">Applying best available offers...</p>

        <div class="status-list">
          <div class="status-item active" id="step1">
            <div class="status-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <span>Checking item availability</span>
          </div>
          <div class="status-item" id="step2">
            <div class="status-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <span>Calculating discounts &amp; Coupon</span>
          </div>
          <div class="status-item" id="step3">
            <div class="status-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <span>Reserving your items</span>
          </div>
        </div>

        <div class="progress-track">
          <div class="progress-bar" id="progressBar"></div>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
      (function () {
        const cartUrl = {{ Js::from($cartRedirectUrl) }};
        const steps = [
          { id: "step1", progress: 35 },
          { id: "step2", progress: 70 },
          { id: "step3", progress: 100 }
        ];

        let current = 0;

        function processSteps() {
          if (current >= steps.length) return;
          const stepEl = document.getElementById(steps[current].id);
          const progressBar = document.getElementById("progressBar");

          progressBar.style.width = steps[current].progress + "%";

          setTimeout(() => {
            stepEl.classList.remove("active");
            stepEl.classList.add("done");

            current++;
            if (current < steps.length) {
              const nextStepEl = document.getElementById(steps[current].id);
              nextStepEl.classList.add("active");
            } else {
              window.location.href = cartUrl;
            }
          }, 700);
        }

        const stepInterval = setInterval(() => {
          if (current >= steps.length) {
            clearInterval(stepInterval);
          } else {
            processSteps();
          }
        }, 900);
      })();
    </script>
    @endpush
</x-app-layout>
