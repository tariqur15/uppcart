<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductTierPrice;
use Illuminate\Support\Collection;

class TieredPricingService
{
    /**
     * Get all active tiers for a product, sorted by minimum_quantity.
     */
    public function getTiers(Product $product): Collection
    {
        return ProductTierPrice::where('product_id', $product->id)
            ->where('status', true)
            ->orderBy('minimum_quantity')
            ->get();
    }

    /**
     * Find the matching tier for a given quantity.
     */
    public function getMatchingTier(Product $product, int $quantity): ?ProductTierPrice
    {
        return $this->getTiers($product)
            ->first(fn (ProductTierPrice $tier) => $quantity >= $tier->minimum_quantity
                && ($tier->maximum_quantity === null || $quantity <= $tier->maximum_quantity));
    }

    /**
     * Calculate the final unit price for a given quantity.
     */
    public function getUnitPrice(Product $product, int $quantity): float
    {
        $basePrice = (float) ($product->sale_price ?? $product->price);
        $tier = $this->getMatchingTier($product, $quantity);

        if (! $tier) {
            return $basePrice;
        }

        return $this->calculateTierPrice($basePrice, $tier);
    }

    /**
     * Calculate the discount amount for a given quantity.
     */
    public function getDiscountAmount(Product $product, int $quantity): float
    {
        $basePrice = (float) ($product->sale_price ?? $product->price);

        return $basePrice - $this->getUnitPrice($product, $quantity);
    }

    /**
     * Get total discount for a cart item (unit discount × quantity).
     */
    public function getTotalDiscount(Product $product, int $quantity): float
    {
        return $this->getDiscountAmount($product, $quantity) * $quantity;
    }

    /**
     * Preview unit price for a specific tier (used in admin).
     */
    public function previewTierPrice(float $basePrice, string $discountType, float $discountValue): float
    {
        return $this->calculateTierPrice($basePrice, [
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
        ]);
    }

    /**
     * Core price calculation — never allows negative prices.
     */
    private function calculateTierPrice(float $basePrice, ProductTierPrice|array $tier): float
    {
        $discountType = $tier instanceof ProductTierPrice ? $tier->discount_type : $tier['discount_type'];
        $discountValue = $tier instanceof ProductTierPrice ? (float) $tier->discount_value : (float) $tier['discount_value'];

        $finalPrice = match ($discountType) {
            'percentage' => $basePrice - ($basePrice * ($discountValue / 100)),
            'fixed_amount' => $basePrice - $discountValue,
            'fixed_price' => $discountValue,
            default => $basePrice,
        };

        return max(0, $finalPrice);
    }
}
