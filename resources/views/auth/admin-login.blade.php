<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($storeSettings['store_name'] ?? config('app.name', 'Store')) }} - Admin Log In</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }
        html, body {
            height: 100%;
            margin: 0;
            background: #0d0a16;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            overflow: hidden;
        }

        .scene {
            position: relative;
            width: 420px;
            height: 560px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 70px;
            background: #1e1a2e;
            border-radius: 28px;
            border: 1px solid rgba(255,200,120,0.15);
            box-shadow: 0 0 20px rgba(255,190,110,0.08);
            transition: border-color 0.7s ease, box-shadow 0.7s ease;
            overflow: hidden;
        }
        .scene.is-on {
            border-color: rgba(255,205,120,0.5);
            box-shadow: 0 0 55px 8px rgba(255,190,110,0.28);
        }

        .hint {
            position: absolute;
            top: 18px;
            left: 50%;
            transform: translateX(-50%);
            color: #7b7690;
            font-size: 13px;
            letter-spacing: 0.02em;
        }

        .glow {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translate(-50%, 0);
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,225,150,0.55) 0%, rgba(255,200,110,0.18) 35%, rgba(255,200,110,0) 70%);
            opacity: 0;
            transition: opacity 0.8s ease;
            pointer-events: none;
        }
        .scene.is-on .glow { opacity: 1; }

        .lamp {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            margin-bottom: 30px;
        }

        .shade {
            width: 150px;
            height: 78px;
            border-radius: 75px 75px 10px 10px;
            background: linear-gradient(180deg, #4a465a 0%, #34303f 100%);
            box-shadow: inset 0 -6px 10px rgba(0,0,0,0.25);
            transition: background 0.5s ease, box-shadow 0.5s ease;
        }
        .scene.is-on .shade {
            background: linear-gradient(180deg, #fff8e6 0%, #ffe9b8 100%);
            box-shadow: 0 0 40px 10px rgba(255,210,120,0.55), inset 0 -6px 10px rgba(180,130,40,0.2);
        }

        .pole {
            width: 8px;
            height: 150px;
            background: linear-gradient(180deg, #5a5568, #3a3648);
        }

        .base {
            width: 78px;
            height: 14px;
            border-radius: 7px;
            background: linear-gradient(180deg, #5a5568, #3a3648);
        }

        .cord {
            position: absolute;
            top: 70px;
            left: 50%;
            width: 1px;
            height: 46px;
            background: #6b6680;
            transform-origin: top center;
            transition: transform 0.15s ease;
            transform: translateX(-50%);
        }
        .pull {
            position: absolute;
            top: 44px;
            left: -6px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #b98a4a;
            cursor: pointer;
        }
        .pull:hover { background: #cf9c56; }
        .scene.is-on .cord { transform: translateX(-50%) rotate(6deg); }

        .card {
            position: relative;
            z-index: 2;
            width: 280px;
            padding: 26px 26px 24px;
            border-radius: 16px;
            background: rgba(35, 32, 48, 0.55);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.08);
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 0.6s ease 0.25s, transform 0.6s ease 0.25s;
        }
        .scene.is-on .card {
            opacity: 1;
            transform: translateY(0);
        }

        .card h2 {
            margin: 0 0 18px;
            color: #f4f1ff;
            font-size: 19px;
            font-weight: 600;
            text-align: center;
        }

        label {
            display: block;
            font-size: 11px;
            color: #a8a2c0;
            margin: 0 0 6px;
        }

        .field {
            width: 100%;
            height: 38px;
            margin-bottom: 14px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.06);
            color: #f0eefa;
            font-size: 13.5px;
            padding: 0 12px;
        }
        .field::placeholder { color: #79738f; }
        .field:focus { outline: none; border-color: #d9ab5c; }

        .signin {
            width: 100%;
            height: 44px;
            margin-top: 6px;
            border: none;
            border-radius: 999px;
            background: #7c5cfc;
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 600;
            letter-spacing: 0.2px;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.3s ease, background-color 0.3s ease;
            box-shadow: 0 4px 14px rgba(124,92,252,0.4);
        }
        .signin:hover {
            background: #8d70fd;
            box-shadow: 0 6px 20px rgba(124,92,252,0.5);
            transform: translateY(-1px);
        }
        .signin:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 2px 8px rgba(124,92,252,0.35);
        }

        .session-status {
            font-size: 12.5px;
            color: #9fe6b0;
            margin: -6px 0 10px;
            text-align: center;
        }

        .error {
            font-size: 12px;
            color: #ff9d9d;
            margin: -6px 0 10px;
            display: none;
        }
        .error.show { display: block; }

        @media (max-width: 480px) {
            html, body { overflow: auto; }
            .scene {
                width: 100%;
                height: 100%;
                aspect-ratio: 1 / 1;
                border-radius: 0;
            }
            .hint { font-size: 12px; white-space: nowrap; }
            .card { width: min(280px, 86vw); }
        }

        @media (min-width: 900px) {
            .scene {
                width: 640px;
                height: 640px;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                gap: 50px;
                padding-top: 0;
            }
            .lamp { margin-bottom: 0; }
            .card { margin-top: 0; }

            .glow {
                top: 50%;
                left: 175px;
                transform: translate(-50%, -50%);
                width: 480px;
                height: 480px;
            }
        }
    </style>
</head>
<body>

@php
    $laravelError = $errors->any() ? $errors->all()[0] : null;
@endphp

<div class="scene {{ $laravelError ? 'is-on' : '' }}" id="scene">
    <div class="glow"></div>

    <div class="lamp">
        <div class="shade"></div>
        <div class="cord"><div class="pull" id="pull"></div></div>
        <div class="pole"></div>
        <div class="base"></div>
    </div>

    <div class="card">
        <h2>Welcome</h2>
        <form id="loginForm" method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <label for="user">Phone Number</label>
            <input class="field" id="user" name="phone" value="{{ old('phone') }}" placeholder="Enter phone number" autocomplete="tel" required />
            <label for="pass">Password</label>
            <input class="field" id="pass" name="password" type="password" placeholder="Enter password" autocomplete="current-password" required />

            @if (session('status'))
                <p class="session-status">{{ session('status') }}</p>
            @endif

            <p class="error {{ $laravelError ? 'show' : '' }}" id="error">
                {{ $laravelError ?? 'Enter a phone number and password first.' }}
            </p>

            <button class="signin" type="submit">Sign in</button>
        </form>
    </div>
</div>

<script>
    const scene = document.getElementById('scene');
    const pull = document.getElementById('pull');
    const form = document.getElementById('loginForm');
    const error = document.getElementById('error');

    pull.addEventListener('click', function () {
        scene.classList.toggle('is-on');
    });

    if (window.innerWidth >= 900) {
        scene.classList.add('is-on');
    }

    form.addEventListener('submit', function (e) {
        const user = document.getElementById('user').value.trim();
        const pass = document.getElementById('pass').value.trim();
        if (!user || !pass) {
            e.preventDefault();
            error.style.display = 'block';
            return;
        }
        error.style.display = 'none';
    });

    document.getElementById('user').addEventListener('input', function () {
        error.style.display = 'none';
    });
    document.getElementById('pass').addEventListener('input', function () {
        error.style.display = 'none';
    });
</script>

</body>
</html>