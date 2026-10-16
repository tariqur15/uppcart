<x-app-layout>
    @php
        $checkoutStoreName = $storeSettings['store_name'] ?? config('app.name', 'Store');
        $checkoutRedirectUrl = route('checkout.index');
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
      #checkout-loading{
        --brand-green: #0b7043;
        --light-green: #e6f4ed;
        --gray-track: #e5e7eb;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        background:#ffffff;
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
        #checkout-loading{
          margin-top:-2rem;
          margin-bottom:-2rem;
          padding-bottom:2rem;
          min-height:calc(100vh - 1rem);
        }
      }
      @media (min-width:1024px){
        #checkout-loading{
          margin-top:-2.5rem;
          margin-bottom:-2.5rem;
          padding-bottom:2.5rem;
          min-height:calc(100vh - 2rem);
        }
      }

      #checkout-loading .loader-card{
        text-align:center;
        max-width:420px;
        padding:2rem;
      }

      #checkout-loading .spinner-container{
        position:relative;
        width:100px;
        height:100px;
        margin:0 auto 1.5rem auto;
        display:flex;
        align-items:center;
        justify-content:center;
      }
      #checkout-loading .spinner-ring{
        position:absolute;
        width:100%;
        height:100%;
        border-radius:50%;
        border:3px solid var(--light-green);
        border-top-color:var(--brand-green);
        border-right-color:var(--brand-green);
        animation:checkout-spin 1.2s linear infinite;
      }
      #checkout-loading .shield-icon{
        width:32px;
        height:32px;
        color:var(--brand-green);
        z-index:1;
      }

      #checkout-loading .title{
        font-size:1.5rem;
        font-weight:700;
        color:var(--text-dark);
        margin:0 0 0.5rem 0;
      }
      #checkout-loading .subtitle{
        font-size:1rem;
        color:var(--text-muted);
        margin:0 0 2rem 0;
      }

      #checkout-loading .progress-bar-container{
        display:flex;
        gap:8px;
        margin-bottom:2rem;
      }
      #checkout-loading .segment{
        height:6px;
        flex:1;
        background-color:var(--gray-track);
        border-radius:4px;
        overflow:hidden;
        position:relative;
      }
      #checkout-loading .segment-fill{
        height:100%;
        width:0%;
        background-color:var(--brand-green);
        transition:width 0.4s ease-in-out;
      }

      #checkout-loading .trust-badges{
        display:flex;
        justify-content:center;
        align-items:center;
        gap:12px;
        font-size:0.875rem;
        color:var(--text-muted);
        flex-wrap:wrap;
      }
      #checkout-loading .badge-item{
        display:flex;
        align-items:center;
        gap:4px;
      }
      #checkout-loading .badge-item svg{
        width:16px;
        height:16px;
        color:var(--brand-green);
      }
      #checkout-loading .divider{
        color:var(--gray-track);
      }
      #checkout-loading .sub-badge{
        margin-top:0.75rem;
        display:flex;
        justify-content:center;
        align-items:center;
        gap:4px;
        font-size:0.875rem;
        color:var(--text-muted);
      }

      @keyframes checkout-spin{
        0%{transform:rotate(0deg);}
        100%{transform:rotate(360deg);}
      }
    </style>

    <div id="checkout-loading">
      <div class="loader-card">
        <div class="spinner-container">
          <div class="spinner-ring"></div>
          <svg class="shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <circle cx="12" cy="11" r="1" fill="currentColor"/>
            <path d="M12 12v3"/>
          </svg>
        </div>

        <h1 class="title">Preparing your secure checkout</h1>
        <p class="subtitle">Please wait a moment...</p>

        <div class="progress-bar-container">
          <div class="segment"><div class="segment-fill" id="seg1"></div></div>
          <div class="segment"><div class="segment-fill" id="seg2"></div></div>
          <div class="segment"><div class="segment-fill" id="seg3"></div></div>
          <div class="segment"><div class="segment-fill" id="seg4"></div></div>
        </div>

        <div class="trust-badges">
          <div class="badge-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              <path d="m9 12 2 2 4-4"/>
            </svg>
            <span>Secure & encrypted</span>
          </div>
          <span class="divider">|</span>
          <div class="badge-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span>Your data is protected</span>
          </div>
        </div>

        <div class="sub-badge">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0b7043" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <span>Getting ready</span>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
      (function () {
        const checkoutUrl = {{ Js::from($checkoutRedirectUrl) }};
        const segments = ["seg1", "seg2", "seg3", "seg4"];
        let currentStep = 0;
        const interval = setInterval(() => {
          if (currentStep < segments.length) {
            document.getElementById(segments[currentStep]).style.width = "100%";
            currentStep++;
          } else {
            clearInterval(interval);
            window.location.href = checkoutUrl;
          }
        }, 600);
      })();
    </script>
    @endpush
</x-app-layout>
