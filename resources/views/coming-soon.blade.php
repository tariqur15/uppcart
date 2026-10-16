<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coming Soon — {{ config('app.name', 'Store') }}</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Figtree',sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#f8fafc;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
        .card{text-align:center;max-width:480px;width:100%}
        .badge{display:inline-block;background:rgba(99,102,241,.2);color:#a5b4fc;font-size:13px;font-weight:600;padding:6px 16px;border-radius:999px;margin-bottom:28px;letter-spacing:.04em}
        h1{font-size:36px;font-weight:700;line-height:1.2;margin-bottom:16px}
        h1 span{color:#818cf8}
        p{color:#94a3b8;font-size:16px;line-height:1.7;margin-bottom:32px}
        .timer{display:flex;justify-content:center;gap:16px;margin-bottom:40px}
        .timer-unit{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:16px 20px;min-width:80px}
        .timer-num{font-size:28px;font-weight:700;color:#fff}
        .timer-label{font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-top:4px}
        .email-form{display:flex;gap:8px;max-width:380px;margin:0 auto}
        .email-form input{flex:1;padding:12px 16px;border-radius:10px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;font-size:14px;outline:none}
        .email-form input::placeholder{color:#64748b}
        .email-form input:focus{border-color:#818cf8}
        .email-form button{padding:12px 24px;border-radius:10px;border:none;background:#6366f1;color:#fff;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;transition:background .15s}
        .email-form button:hover{background:#4f46e5}
        @media(max-width:480px){.email-form{flex-direction:column}.timer-unit{min-width:64px;padding:12px 14px}.timer-num{font-size:22px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">COMING SOON</div>
        <h1>Something <span>amazing</span> is on the way</h1>
        <p>We&rsquo;re working hard to bring you a new experience. Stay tuned &mdash; we&rsquo;ll be launching soon.</p>
        <div class="timer" id="countdown">
            <div class="timer-unit"><div class="timer-num" id="days">00</div><div class="timer-label">Days</div></div>
            <div class="timer-unit"><div class="timer-num" id="hours">00</div><div class="timer-label">Hours</div></div>
            <div class="timer-unit"><div class="timer-num" id="mins">00</div><div class="timer-label">Minutes</div></div>
            <div class="timer-unit"><div class="timer-num" id="secs">00</div><div class="timer-label">Seconds</div></div>
        </div>
        <form class="email-form" onsubmit="event.preventDefault(); this.querySelector('button').textContent='Subscribed!'; this.querySelector('input').disabled=true;">
            <input type="email" placeholder="Enter your email for updates" required>
            <button type="submit">Notify Me</button>
        </form>
    </div>
    <script>
        (function(){
            var stored = @json($launchDate ?? null);
            var target = stored ? new Date(stored) : new Date(Date.now() + 14 * 864e5);
            if (isNaN(target.getTime())) target = new Date(Date.now() + 14 * 864e5);
            function tick(){
                var diff = target - new Date();
                if(diff <= 0) return;
                document.getElementById('days').textContent = String(Math.floor(diff/864e5)).padStart(2,'0');
                document.getElementById('hours').textContent = String(Math.floor((diff%864e5)/36e5)).padStart(2,'0');
                document.getElementById('mins').textContent = String(Math.floor((diff%36e5)/6e4)).padStart(2,'0');
                document.getElementById('secs').textContent = String(Math.floor((diff%6e4)/1e3)).padStart(2,'0');
                setTimeout(tick, 1000);
            }
            tick();
        })();
    </script>
</body>
</html>
