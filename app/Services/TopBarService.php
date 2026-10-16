<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Request;

class TopBarService
{
    public const TYPES = [
        'announcement' => 'Announcement',
        'promotional' => 'Promotional Offer',
        'free_shipping' => 'Free Shipping',
        'discount' => 'Discount / Coupon',
        'countdown' => 'Countdown',
        'custom' => 'Custom',
    ];

    public const ANIMATIONS = [
        'static' => 'Static',
        'sliding' => 'Sliding Text',
        'marquee' => 'Marquee',
        'fade' => 'Fade',
        'typewriter' => 'Typewriter',
    ];

    /**
     * All configured top bars, newest first (index 0 = newest).
     *
     * @return array<int, array<string, mixed>>
     */
    public function bars(): array
    {
        $raw = Setting::get('top_bars');

        if (! $raw) {
            return [];
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            return [];
        }

        $bars = array_values(array_filter($decoded, fn ($bar) => is_array($bar)));

        // Newest on top: items with the highest sort order appear first.
        usort($bars, fn ($a, $b) => (int) ($b['sort'] ?? 0) <=> (int) ($a['sort'] ?? 0));

        return $bars;
    }

    /**
     * Bars that are enabled and currently within their scheduling window.
     *
     * @return array<int, array<string, mixed>>
     */
    public function activeBars(): array
    {
        $now = now();

        return array_values(array_filter($this->bars(), function ($bar) use ($now) {
            if (($bar['status'] ?? false) !== true) {
                return false;
            }

            if (! empty($bar['start']) && $now->lt($bar['start'])) {
                return false;
            }

            if (! empty($bar['end']) && $now->gt($bar['end'])) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Determine whether a bar should display on the current request.
     */
    public function shouldDisplay(array $bar, Request $request): bool
    {
        if (! $this->isOnDevice($bar)) {
            return false;
        }

        return $this->isOnPage($bar, $request);
    }

    private function isOnDevice(array $bar): bool
    {
        $devices = $this->arrayValue($bar, 'devices');
        $agent = request()->userAgent() ?: '';

        if (in_array('all', $devices, true)) {
            return true;
        }

        return match (true) {
            str_contains($agent, 'Android') || str_contains($agent, 'iPhone') => in_array('mobile', $devices, true),
            preg_match('/iPad|Tablet|Silk/', $agent) === 1 => in_array('tablet', $devices, true),
            default => in_array('desktop', $devices, true),
        };
    }

    private function isOnPage(array $bar, Request $request): bool
    {
        $pages = $this->arrayValue($bar, 'show_on');

        if (in_array('all', $pages, true)) {
            return true;
        }

        $page = $this->currentPage($request);

        return in_array($page, $pages, true);
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

    /**
     * Build the display message / markup pieces for a bar.
     *
     * @return array<string, mixed>
     */
    public function content(array $bar): array
    {
        $message = (string) ($bar['message'] ?? '');
        $coupon = (string) ($bar['coupon_code'] ?? '');

        if ($coupon !== '' && $bar['type'] === 'discount' && str_contains($message, '{coupon}')) {
            $message = str_replace('{coupon}', $coupon, $message);
        }

        return [
            'message' => $message,
            'highlight' => (string) ($bar['highlight_text'] ?? ''),
            'button_text' => (string) ($bar['button_text'] ?? ''),
            'button_url' => (string) ($bar['button_url'] ?? ''),
            'coupon' => $coupon,
            'icon' => (string) ($bar['icon'] ?? ''),
            'countdown_enabled' => ($bar['countdown_enabled'] ?? false) === true,
            'countdown_end' => (string) ($bar['countdown_end'] ?? ''),
            'countdown_start' => (string) ($bar['countdown_start'] ?? ''),
            'expired_message' => (string) ($bar['expired_message'] ?? ''),
        ];
    }

    private function arrayValue(array $bar, string $key): array
    {
        $value = $bar[$key] ?? [];

        return is_array($value) ? $value : [];
    }
}
