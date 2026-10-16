<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Collection;

class FreeShippingService
{
    public function __construct(
        private readonly TieredPricingService $tieredPricingService,
    ) {}

    /**
     * Check if the given cart items qualify for free shipping.
     */
    public function qualifies(Collection $items): bool
    {
        $type = Setting::get('free_shipping_type', 'order_amount');

        return match ($type) {
            'order_amount' => $this->checkOrderAmount($items),
            'specific_products' => $this->checkSpecificProducts($items),
            'specific_categories' => $this->checkSpecificCategories($items),
            'specific_collections' => $this->checkSpecificCollections($items),
            'product_weight' => $this->checkProductWeight($items),
            default => false,
        };
    }

    /**
     * Get the free shipping threshold setting.
     */
    public function getThreshold(): float
    {
        return (float) Setting::get('free_shipping_threshold', '0');
    }

    private function checkOrderAmount(Collection $items): bool
    {
        $threshold = $this->getThreshold();

        if ($threshold <= 0) {
            return false;
        }

        $total = $items->sum(fn ($item) => $this->itemSubtotal($item));

        return $total >= $threshold;
    }

    private function checkSpecificProducts(Collection $items): bool
    {
        $productIds = json_decode(Setting::get('free_shipping_products', '[]'), true);

        if (empty($productIds) || $items->isEmpty()) {
            return false;
        }

        $cartProductIds = $items->pluck('product_id')->filter()->values()->all();

        foreach ($cartProductIds as $productId) {
            if (! in_array($productId, $productIds)) {
                return false;
            }
        }

        return true;
    }

    private function checkSpecificCategories(Collection $items): bool
    {
        $categoryIds = json_decode(Setting::get('free_shipping_categories', '[]'), true);

        if (empty($categoryIds)) {
            return false;
        }

        $cartCategoryIds = $items->pluck('product.categories')
            ->flatten()
            ->pluck('id')
            ->push($items->pluck('product.category_id'))
            ->flatten()
            ->filter()
            ->values()
            ->all();

        return count(array_intersect($categoryIds, $cartCategoryIds)) > 0;
    }

    private function checkSpecificCollections(Collection $items): bool
    {
        $collectionIds = json_decode(Setting::get('free_shipping_collections', '[]'), true);

        if (empty($collectionIds)) {
            return false;
        }

        foreach ($items as $item) {
            $product = $item->product ?? Product::find($item->product_id);
            if (! $product) {
                continue;
            }

            $directCollectionId = $product->collection_id;
            if ($directCollectionId && in_array($directCollectionId, $collectionIds)) {
                return true;
            }

            $pivotCollectionIds = $product->collections()->pluck('collections.id')->all();
            if (count(array_intersect($pivotCollectionIds, $collectionIds)) > 0) {
                return true;
            }
        }

        return false;
    }

    private function checkProductWeight(Collection $items): bool
    {
        $threshold = (float) Setting::get('free_shipping_weight_threshold', '0');

        if ($threshold <= 0) {
            return false;
        }

        $totalWeight = $items->sum(function ($item) {
            $product = $item->product ?? Product::find($item->product_id);

            return (float) ($product->weight ?? 0) * ($item->quantity ?? 1);
        });

        return $totalWeight <= $threshold;
    }

    private function itemSubtotal(object $item): float
    {
        $product = $item->product ?? Product::find($item->product_id);
        $quantity = $item->quantity ?? $item->qty;

        return $this->tieredPricingService->getUnitPrice($product, $quantity) * $quantity;
    }
}
