<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class AbandonedCartService
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function track(?Request $request = null, ?string $name = null, ?string $phone = null, ?string $address = null): void
    {
        if (! $phone) {
            return;
        }

        $items = $this->cartService->getItems();

        $userId = auth()->id() ?? session('live_chat_user_id');
        $deviceId = null;

        if (! $userId) {
            $deviceId = $this->resolveDeviceId($request);
        }

        $record = AbandonedCart::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('device_id', $deviceId))
            ->first();

        if ($items->isEmpty()) {
            $record?->delete();

            return;
        }

        if (! $userId && ! $deviceId) {
            return;
        }

        $snapshot = $items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'name' => $item->product?->name,
            'image' => $item->product?->image,
            'quantity' => $item->quantity ?? $item->qty,
            'variant' => $item->variant ?? null,
            'subtotal' => $this->cartService->itemSubtotal($item),
        ]);

        $data = [
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
            'items_data' => $snapshot,
            'total' => $snapshot->sum('subtotal'),
            'item_count' => $snapshot->sum('quantity'),
            'last_activity_at' => now(),
            'recovered_at' => null,
        ];

        if ($record) {
            $record->update($data);

            $this->ensureCoupon($record);

            return;
        }

        $record = AbandonedCart::create([
            ...$data,
            'user_id' => $userId,
            'device_id' => $deviceId,
        ]);

        $this->ensureCoupon($record);
    }

    public function generateCoupon(AbandonedCart $cart): Coupon
    {
        return Coupon::create([
            'title' => "5% off — Abandoned cart #{$cart->id}",
            'code' => 'SAVE5-'.strtoupper(Str::random(6)),
            'type' => 'percentage',
            'value' => 5,
            'min_order_amount' => null,
            'max_uses' => 1,
            'starts_at' => now(),
            'expires_at' => now()->addDays(7),
            'is_active' => true,
        ]);
    }

    public function ensureCoupon(AbandonedCart $cart): void
    {
        if ($cart->coupon_code && Coupon::where('code', $cart->coupon_code)->exists()) {
            return;
        }

        $coupon = $this->generateCoupon($cart);

        $cart->update(['coupon_code' => $coupon->code]);
    }

    public const REMINDER_TYPES = ['reminder', 'stock', 'discount'];

    public function recordReminder(AbandonedCart $cart, string $type): void
    {
        abort_unless(in_array($type, self::REMINDER_TYPES, true), 422, 'Invalid reminder type.');

        $cart->update([
            'reminder_type' => $type,
            'last_reminder_sent_at' => now(),
            'reminder_sent_count' => $cart->reminder_sent_count + 1,
        ]);
    }

    public function toggleIgnore(AbandonedCart $cart): void
    {
        $cart->update([
            'ignored_at' => $cart->ignored_at ? null : now(),
        ]);
    }

    public function markRecovered(?Request $request = null, ?int $userId = null): void
    {
        $userIds = array_values(array_unique(array_filter([
            $userId,
            auth()->id(),
        ])));

        $deviceId = $request?->cookie('device_id');
        $restoredCartId = session('restored_abandoned_cart_id');

        if (empty($userIds) && ! $deviceId && ! $restoredCartId) {
            return;
        }

        AbandonedCart::query()
            ->when($userIds, fn ($q) => $q->whereIn('user_id', $userIds))
            ->when($deviceId, fn ($q) => $q->orWhere('device_id', $deviceId))
            ->when($restoredCartId, fn ($q) => $q->orWhere('id', $restoredCartId))
            ->whereNull('recovered_at')
            ->update(['recovered_at' => now()]);

        session()->forget('restored_abandoned_cart_id');
    }

    private function resolveDeviceId(?Request $request): ?string
    {
        $request ??= request();

        if ($cookie = $request->cookie('device_id')) {
            return $cookie;
        }

        $id = (string) Str::uuid();

        Cookie::queue('device_id', $id, 60 * 24 * 365 * 2);

        return $id;
    }
}
