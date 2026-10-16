<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;

class FlashSaleService
{
    /**
     * Resolve the effective status for a sale based on its stored status and schedule.
     */
    public function status(FlashSale $sale): string
    {
        if ($sale->status === FlashSale::STATUS_CANCELLED || $sale->status === FlashSale::STATUS_ENDED) {
            return $sale->status;
        }

        if (! $sale->starts_at || ! $sale->ends_at) {
            return FlashSale::STATUS_DRAFT;
        }

        $now = now();

        if ($sale->starts_at->gt($now)) {
            return FlashSale::STATUS_SCHEDULED;
        }

        if ($sale->ends_at->lt($now)) {
            return FlashSale::STATUS_ENDED;
        }

        return FlashSale::STATUS_ACTIVE;
    }

    /**
     * Sales that are currently running (active and within their time window).
     *
     * @return Collection<int, FlashSale>
     */
    public function activeSales(): Collection
    {
        $now = now();

        return FlashSale::with('products')
            ->where('status', FlashSale::STATUS_ACTIVE)
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->get();
    }

    /**
     * Compute the flash price for a flash sale product row.
     */
    public function computeFlashPrice(FlashSaleProduct $flashProduct): float
    {
        $regular = (float) $flashProduct->regular_price;

        $flashPrice = match ($flashProduct->discount_type) {
            FlashSaleProduct::DISCOUNT_FIXED_PRICE => (float) $flashProduct->discount_value,
            FlashSaleProduct::DISCOUNT_PERCENTAGE => $regular * (1 - (float) $flashProduct->discount_value / 100),
            FlashSaleProduct::DISCOUNT_FIXED_AMOUNT => $regular - (float) $flashProduct->discount_value,
            default => $regular,
        };

        return round(max(0, $flashPrice), 2);
    }

    /**
     * Find the active flash sale product row matching a product (and optional variant).
     */
    public function findFlashProduct(Product $product, ?string $variant = null): ?FlashSaleProduct
    {
        return FlashSaleProduct::query()
            ->where('product_id', $product->id)
            ->where(fn ($q) => $q->whereNull('variant')->orWhere('variant', $variant))
            ->whereHas('flashSale', fn ($q) => $q
                ->where('status', FlashSale::STATUS_ACTIVE)
                ->whereNotNull('starts_at')
                ->whereNotNull('ends_at')
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now()))
            ->orderByRaw("CASE WHEN scope = 'variants' THEN 0 ELSE 1 END")
            ->first();
    }

    /**
     * Effective price for a product/variant/quantity.
     *
     * When an active flash sale matches, returns the flash price and the flash
     * product row so callers can validate stock & purchase limits.
     *
     * @return array{price: float, flash_product: FlashSaleProduct|null}
     */
    public function effectivePrice(Product $product, ?string $variant, int $quantity, float $fallbackPrice): array
    {
        $flashProduct = $this->findFlashProduct($product, $variant);

        if ($flashProduct) {
            return ['price' => $this->computeFlashPrice($flashProduct), 'flash_product' => $flashProduct];
        }

        return ['price' => $fallbackPrice, 'flash_product' => null];
    }

    /**
     * Whether the given sale has any connected order history.
     */
    public function hasOrders(FlashSale $sale): bool
    {
        return Order::whereHas('items', fn ($q) => $q->where('flash_sale_id', $sale->id))->exists();
    }

    /**
     * Previous quantity purchased by a customer for a flash sale product across orders.
     */
    public function customerPurchasedQuantity(FlashSale $sale, FlashSaleProduct $flashProduct, ?int $userId, ?string $phone = null, ?string $email = null): int
    {
        $query = Order::whereHas('items', fn ($q) => $q
            ->where('flash_sale_id', $sale->id)
            ->where('flash_sale_product_id', $flashProduct->id));

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($phone) {
            $query->where('phone', $phone);
        } elseif ($email) {
            $query->where('email', $email);
        } else {
            return 0;
        }

        $orders = $query->pluck('id');

        if ($orders->isEmpty()) {
            return 0;
        }

        return (int) Order::query()
            ->whereIn('id', $orders)
            ->with('items', fn ($q) => $q
                ->where('flash_sale_id', $sale->id)
                ->where('flash_sale_product_id', $flashProduct->id))
            ->get()
            ->flatMap(fn ($order) => $order->items)
            ->sum('quantity');
    }
}
