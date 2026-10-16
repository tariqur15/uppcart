<?php

namespace App\Services;

use App\Models\Popup;
use Illuminate\Http\Request;

class PopupService
{
    public const DEVICES = [
        'all' => 'All devices',
        'desktop' => 'Desktop',
        'mobile' => 'Mobile',
        'tablet' => 'Tablet',
    ];

    public const PAGES = [
        'all' => 'All pages',
        'homepage' => 'Homepage only',
        'shop' => 'Shop page',
        'products' => 'Product pages',
        'cart' => 'Cart',
        'checkout' => 'Checkout',
        'selected' => 'Selected pages',
    ];

    public const CUSTOMERS = [
        'everyone' => 'Everyone',
        'new_visitors' => 'New visitors',
        'returning_visitors' => 'Returning visitors',
        'logged_in' => 'Logged-in customers',
        'guest' => 'Guest customers',
    ];

    public const TRIGGERS = [
        'immediately' => 'Immediately',
        'after_seconds' => 'After X seconds',
        'on_scroll' => 'On scroll',
        'exit_intent' => 'Exit intent',
        'after_add_to_cart' => 'After adding to cart',
    ];

    public const FREQUENCIES = [
        'every_visit' => 'Show every visit',
        'once_per_session' => 'Show once per session',
        'once_per_day' => 'Show once per day',
        'once_per_week' => 'Show once per week',
        'once_only' => 'Show only once',
    ];

    public const IMAGE_POSITIONS = [
        'top' => 'Top',
        'bottom' => 'Bottom',
        'none' => 'No image',
    ];

    public function defaults(): array
    {
        return [
            'type' => 'both',
            'title' => '',
            'message' => '',
            'image' => null,
            'image_position' => 'top',
            'button_text' => '',
            'button_url' => '',
            'button_color' => '#4f46e5',
            'button_text_color' => '#ffffff',
            'text_color' => '#111827',
            'bg_color' => '#ffffff',
            'overlay_opacity' => 60,
            'rounded' => '2xl',
            'width' => 'sm',
            'trigger' => 'after_seconds',
            'trigger_seconds' => 3,
            'scroll_percent' => 50,
            'after_pages_viewed' => 2,
            'frequency' => 'once_per_session',
            'hide_after_close' => true,
            'hide_after_click' => true,
            'show_on' => ['all'],
            'selected_pages' => [],
            'devices' => ['all'],
            'customers' => ['everyone'],
            'start' => '',
            'end' => '',
        ];
    }

    /**
     * All active popups that should render on the current request, newest first.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\Popup>
     */
    public function activePopups(Request $request)
    {
        $now = now();

        return Popup::orderBy('sort_order')->orderByDesc('id')
            ->get()
            ->filter(fn (Popup $popup) => $popup->is_active)
            ->filter(fn (Popup $popup) => $this->isScheduled($popup, $now))
            ->filter(fn (Popup $popup) => $this->shouldDisplay($popup, $request))
            ->values();
    }

    public function config(Popup $popup): array
    {
        return array_merge($this->defaults(), $popup->getConfig());
    }

    public function isScheduled(Popup $popup, ?\Illuminate\Support\Carbon $now = null): bool
    {
        $now = $now ?? now();
        $config = $this->config($popup);

        if (! empty($config['start']) && $now->lt($config['start'])) {
            return false;
        }

        if (! empty($config['end']) && $now->gt($config['end'])) {
            return false;
        }

        return true;
    }

    public function shouldDisplay(Popup $popup, Request $request): bool
    {
        $config = $this->config($popup);

        if (! $this->isOnDevice($config, $request)) {
            return false;
        }

        if (! $this->isForCustomer($config)) {
            return false;
        }

        return $this->isOnPage($config, $request);
    }

    private function isOnDevice(array $config, Request $request): bool
    {
        $devices = $config['devices'] ?? ['all'];

        if (in_array('all', $devices, true)) {
            return true;
        }

        $agent = $request->userAgent() ?: '';

        return match (true) {
            str_contains($agent, 'Android') || str_contains($agent, 'iPhone') => in_array('mobile', $devices, true),
            preg_match('/iPad|Tablet|Silk/', $agent) === 1 => in_array('tablet', $devices, true),
            default => in_array('desktop', $devices, true),
        };
    }

    private function isForCustomer(array $config): bool
    {
        $customers = $config['customers'] ?? ['everyone'];

        if (in_array('everyone', $customers, true)) {
            return true;
        }

        $isLoggedIn = auth()->check();

        return match (true) {
            in_array('guest', $customers, true) => ! $isLoggedIn,
            in_array('logged_in', $customers, true) => $isLoggedIn,
            in_array('new_visitors', $customers, true) => ! request()->cookie('storefront_visited_before'),
            in_array('returning_visitors', $customers, true) => (bool) request()->cookie('storefront_visited_before'),
            default => true,
        };
    }

    private function isOnPage(array $config, Request $request): bool
    {
        $pages = $config['show_on'] ?? ['all'];

        if (in_array('all', $pages, true)) {
            return true;
        }

        if (in_array('selected', $pages, true)) {
            $selected = array_map(fn ($p) => trim(parse_url($p, PHP_URL_PATH) ?: $p, '/'), $config['selected_pages'] ?? []);
            $current = trim(parse_url($request->path(), PHP_URL_PATH), '/');

            return in_array($current, $selected, true) || in_array($request->fullUrl(), $config['selected_pages'] ?? [], true);
        }

        return in_array($this->currentPage($request), $pages, true);
    }

    private function currentPage(Request $request): string
    {
        if ($request->routeIs('home')) {
            return 'homepage';
        }

        if ($request->routeIs('products.index', 'shop')) {
            return 'shop';
        }

        if ($request->routeIs('products.show')) {
            return 'products';
        }

        if ($request->routeIs('cart.index')) {
            return 'cart';
        }

        if ($request->routeIs('checkout.index')) {
            return 'checkout';
        }

        return 'other';
    }

    public function device(Request $request): string
    {
        $agent = $request->userAgent() ?: '';

        if (str_contains($agent, 'Android') || str_contains($agent, 'iPhone')) {
            return 'mobile';
        }

        if (preg_match('/iPad|Tablet|Silk/', $agent) === 1) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Build a normalized view model used by the storefront renderer.
     *
     * @return array<string, mixed>
     */
    public function viewModel(Popup $popup): array
    {
        $config = $this->config($popup);
        $config['image_url'] = $config['image'] ? \Illuminate\Support\Facades\Storage::url($config['image']) : null;

        return $config;
    }
}
