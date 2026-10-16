<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO Meta --}}
        @php
            $pageTitle = $storeSettings['seo_meta_title'] ?? config('app.name', 'Store');
            $pageDesc = $storeSettings['seo_meta_description'] ?? '';
            $ogImage = $storeSettings['seo_og_image'] ?? '';
            $canonical = $storeSettings['seo_canonical_url'] ?? url()->current();
            $indexable = ($storeSettings['seo_indexable'] ?? '1') === '1';
        @endphp
        <title>{{ $pageTitle }}</title>
        @if ($pageDesc)
            <meta name="description" content="{{ $pageDesc }}">
        @endif
        @if ($canonical)
            <link rel="canonical" href="{{ $canonical }}">
        @endif
        @if (!$indexable)
            <meta name="robots" content="noindex, nofollow">
        @endif
        @if ($ogImage)
            <meta property="og:title" content="{{ $pageTitle }}">
            <meta property="og:description" content="{{ $pageDesc }}">
            <meta property="og:image" content="{{ $ogImage }}">
            <meta property="og:type" content="website">
        @endif

        {{-- Favicon --}}
        @if ($storeSettings['store_favicon'] ?? null)
            <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $storeSettings['store_favicon']) }}">
        @else
            <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Google Tag Manager --}}
        @if ($storeSettings['google_tag_manager_id'] ?? null)
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ $storeSettings["google_tag_manager_id"] }}');</script>
        @endif

        {{-- Google Analytics --}}
        @if ($storeSettings['google_analytics_id'] ?? null)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $storeSettings['google_analytics_id'] }}"></script>
            <script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '{{ $storeSettings['google_analytics_id'] }}');</script>
        @endif

        {{-- Facebook Pixel --}}
        @if ($storeSettings['facebook_pixel_id'] ?? null)
            <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '{{ $storeSettings['facebook_pixel_id'] }}');fbq('track', 'PageView');</script>
        @endif
    </head>
    <body class="font-sans antialiased" data-cart-count="{{ $cartCount ?? 0 }}">
        {{-- GTM noscript --}}
        @if ($storeSettings['google_tag_manager_id'] ?? null)
            <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $storeSettings['google_tag_manager_id'] }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        @endif

        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="py-6 sm:py-8 lg:py-10">
                <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                    <x-flash-messages />
                    {{ $slot }}
                </div>
            </main>
        </div>

        {{-- Toggle Chat --}}
        @php
            $chatWhatsappNumber = $storeSettings['chat_bubble_whatsapp_number'] ?? '';
            if (empty($chatWhatsappNumber)) {
                $chatWhatsappNumber = $storeSettings['contact_whatsapp'] ?? '';
            }
            $chatWhatsappEnabled = ($storeSettings['chat_bubble_whatsapp'] ?? '0') === '1' && !empty($chatWhatsappNumber);
            $chatMessengerEnabled = ($storeSettings['chat_bubble_messenger'] ?? '0') === '1' && !empty($storeSettings['chat_bubble_messenger_page_id']);
            $chatLiveEnabled = ($storeSettings['chat_bubble_custom'] ?? '0') === '1';
            $chatAvailable = $chatWhatsappEnabled || $chatMessengerEnabled || $chatLiveEnabled;
            $chatStoreName = $storeSettings['store_name'] ?? config('app.name', 'Store');
            $chatFaqs = json_decode($storeSettings['live_chat_faqs'] ?? '[]', true);
            if (empty($chatFaqs) || ! is_array($chatFaqs)) {
                $chatFaqs = [
                    ['q' => 'How long does delivery take?', 'a' => 'Delivery usually takes 2–4 business days inside Dhaka and 3–6 business days outside Dhaka.'],
                    ['q' => 'How can I pay?', 'a' => 'We accept Cash on Delivery (COD), bKash, Nagad, Rocket, and card payments.'],
                    ['q' => 'Can I return or exchange a product?', 'a' => 'Yes, you can return or exchange within 3 days of delivery if the product is unused and in original condition.'],
                    ['q' => 'How do I track my order?', 'a' => 'Use the "Track Order" page with your order number and phone number.'],
                    ['q' => 'Can I cancel my order?', 'a' => 'Orders can be cancelled before they are shipped. Message us and we will help you.'],
                ];
            }
        @endphp
        @if ($chatAvailable)
            {{-- Live chat panel --}}
            @if ($chatLiveEnabled)
                <div class="fixed bottom-24 right-6 z-[70] w-[340px] max-w-[calc(100vw-3rem)] h-[480px] max-h-[calc(100vh-9rem)] bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden"
                     x-data="liveChat({ storeName: @js($chatStoreName), faqs: @js($chatFaqs) })"
                     x-show="open" x-cloak
                     x-on:open-live-chat.window="openPanel()"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-95">
                    <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                        <div class="relative">
                            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 rounded-full border-2 border-indigo-600"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $chatStoreName }} Live Chat</p>
                            <p class="text-[11px] text-indigo-200">Typically replies instantly</p>
                        </div>
                        <button type="button" @click="open = false" class="p-1 hover:bg-white/10 rounded-lg transition" aria-label="Close chat">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" x-ref="chatBody">
                        <template x-for="(m, i) in messages" :key="i">
                            <div :class="m.from === 'user' ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="m.from === 'user' ? 'bg-indigo-600 text-white rounded-2xl rounded-br-md' : 'bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-bl-md'"
                                     class="max-w-[85%] px-3.5 py-2 shadow-sm">
                                    <template x-if="m.attachment">
                                        <div class="mb-1.5">
                                            <template x-if="m.attachment.mime && m.attachment.mime.startsWith('image/')">
                                                <a :href="m.attachment.url" target="_blank" rel="noopener">
                                                    <img :src="m.attachment.url" :alt="m.attachment.name" class="max-w-[200px] max-h-[160px] rounded-lg object-contain">
                                                </a>
                                            </template>
                                            <template x-if="!m.attachment.mime || !m.attachment.mime.startsWith('image/')">
                                                <a :href="m.attachment.url" target="_blank" rel="noopener"
                                                   :class="m.from === 'user' ? 'bg-indigo-500/40' : 'bg-gray-100'"
                                                   class="flex items-center gap-2.5 rounded-lg px-3 py-2">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                                    <span class="text-[13px] font-medium truncate max-w-[150px]" x-text="m.attachment.name"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    <p class="text-[13px] leading-relaxed whitespace-pre-wrap" x-show="m.text" x-text="m.text"></p>
                                    <p class="text-[10px] mt-1" :class="m.from === 'user' ? 'text-indigo-200' : 'text-gray-400'" x-text="m.time || ''"></p>
                                </div>
                            </div>
                        </template>

                        <div x-show="busy" x-cloak class="flex justify-start">
                            <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm">
                                <div class="flex gap-1.5">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.15s"></span>
                                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.3s"></span>
                                </div>
                            </div>
                        </div>

                        <div x-show="step === 'form'" x-cloak class="pt-1">
                            <form @submit.prevent="start()" class="bg-white border border-gray-200 rounded-2xl rounded-bl-md p-4 shadow-sm space-y-3">
                                <div>
                                    <label for="chat-name" class="block text-xs font-medium text-gray-600 mb-1">Your Name</label>
                                    <input type="text" id="chat-name" x-model="name" :disabled="busy"
                                           placeholder="Enter your name"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none disabled:bg-gray-100">
                                </div>
                                <div>
                                    <label for="chat-phone" class="block text-xs font-medium text-gray-600 mb-1">Your Phone Number</label>
                                    <input type="tel" id="chat-phone" x-model="phone" :disabled="busy"
                                           inputmode="numeric" pattern="[0-9+]*"
                                           placeholder="+8801XXXXXXXXX"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none disabled:bg-gray-100">
                                </div>
                                <p x-show="formError" x-cloak class="text-xs text-red-500" x-text="formError"></p>
                                <button type="submit" :disabled="busy"
                                        class="w-full text-center text-sm font-semibold bg-indigo-600 text-white rounded-xl py-2.5 hover:bg-indigo-700 transition disabled:opacity-50">
                                    Start Chat
                                </button>
                            </form>
                        </div>

                        <div x-show="step === 'faq'" x-cloak class="pt-1">
                            <p class="text-[11px] text-gray-500 mb-2">Common questions:</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="f in faqs" :key="f.q">
                                    <button type="button" @click="pickFaq(f)"
                                            class="text-[11px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1.5 hover:bg-indigo-100 transition text-left">
                                        <span x-text="f.q"></span>
                                    </button>
                                </template>
                            </div>
                            <button type="button" @click="connect()" :disabled="busy"
                                    class="mt-3 w-full text-center text-sm font-semibold bg-indigo-600 text-white rounded-xl py-2.5 hover:bg-indigo-700 transition disabled:opacity-50">
                                Connect me with a customer agent
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 p-3 bg-white" x-show="step !== 'form'">
                        <form @submit.prevent="submit()" class="flex items-end gap-2">
                            <label for="chat-attachment" class="shrink-0 flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 transition cursor-pointer" title="Attach a file">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                            </label>
                            <input type="file" id="chat-attachment" class="hidden" @change="pickFile($event)">
                            <input type="text" x-model="input" :disabled="sending || busy"
                                   placeholder="Type a message..."
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none disabled:bg-gray-100">
                            <button type="button" @click="submit()" :disabled="sending || busy"
                                    class="shrink-0 flex items-center justify-center w-10 h-10 bg-indigo-600 rounded-lg text-white hover:bg-indigo-700 transition disabled:opacity-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            </button>
                        </form>
                        <template x-if="file">
                            <div class="flex items-center gap-2 mt-2 bg-gray-100 rounded-lg px-3 py-2">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-500 shrink-0"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                <span class="text-xs font-medium text-gray-700 truncate max-w-[220px]" x-text="file.name"></span>
                                <button type="button" @click="file = null" class="ml-auto text-gray-400 hover:text-red-500 transition-colors shrink-0" title="Remove attachment">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            @endif

            <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3" x-data="{ open: false }" @click.outside="open = false">
                <div class="flex flex-col gap-3 transition-all duration-300"
                     :class="open ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4 pointer-events-none'">
                    @if ($chatWhatsappEnabled)
                        <a href="{{ \App\Helpers\PhoneHelper::whatsappLink($chatWhatsappNumber, $storeSettings['timezone'] ?? null) }}?text={{ rawurlencode('Hello! I would like to know more about your products.') }}"
                           target="_blank" rel="noopener noreferrer"
                           class="relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-full shadow-2xl hover:scale-110 hover:-translate-y-1 transition-all duration-300"
                           title="Chat on WhatsApp">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white animate-pulse"></span>
                        </a>
                    @endif
                    @if ($chatMessengerEnabled)
                        <a href="https://m.me/{{ $storeSettings['chat_bubble_messenger_page_id'] }}"
                           target="_blank" rel="noopener noreferrer"
                           class="relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full shadow-2xl hover:scale-110 hover:-translate-y-1 transition-all duration-300"
                           title="Chat on Messenger">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 2.143.562 4.15 1.546 5.89L0 24l6.34-1.508A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm6.274 15.2c-.258.726-1.536 1.39-2.16 1.44-.538.044-.624.42-3.82-.816-3.198-1.236-5.054-4.43-5.208-4.634-.152-.204-1.24-1.65-1.24-3.148 0-1.498.784-2.234 1.064-2.542.28-.308.614-.384.818-.384.204 0 .408 0 .588.01.188.01.44-.07.688.528.258.616 1.008 2.05 1.076 2.198.07.148.116.322.022.52-.094.198-.14.32-.282.494-.14.174-.296.388-.422.518-.14.14-.286.292-.122.574.164.282.73 1.202 1.564 1.948 1.076.96 1.982 1.258 2.264 1.398.282.14.446.116.61-.07.164-.174.706-.824.894-1.106.188-.282.376-.234.634-.14.258.094 1.64.774 1.922.914.282.14.47.21.54.328.068.116.068.674-.19 1.4z"/></svg>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-blue-400 rounded-full border-2 border-white animate-pulse"></span>
                        </a>
                    @endif
                    @if ($chatLiveEnabled)
                        <button type="button"
                                @click="open = false; window.dispatchEvent(new CustomEvent('open-live-chat'))"
                                class="relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-slate-500 to-slate-700 rounded-full shadow-2xl hover:scale-110 hover:-translate-y-1 transition-all duration-300"
                                title="Live Chat">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-slate-400 rounded-full border-2 border-white animate-pulse"></span>
                        </button>
                    @endif
                </div>

                <button type="button" @click="open = !open"
                        class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-full shadow-2xl hover:scale-110 transition-all duration-300"
                        :class="open ? 'rotate-45' : 'rotate-0'"
                        title="Toggle Chat">
                    <svg x-show="open" x-cloak class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    <span x-show="!open" class="relative">
                        @if ($chatWhatsappEnabled)
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        @elseif ($chatMessengerEnabled)
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 2.143.562 4.15 1.546 5.89L0 24l6.34-1.508A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm6.274 15.2c-.258.726-1.536 1.39-2.16 1.44-.538.044-.624.42-3.82-.816-3.198-1.236-5.054-4.43-5.208-4.634-.152-.204-1.24-1.65-1.24-3.148 0-1.498.784-2.234 1.064-2.542.28-.308.614-.384.818-.384.204 0 .408 0 .588.01.188.01.44-.07.688.528.258.616 1.008 2.05 1.076 2.198.07.148.116.322.022.52-.094.198-.14.32-.282.494-.14.174-.296.388-.422.518-.14.14-.286.292-.122.574.164.282.73 1.202 1.564 1.948 1.076.96 1.982 1.258 2.264 1.398.282.14.446.116.61-.07.164-.174.706-.824.894-1.106.188-.282.376-.234.634-.14.258.094 1.64.774 1.922.914.282.14.47.21.54.328.068.116.068.674-.19 1.4z"/></svg>
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                        @endif
                        @if ($chatMessengerEnabled && $chatWhatsappEnabled)
                            <svg class="w-4 h-4 text-white absolute -bottom-1 -right-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 2.143.562 4.15 1.546 5.89L0 24l6.34-1.508A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm6.274 15.2c-.258.726-1.536 1.39-2.16 1.44-.538.044-.624.42-3.82-.816-3.198-1.236-5.054-4.43-5.208-4.634-.152-.204-1.24-1.65-1.24-3.148 0-1.498.784-2.234 1.064-2.542.28-.308.614-.384.818-.384.204 0 .408 0 .588.01.188.01.44-.07.688.528.258.616 1.008 2.05 1.076 2.198.07.148.116.322.022.52-.094.198-.14.32-.282.494-.14.174-.296.388-.422.518-.14.14-.286.292-.122.574.164.282.73 1.202 1.564 1.948 1.076.96 1.982 1.258 2.264 1.398.282.14.446.116.61-.07.164-.174.706-.824.894-1.106.188-.282.376-.234.634-.14.258.094 1.64.774 1.922.914.282.14.47.21.54.328.068.116.068.674-.19 1.4z"/></svg>
                        @endif
                    </span>
                </button>
            </div>
        @endif

        {{-- Global Variant Modal --}}
        <div x-data="variantModal()" x-cloak>
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-50" @click="showModal = false"></div>
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.stop>
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Select Options</h3>
                        <button @click="showModal = false" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="px-5 py-5 max-h-[60vh] overflow-y-auto">
                        <div class="flex gap-4 mb-5">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                <img x-ref="modalImage" :src="activeImage()" class="w-full h-full object-cover" alt="">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 line-clamp-2" x-text="productName"></h4>
                                <div class="mt-1">
                                    <span class="text-xl font-bold text-[#5c00c6]" x-text="'&#2547; ' + parseFloat(activePrice()).toFixed(2)"></span>
                                </div>
                                <div class="mt-1">
                                    <span x-show="activeStock() > 0" class="text-sm text-green-600 font-medium">In Stock (<span x-text="activeStock()"></span> available)</span>
                                    <span x-show="activeStock() <= 0" class="text-sm text-red-500 font-medium">Out of Stock</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <template x-for="(option, oi) in variantOptions" :key="oi">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2" x-text="option.name"></label>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="(value, vi) in option.values" :key="vi">
                                            <button type="button" @click="selectOption(option.name, value)" :class="oi === 0 &amp;&amp; getValueImage(oi, value) ? 'flex flex-col items-center gap-1.5 transition-all duration-200' : 'px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 ' + (isSelected(option.name, value) ? 'bg-[#5c00c6] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200')">
                                                <template x-if="oi === 0 &amp;&amp; getValueImage(oi, value)">
                                                    <span class="flex flex-col items-center gap-1.5">
                                                        <span class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 transition-all duration-200" :class="isSelected(option.name, value) ? 'ring-2 ring-[#5c00c6] ring-offset-2' : 'ring-1 ring-gray-200'">
                                                            <img :src="getValueImage(oi, value)" class="w-full h-full object-cover" />
                                                        </span>
                                                        <span class="text-xs font-medium transition-colors duration-200" :class="isSelected(option.name, value) ? 'text-[#5c00c6]' : 'text-gray-600'" x-text="value"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!(oi === 0 &amp;&amp; getValueImage(oi, value))">
                                                    <span x-text="value"></span>
                                                </template>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="mt-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <input type="number" x-model.number="qty" min="1" :max="activeStock()" class="w-16 text-center border border-gray-200 rounded-lg py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#5c00c6]">
                                <button type="button" @click="qty = Math.min(activeStock(), qty + 1)" class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex gap-3">
                        <button @click="addToCart()" :disabled="!canAddToCart() || loading" class="flex-1 py-3 rounded-xl font-semibold text-sm transition-colors bg-gray-900 text-white hover:bg-gray-800 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            <span x-text="loading ? 'Adding...' : 'Add to Cart'"></span>
                        </button>
                        <button @click="buyNow()" :disabled="!canAddToCart() || loading" class="flex-1 py-3 rounded-xl font-semibold text-sm transition-colors bg-emerald-600 text-white hover:bg-emerald-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span x-text="loading ? 'Processing...' : 'Buy Now'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast notification --}}
        <div x-data="{ show: false, message: '', type: 'success' }"
             x-init="
                window.addEventListener('toast', (e) => {
                    message = e.detail.message;
                    type = e.detail.type || 'success';
                    show = true;
                    setTimeout(() => { show = false }, 3000);
                });
             "
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             x-cloak
             class="fixed bottom-4 right-4 z-50 max-w-sm"
             role="alert">
            <div class="flex items-center gap-3 bg-white rounded-xl shadow-lg border border-gray-200 px-5 py-4"
                 :class="type === 'success' ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500'">
                <template x-if="type === 'success'">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="type !== 'success'">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
                <p class="text-sm font-medium text-gray-800" x-text="message"></p>
                <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ===== Storefront popups ===== --}}
        @if (! request()->routeIs('orders.show'))
            @foreach ($storePopups as $storePopup)
                @include('layouts.partials.popup-modal', [
                    'id' => $storePopup['id'],
                    'config' => $storePopup['config'],
                ])
            @endforeach
        @endif

        {{-- ===== Fake order notifications ===== --}}
        @php
            $fakeOrderPosition = $fakeOrdersSettings['position'] ?? 'bottom-left';
            $fakePositionClasses = [
                'bottom-left' => 'bottom-5 left-5',
                'bottom-right' => 'bottom-5 right-5',
                'top-left' => 'top-5 left-5',
                'top-right' => 'top-5 right-5',
            ];
            $fakeOrderPositionClass = $fakePositionClasses[$fakeOrderPosition] ?? $fakePositionClasses['bottom-left'];

            $fakeOrderSelectedPages = array_filter(array_map('trim', explode(',', $fakeOrdersSettings['pages'] ?? 'all')));
            $fakeOrderCurrentPages = collect(['home', 'products', 'product', 'cart', 'checkout'])
                ->filter(fn ($key) => match ($key) {
                    'home' => request()->routeIs('home'),
                    'products' => request()->routeIs('products.index'),
                    'product' => request()->routeIs('products.show'),
                    'cart' => request()->routeIs('cart.index'),
                    'checkout' => request()->routeIs('checkout.*'),
                    default => false,
                })
                ->all();
            $showFakeOrdersOnPage = in_array('all', $fakeOrderSelectedPages, true)
                || array_intersect($fakeOrderSelectedPages, $fakeOrderCurrentPages) !== [];
        @endphp
        @if (($fakeOrdersSettings['enabled'] ?? false) && $showFakeOrdersOnPage && ! request()->routeIs('orders.show'))
            <input type="hidden" id="fake-order-interval" value="{{ $fakeOrdersSettings['interval_seconds'] ?? 20 }}">
            <div x-data="fakeOrders({{ json_encode(['template' => $fakeOrdersSettings['message'] ?? '']) }})" x-init="start()"
                 x-show="visible"
                 x-cloak
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-x-6"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-6"
                 class="fixed {{ $fakeOrderPositionClass }} z-[75] max-w-[320px] bg-white rounded-xl shadow-2xl border border-gray-100 p-3.5 flex items-start gap-3"
                 @click="visible = false">
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[12.5px] text-gray-700 font-medium leading-snug" x-text="message"></p>
                    <p class="text-[11px] text-gray-400 mt-1" x-text="'Just now'"></p>
                </div>
            </div>
        @endif

        @stack('scripts')
        @stack('storefront-scripts')
    </body>
</html>
