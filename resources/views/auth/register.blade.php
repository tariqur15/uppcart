<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($storeSettings['store_name'] ?? config('app.name', 'Store')) }} - Sign Up</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: #eef1e7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* =========================================
           MAIN CONTAINER
        ========================================= */

        .signup-wrapper {
            width: 100%;
            max-width: 1000px;
            min-height: 680px;

            position: relative;

            display: flex;
            align-items: stretch;

            border-radius: 28px;

            background: linear-gradient(
                        180deg,
                        #E6EFE2 0%,
                        #E1EEE2 100%,
                        #DBECE2 50%
                    );

            border: 2px solid #238b83;

            overflow: hidden;

            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.12);
        }


        /* =========================================
           LEFT SIDE
        ========================================= */

        .signup-visual {
            width: 58%;

            margin-right: auto;

            min-height: 680px;

            position: relative;

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;

            animation: slideBehind 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;

            transform-origin: left center;
        }

        @keyframes slideBehind {

            0% {
                transform: translateX(50%);
            }

            100% {
                transform: translateX(0);
            }

        }

        .signup-visual img {
            width: 100%;
            height: 100%;

            object-fit: cover;
            object-position: center;

            display: block;

            position: relative;

            z-index: 2;

            animation: imgSlide 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        @keyframes imgSlide {

            0% {
                transform: translateX(60%);
            }

            100% {
                transform: translateX(0);
            }

        }

        .visual-overlay {
            position: absolute;
            inset: 0;

            background: linear-gradient(
                98deg,
                rgba(95, 205, 130, 0.05),
                rgba(39, 190, 180, 0.10)
            );

            pointer-events: none;
        }

        /* =========================================
           FLOATING PHONE-EMISSION ANIMATION
        =========================================
           PHONE SOURCE:
           The smartphone sits at ~right-center of the
           image, so these two values control exactly
           where every promo card is born:

             left:  XX%   (0 = far left of visual, 100 = far right)
             top:   XX%   (0 = top of visual,      100 = bottom)

           Adjust them ONLY if the phone moves in the image.
        ========================================= */

        .phone-source {
            position: absolute;

            left: 75%;
            top: 40%;

            width: 0;
            height: 0;

            z-index: 10;

            pointer-events: none;
        }

        .phone-source::before {
            content: "";

            position: absolute;

            left: 50%;
            top: 50%;

            width: 14px;
            height: 14px;

            background: rgba(6, 123, 130, 0.35);

            border-radius: 50%;

            transform: translate(-50%, -50%);

            opacity: 0;

            animation: phonePulse 2.6s ease-out 0.9s infinite;

            pointer-events: none;
        }

        @keyframes phonePulse {

            0% {
                transform: translate(-50%, -50%) scale(0.5);

                opacity: 0.8;
            }

            100% {
                transform: translate(-50%, -50%) scale(2.6);

                opacity: 0;
            }

        }

        .emit-card {
            position: absolute;

            left: 75%;
            top: 40%;

            background: rgba(255, 255, 255, 0.93);

            color: #0d5c63;

            font-weight: 600;

            font-size: 12.5px;

            line-height: 1.4;

            padding: 9px 16px;

            border-radius: 18px;

            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.14);

            backdrop-filter: blur(6px);

            white-space: nowrap;

            opacity: 0;

            transform: translate(-50%, -50%) scale(0.5);

            z-index: 20;

            animation: emitCycle 8.5s cubic-bezier(0.3, 0.25, 0.25, 1) infinite;

            pointer-events: none;
        }

        .emit-card.welcome {
            font-size: 13.5px;

            padding: 12px 20px;

            border-radius: 20px;

            z-index: 22;
        }

        .emit-card .ec-title {
            display: flex;
            align-items: center;

            gap: 7px;

            font-size: 12.5px;
            font-weight: 700;

            color: #067b82;

            letter-spacing: 0.3px;
        }

        .emit-card.welcome .ec-title {
            font-size: 14px;
        }

        .emit-card .ec-sub {
            margin-top: 2px;

            font-size: 11.5px;
            font-weight: 500;

            color: #4a7a80;
        }

        .emit-card i {
            font-size: 14px;

            color: #067b82;
        }

        .emit-card .ec-check {
            color: #0db46a;
        }

        .emit-card .ec-coupon {
            font-weight: 700;

            letter-spacing: 1px;

            color: #067b82;

            background: rgba(255, 231, 46, 0.55);

            padding: 1px 6px;

            border-radius: 6px;

            font-size: 11.5px;
        }

        /* Each item shares a 0.9s start delay (slide-in) + stagger ~1.42s each */
        .emit-card.it-1 { animation-delay: 0.9s;    }
        .emit-card.it-2 { animation-delay: 2.32s;   }
        .emit-card.it-3 { animation-delay: 3.74s;   }
        .emit-card.it-4 { animation-delay: 5.16s;   }
        .emit-card.it-5 { animation-delay: 6.58s;   }
        .emit-card.it-6 { animation-delay: 8.00s;   }

        /* Trajectories: phone -> floating upward, slight left/right */
        .emit-card.it-1 { --move-x:  120px; --move-y: -170px; }
        .emit-card.it-2 { --move-x: -110px; --move-y: -180px; }
        .emit-card.it-3 { --move-x:  130px; --move-y: -170px; }
        .emit-card.it-4 { --move-x: -120px; --move-y: -180px; }
        .emit-card.it-5 { --move-x:  110px; --move-y: -200px; }
        .emit-card.it-6 { --move-x: -115px; --move-y: -190px; }

        @keyframes emitCycle {

            0% {
                opacity: 0;

                transform: translate(-50%, -50%) scale(0.5);
            }

            6% {
                opacity: 0.7;

                transform: translate(-50%, -50%) scale(0.85);
            }

            11% {
                opacity: 1;

                transform: translate(-50%, -50%) scale(1);
            }

            38% {
                opacity: 1;

                transform: translate(
                    calc(-50% + var(--move-x) * 0.4),
                    calc(-50% + var(--move-y) * 0.4)
                ) scale(1);
            }

            65% {
                opacity: 1;

                transform: translate(
                    calc(-50% + var(--move-x) * 0.92),
                    calc(-50% + var(--move-y) * 0.92)
                ) scale(1);
            }

            84% {
                opacity: 0.75;

                transform: translate(
                    calc(-50% + var(--move-x)),
                    calc(-50% + var(--move-y))
                ) scale(0.98);
            }

            100% {
                opacity: 0;

                transform: translate(
                    calc(-50% + var(--move-x) * 1.12),
                    calc(-50% + var(--move-y) * 1.12)
                ) scale(0.9);
            }

        }


        /* =========================================
           RIGHT FORM CARD
        ========================================= */

        .signup-card {
            width: 420px;
            height: 100%;

            position: absolute;

            right: -1px;
            top: 50%;

            transform: translateY(-50%);

            background: #067b82;


            padding: 32px 40px 30px;

            color: white;

            box-shadow:
                0 20px 45px rgba(0, 0, 0, 0.18);

            z-index: 5;
        }


        /* =========================================
           TITLE
        ========================================= */

        .title {
            display: flex;
            align-items: center;
            gap: 9px;

            margin-bottom: 4px;
        }

        .yellow-dot {
            width: 14px;
            height: 14px;

            background: #ffe72e;

            border-radius: 50%;

            flex-shrink: 0;
        }

        .title h1 {
            font-size: 24px;
            font-weight: 600;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 13.5px;
            line-height: 1.6;

            color: rgba(255,255,255,0.95);

            margin: 7px 0 14px;
        }


        /* =========================================
           ERROR BOX
        ========================================= */

        .error-box {
            background: rgba(255, 255, 255, 0.12);
            border-left: 3px solid #ffe72e;

            color: #fff;

            font-size: 13px;

            line-height: 1.5;

            padding: 8px 12px;

            border-radius: 8px;

            margin-bottom: 12px;
        }


        /* =========================================
           INPUTS
        ========================================= */

        .input-group {
            position: relative;
            margin-bottom: 9px;
        }

        .input-group input {
            width: 100%;

            height: 45px;

            border: none;
            outline: none;

            border-radius: 25px;

            background: #ffffff;

            padding: 0 22px;

            font-family: "Poppins", sans-serif;

            font-size: 14px;

            color: #174f68;

            transition: 0.25s ease;
        }

        .input-group input::placeholder {
            color: #17536e;
            opacity: 1;
        }

        .input-group input:focus {
            box-shadow:
                0 0 0 3px rgba(255, 231, 46, 0.45);
        }

        .input-group .field-error {
            display: block;

            color: #ffe72e;

            font-size: 11.5px;

            margin: 3px 14px 0;

            line-height: 1.4;
        }


        /* =========================================
           PASSWORD
        ========================================= */

        .password-wrapper input {
            padding-right: 48px;
        }

        .password-toggle {
            position: absolute;

            right: 17px;
            top: 50%;

            transform: translateY(-50%);

            border: none;
            background: transparent;

            color: #075b82;

            cursor: pointer;

            font-size: 14px;
        }


        /* =========================================
           TERMS
        ========================================= */

        .terms {
            display: flex;
            align-items: flex-start;

            gap: 8px;

            margin: 9px 0 13px;

            font-size: 12.5px;
            line-height: 1.5;

            color: #fff;
        }

        .terms input {
            appearance: none;

            width: 16px;
            height: 16px;

            border: 3px solid white;

            border-radius: 50%;

            flex-shrink: 0;

            margin-top: 1px;

            cursor: pointer;

            position: relative;
        }

        .terms input:checked {
            background: #dde756;

            border-color: #ffe72e;
        }

        .terms input:checked::after {
            content: "";

            width: 5px;
            height: 5px;

            background: #075b82;

            border-radius: 50%;

            position: absolute;

            top: 50%;
            left: 50%;

            transform: translate(-50%, -50%);
        }

        .terms a {
            color: #ffe72e;

            text-decoration: none;

            font-weight: 500;
        }

        .terms a:hover {
            text-decoration: underline;
        }


        /* =========================================
           DIVIDER
        ========================================= */

        .divider {
            display: flex;
            align-items: center;

            gap: 10px;

            margin: 5px 0 13px;
        }

        .divider::before,
        .divider::after {
            content: "";

            height: 1px;

            flex: 1;

            background: #ffe72e;
        }

        .divider span {
            width: 31px;
            height: 31px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #ffe72e;

            color: #075b82;

            border-radius: 50%;

            font-size: 12px;

            font-weight: 600;
        }


        /* =========================================
           SOCIAL TITLE
        ========================================= */

        .social-title {
            display: flex;
            align-items: center;

            gap: 9px;

            margin-bottom: 11px;
        }

        .social-title h2 {
            font-size: 20px;
            font-weight: 600;
        }


        /* =========================================
           SOCIAL BUTTONS
        ========================================= */

        .social-buttons {
            display: flex;
            gap: 12px;

            margin-bottom: 12px;
        }

        .social-btn {
            flex: 1;

            height: 42px;

            border: none;

            border-radius: 24px;

            background: white;

            color: #075b82;

            font-family: "Poppins", sans-serif;

            font-size: 13px;

            font-weight: 500;

            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 8px;

            text-decoration: none;

            transition: all 0.25s ease;
        }

        .social-btn i {
            font-size: 18px;
        }

        .social-btn.facebook i {
            color: #1877f2;
        }

        .social-btn.google i {
            color: #4285f4;
        }

        .social-btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 7px 16px rgba(0,0,0,0.15);
        }


        /* =========================================
           LOGIN TEXT
        ========================================= */

        .login-text {
            text-align: center;

            font-size: 13.5px;

            margin: 8px 0 12px;

            color: white;
        }

        .login-text a {
            color: #ffe72e;

            text-decoration: none;

            font-weight: 600;
        }

        .login-text a:hover {
            text-decoration: underline;
        }


        /* =========================================
           CREATE ACCOUNT BUTTON
        ========================================= */

        .create-btn {
            width: 100%;

            height: 45px;

            border: none;

            border-radius: 25px;

            background: #ffe72e;

            color: #075b82;

            font-family: "Poppins", sans-serif;

            font-size: 14.5px;

            font-weight: 600;

            cursor: pointer;

            transition: all 0.25s ease;
        }

        .create-btn:hover {
            background: #fff25f;

            transform: translateY(-2px);

            box-shadow:
                0 8px 18px rgba(0,0,0,0.15);
        }

        .create-btn:active {
            transform: translateY(0);
        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 900px) {

            body {
                padding: 25px 15px;
            }

            .signup-wrapper {
                max-width: 600px;

                min-height: auto;

                display: block;

                background:
                    linear-gradient(
                        180deg,
                        #E6EFE2 0%,
                        #E1EEE2 100%,
                        #DBECE2 50%
                    );
            }

            .signup-visual {
                width: 100%;

                height: 300px;

                min-height: 300px;
            }

            /* Keep phone source near center on the tablet image */
            .phone-source,
            .emit-card {
                left: 50%;
                top: 48%;
            }

            .emit-card.it-1 { --move-x:  70px;  --move-y: -110px; }
            .emit-card.it-2 { --move-x: -65px;  --move-y: -115px; }
            .emit-card.it-3 { --move-x:  72px;  --move-y: -105px; }
            .emit-card.it-4 { --move-x: -70px;  --move-y: -112px; }
            .emit-card.it-5 { --move-x:  64px;  --move-y: -120px; }
            .emit-card.it-6 { --move-x: -68px;  --move-y: -112px; }

            .emit-card {
                font-size: 11.5px;
            }

            .emit-card.welcome {
                font-size: 12.5px;
            }

            .signup-card {
                position: relative;

                width: calc(100% - 30px);

                max-width: 420px;

                left: auto;
                right: auto;
                top: auto;

                transform: none;

                margin: -70px auto 30px;
            }
        }


        @media (max-width: 500px) {

            body {
                padding: 0;

                background: #eef1e7;
            }

            .signup-wrapper {
                width: 100%;

                min-height: 100vh;

                border: none;

                border-radius: 0;

                box-shadow: none;
            }

            .signup-visual {
                height: 240px;

                min-height: 240px;
            }

            /* Even more subtle on phones so cards never overflow */
            .phone-source,
            .emit-card {
                left: 52%;
                top: 48%;
            }

            .emit-card.it-1 { --move-x:  46px;  --move-y: -80px; }
            .emit-card.it-2 { --move-x: -44px;  --move-y: -84px; }
            .emit-card.it-3 { --move-x:  48px;  --move-y: -78px; }
            .emit-card.it-4 { --move-x: -46px;  --move-y: -82px; }
            .emit-card.it-5 { --move-x:  42px;  --move-y: -86px; }
            .emit-card.it-6 { --move-x: -44px;  --move-y: -82px; }

            .emit-card {
                font-size: 10.5px;

                padding: 7px 12px;
            }

            .emit-card.welcome {
                font-size: 11.5px;

                padding: 9px 14px;
            }

            .signup-card {
                width: calc(100% - 24px);

                padding: 27px 22px 25px;

                border-radius: 24px;

                margin-top: -45px;
            }

            .title h1 {
                font-size: 22px;
            }

            .subtitle {
                font-size: 13px;
            }

            .social-buttons {
                gap: 8px;
            }

            .social-btn {
                font-size: 12.5px;
            }
        }

    </style>
</head>

<body>

<div class="signup-wrapper">

    <!-- =========================================
         RIGHT IMAGE
    ========================================== -->

    <div class="signup-visual">

        <!-- Replace this image with your own shopping image -->
        <img src="{{ asset('storage/banners/person.png') }}" alt="Shopping">

        <div class="visual-overlay"></div>

        {{-- Anchor — adjust left/top in .phone-source CSS if the phone is elsewhere in the image --}}
        <div class="phone-source"></div>

        <div class="emit-card welcome it-1">
            <div class="ec-title">🎁 WELCOME OFFER</div>
            <div class="ec-sub">Get 10% OFF your first order</div>
        </div>

        <div class="emit-card it-2">
            <div class="ec-title">
                <i class="fa-solid fa-check ec-check"></i>
                Exclusive deals
            </div>
        </div>

        <div class="emit-card it-3">
            <div class="ec-title">
                <i class="fa-solid fa-check ec-check"></i>
                Order tracking
            </div>
        </div>

        <div class="emit-card it-4">
            <div class="ec-title">
                <i class="fa-solid fa-check ec-check"></i>
                Faster checkout
            </div>
        </div>

        <div class="emit-card it-5">
            <div class="ec-title">
                <i class="fa-solid fa-check ec-check"></i>
                Wishlist
            </div>
        </div>

        <div class="emit-card it-6">
            <div class="ec-title">🎟 Coupon</div>
            <div class="ec-sub"><span class="ec-coupon">WELCOME10</span></div>
        </div>

    </div>


    <!-- =========================================
         SIGNUP CARD (left)
    ========================================== -->

    <div class="signup-card">

        <!-- Title -->
        <div class="title">

            <span class="yellow-dot"></span>

            <h1>Sign Up</h1>

        </div>


        <!-- Subtitle -->
        <p class="subtitle">
            Sign up to create an account and enjoy exclusive
            shopping benefits.
        </p>


        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif


        <form method="POST" action="{{ route('register', ['redirect' => request('redirect')]) }}">
            @csrf

            @if (request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            <!-- Full Name -->
            <div class="input-group">

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Full Name"
                    autocomplete="given-name"
                >

                @error('name')
                    <span class="field-error">{{ $message }}</span>
                @enderror

            </div>


            <!-- Mobile Number -->
            <div class="input-group">

                <input
                    type="tel"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="Mobile Number"
                    autocomplete="tel"
                    required
                    autofocus
                >

                @error('phone')
                    <span class="field-error">{{ $message }}</span>
                @enderror

            </div>


            <!-- Email (optional) -->
            <div class="input-group">

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Email Address (optional)"
                    autocomplete="username"
                >

                @error('email')
                    <span class="field-error">{{ $message }}</span>
                @enderror

            </div>


            <!-- Password -->
            <div class="input-group password-wrapper">

                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Password"
                    autocomplete="new-password"
                    required
                >

                <button
                    type="button"
                    class="password-toggle"
                    onclick="togglePassword()"
                    aria-label="Show password"
                >
                    <i class="fa-regular fa-eye"></i>
                </button>

                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror

            </div>


            <!-- Confirm Password -->
            <div class="input-group password-wrapper">

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm Password"
                    autocomplete="new-password"
                    required
                >

                <button
                    type="button"
                    class="password-toggle"
                    onclick="toggleConfirmPassword()"
                    aria-label="Show confirm password"
                >
                    <i class="fa-regular fa-eye"></i>
                </button>

                @error('password_confirmation')
                    <span class="field-error">{{ $message }}</span>
                @enderror

            </div>


            <!-- Terms -->
            <label class="terms">

                <input
                    type="checkbox"
                    id="terms"
                >

                <span>
                    By creating account, you agree to accept our
                    <a href="#">Privacy Policy</a>
                    and
                    <a href="#">Terms of Services</a>
                </span>

            </label>


            <!-- Divider -->
            <div class="divider">

                <span>OR</span>

            </div>


            <!-- Social Login -->
            @if (! empty($storeSettings['google_oauth_client_id']) || ! empty($storeSettings['facebook_oauth_client_id']))
                <div class="social-title">

                    <span class="yellow-dot"></span>

                    <h2>Sign up with</h2>

                </div>


                <div class="social-buttons">

                    @if (! empty($storeSettings['facebook_oauth_client_id']))
                        <a
                            href="{{ route('social.redirect', ['provider' => 'facebook', 'redirect' => request('redirect')]) }}"
                            class="social-btn facebook"
                        >

                            <i class="fa-brands fa-facebook"></i>

                            <span>Facebook</span>

                        </a>
                    @endif


                    @if (! empty($storeSettings['google_oauth_client_id']))
                        <a
                            href="{{ route('social.redirect', ['provider' => 'google', 'redirect' => request('redirect')]) }}"
                            class="social-btn google"
                        >

                            <i class="fa-brands fa-google"></i>

                            <span>Google</span>

                        </a>
                    @endif

                </div>
            @endif


            <!-- Login -->
            <p class="login-text">

                Already have an account?

                <a href="{{ route('login', ['redirect' => request('redirect')]) }}">
                    Log in
                </a>

            </p>


            <!-- Create Account -->
            <button
                type="submit"
                class="create-btn"
                onclick="validateTerms(event)"
            >

                CREATE ACCOUNT

            </button>

        </form>

    </div>

</div>


<script>

    /* =========================================
       PASSWORD SHOW / HIDE
    ========================================= */

    function togglePassword() {

        const password =
            document.getElementById("password");

        const button =
            password.parentElement.querySelector(".password-toggle i");

        if (password.type === "password") {

            password.type = "text";

            button.classList.remove("fa-eye");

            button.classList.add("fa-eye-slash");

        } else {

            password.type = "password";

            button.classList.remove("fa-eye-slash");

            button.classList.add("fa-eye");

        }

    }

    function toggleConfirmPassword() {

        const password =
            document.getElementById("password_confirmation");

        const button =
            password.parentElement.querySelector(".password-toggle i");

        if (password.type === "password") {

            password.type = "text";

            button.classList.remove("fa-eye");

            button.classList.add("fa-eye-slash");

        } else {

            password.type = "password";

            button.classList.remove("fa-eye-slash");

            button.classList.add("fa-eye");

        }

    }


    /* =========================================
       TERMS VALIDATION
    ========================================= */

    function validateTerms(event) {

        const terms =
            document.getElementById("terms");

        if (!terms.checked) {

            event.preventDefault();

            alert(
                "Please accept the Privacy Policy and Terms of Services."
            );

            return;
        }

    }

</script>

</body>
</html>