<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — {{ config('app.name', 'Store') }}</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Figtree',sans-serif;background:#0a0a0a;color:#fafafa;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;overflow:hidden}

        .bg-glow{position:fixed;width:600px;height:600px;border-radius:50%;filter:blur(160px);opacity:.12;pointer-events:none}
        .bg-glow.a{background:#6366f1;top:-200px;left:-100px;animation:float 12s ease-in-out infinite}
        .bg-glow.b{background:#f59e0b;bottom:-250px;right:-150px;animation:float 14s ease-in-out infinite reverse}
        @keyframes float{0%,100%{transform:translate(0,0)}50%{transform:translate(40px,30px)}}

        .container{text-align:center;position:relative;z-index:1;max-width:480px;width:100%}

        .icon-wrap{width:88px;height:88px;margin:0 auto 32px;border-radius:24px;background:linear-gradient(135deg,rgba(251,191,36,.12),rgba(245,158,11,.06));border:1px solid rgba(251,191,36,.15);display:flex;align-items:center;justify-content:center;animation:pulse-ring 2.5s ease-in-out infinite}
        .icon-wrap svg{width:40px;height:40px;color:#fbbf24}
        @keyframes pulse-ring{0%,100%{box-shadow:0 0 0 0 rgba(251,191,36,.15)}50%{box-shadow:0 0 0 16px rgba(251,191,36,0)}}

        .badge{display:inline-block;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#fbbf24;background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.15);padding:5px 14px;border-radius:999px;margin-bottom:24px}

        h1{font-size:32px;font-weight:700;line-height:1.2;margin-bottom:12px;background:linear-gradient(135deg,#fff 0%,#94a3b8 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        p{color:#64748b;font-size:15px;line-height:1.7;margin-bottom:40px;max-width:360px;margin-left:auto;margin-right:auto}

        .progress-wrap{margin-bottom:40px}
        .progress-label{display:flex;justify-content:space-between;font-size:12px;color:#475569;margin-bottom:8px}
        .progress-bar{width:100%;height:6px;background:rgba(255,255,255,.06);border-radius:99px;overflow:hidden}
        .progress-fill{height:100%;width:65%;background:linear-gradient(90deg,#6366f1,#a78bfa);border-radius:99px;animation:progress-move 3s ease-in-out infinite}
        @keyframes progress-move{0%{width:45%}50%{width:72%}100%{width:45%}}

        .status-row{display:flex;justify-content:center;gap:32px;margin-bottom:40px}
        .status-item{display:flex;flex-direction:column;align-items:center;gap:6px}
        .status-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;animation:blink 1.8s ease-in-out infinite}
        .status-dot.warn{background:#f59e0b;animation-delay:.3s}
        .status-text{font-size:11px;color:#475569;text-transform:uppercase;letter-spacing:.06em}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

        .socials{display:flex;justify-content:center;gap:12px}
        .socials a{width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;color:#475569;transition:all .2s}
        .socials a:hover{background:rgba(255,255,255,.08);color:#e2e8f0;border-color:rgba(255,255,255,.12)}

        .footer-note{margin-top:48px;font-size:12px;color:#334155}
    </style>
</head>
<body>
    <div class="bg-glow a"></div>
    <div class="bg-glow b"></div>

    <div class="container">
        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>
            </svg>
        </div>

        <div class="badge">Under Maintenance</div>
        <h1>We&rsquo;ll be back shortly</h1>
        <p>We&rsquo;re making things better for you. Our team is working hard to get everything ready.</p>

        <div class="progress-wrap">
            <div class="progress-label">
                <span>Restoration progress</span>
                <span>Almost there</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
        </div>

        <div class="status-row">
            <div class="status-item">
                <div class="status-dot"></div>
                <span class="status-text">Server</span>
            </div>
            <div class="status-item">
                <div class="status-dot"></div>
                <span class="status-text">Database</span>
            </div>
            <div class="status-item">
                <div class="status-dot warn"></div>
                <span class="status-text">Updates</span>
            </div>
        </div>

        <div class="socials">
            <a href="#" title="WhatsApp">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            </a>
            <a href="#" title="Facebook">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="#" title="Instagram">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            </a>
        </div>

        <p class="footer-note">Thank you for your patience.</p>
    </div>
</body>
</html>
