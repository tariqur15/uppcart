<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private readonly TieredPricingService $tieredPricingService,
        private readonly FlashSaleService $flashSaleService,
    ) {}

    public function getItems(): SupportCollection
    {
        if (auth()->check()) {
            return CartItem::with('product')
                ->where('user_id', auth()->id())
                ->get();
        }

        return $this->getSessionItems();
    }

    public function calculateTotal(?SupportCollection $items = null): float
    {
        $items ??= $this->getItems();

        return $items->sum(fn ($item) => $this->itemSubtotal($item));
    }

    public function itemSubtotal(object $item): float
    {
        $product = $item->product ?? Product::find($item->product_id);
        $quantity = $item->quantity ?? $item->qty;

        $unitPrice = $this->unitPrice($product, $item->variant ?? null, $quantity);

        return $unitPrice * $quantity;
    }

    /**
     * Resolve the unit price for a product/variant/quantity, applying an active
     * flash sale price when one matches (flash takes priority over tier pricing).
     */
    public function unitPrice(Product $product, ?string $variant = null, int $quantity = 1): float
    {
        $tierPrice = $this->tieredPricingService->getUnitPrice($product, $quantity);

        $result = $this->flashSaleService->effectivePrice($product, $variant, $quantity, $tierPrice);

        return $result['price'];
    }

    public function itemUnitPrice(object $item): float
    {
        $quantity = $item->quantity ?? $item->qty;

        if ($quantity <= 0) {
            return 0.0;
        }

        return $this->itemSubtotal($item) / $quantity;
    }

    public function addOrIncrement(int $productId, int $quantity = 1, ?string $variant = null, ?array $customFieldsData = null): object
    {
        $product = Product::findOrFail($productId);

        abort_if($product->status !== 'active', 404);

        if ($product->type !== 'digital' && $product->stock < $quantity) {
            abort(422, "Insufficient stock for \"{$product->name}\". Only {$product->stock} available.");
        }

        $flashProduct = $this->flashSaleService->findFlashProduct($product, $variant);

        if ($flashProduct) {
            $this->validateFlashItem($flashProduct, $quantity, auth()->id(), null, null);
        }

        if (auth()->check()) {
            return $this->addOrIncrementDb($productId, $quantity, $variant, $customFieldsData);
        }

        return $this->addOrIncrementSession($productId, $quantity, $variant, $customFieldsData);
    }

    public function updateQuantity(CartItem $cartItem, int $quantity): void
    {
        $this->assertOwnership($cartItem);

        $cartItem->update(['quantity' => $quantity]);
    }

    public function removeItem(CartItem $cartItem): void
    {
        $this->assertOwnership($cartItem);

        $cartItem->delete();
    }

    public function updateSessionItem(int $productId, int $quantity): void
    {
        $cart = session('cart', []);

        foreach ($cart as $key => $item) {
            if ((int) $item['product_id'] === $productId) {
                $cart[$key]['quantity'] = $quantity;
                break;
            }
        }

        session(['cart' => $cart]);
    }

    public function clearCart(): void
    {
        if (auth()->check()) {
            CartItem::where('user_id', auth()->id())->delete();
        } else {
            session()->forget('cart');
        }
    }

    public function removeSessionItem(int $productId): void
    {
        $cart = session('cart', []);

        $cart = array_values(array_filter($cart, fn ($item) => (int) $item['product_id'] !== $productId));

        session(['cart' => $cart]);
    }

    public function checkout(string $address, array $extras = [], ?int $userId = null): Order
    {
        $items = $this->getItems();

        abort_if($items->isEmpty(), 400, 'Cart is empty.');

        $userId ??= auth()->id();

        return DB::transaction(function () use ($items, $address, $extras, $userId) {
            $this->validateStock($items, $userId, $extras['phone'] ?? null, $extras['email'] ?? null);

            $total = $this->calculateTotal($items);

            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $total + ($extras['delivery_charge'] ?? 0) - ($extras['discount_amount'] ?? 0),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'address' => $address,
                'first_name' => $extras['first_name'] ?? null,
                'last_name' => $extras['last_name'] ?? null,
                'email' => $extras['email'] ?? null,
                'phone' => $extras['phone'] ?? null,
                'address_type' => $extras['address_type'] ?? null,
                'division' => $extras['division'] ?? null,
                'district' => $extras['district'] ?? null,
                'thana' => $extras['thana'] ?? null,
                'landmark' => $extras['landmark'] ?? null,
                'delivery_option' => $extras['delivery_option'] ?? 'inside_dhaka',
                'delivery_charge' => $extras['delivery_charge'] ?? 0,
                'payment_method' => $extras['payment_method'] ?? 'cod',
                'order_note' => $extras['order_note'] ?? null,
                'promo_code' => $extras['promo_code'] ?? null,
                'discount_amount' => $extras['discount_amount'] ?? 0,
                'ip_address' => $extras['ip_address'] ?? null,
                'device_id' => $extras['device_id'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = $item->product ?? Product::find($item->product_id);
                $quantity = $item->quantity ?? $item->qty;
                $variant = $item->variant ?? null;

                $this->deductStock($product, $quantity);

                $tier = $this->tieredPricingService->getMatchingTier($product, $quantity);
                $tierPrice = $this->tieredPricingService->getUnitPrice($product, $quantity);
                $basePrice = (float) ($product->sale_price ?? $product->price);

                $flashResult = $this->flashSaleService->effectivePrice($product, $variant, $quantity, $tierPrice);
                /** @var \App\Models\FlashSaleProduct|null $flashProduct */
                $flashProduct = $flashResult['flash_product'];

                if ($flashProduct) {
                    $unitPrice = $flashResult['price'];
                    $this->deductFlashStock($flashProduct, $quantity);

                    $order->items()->create([
                        'product_id' => $product->id,
                        'price' => $unitPrice,
                        'quantity' => $quantity,
                        'original_price' => (float) $flashProduct->regular_price,
                        'tier_price' => $tierPrice,
                        'discount' => round(((float) $flashProduct->regular_price - $unitPrice) * $quantity, 2),
                        'product_tier_price_id' => $tier?->id,
                        'flash_sale_id' => $flashProduct->flash_sale_id,
                        'flash_sale_product_id' => $flashProduct->id,
                        'variant' => $variant,
                        'custom_fields_data' => $item->custom_fields_data ?? null,
                    ]);
                } else {
                    $discount = $basePrice - $tierPrice;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'price' => $tierPrice,
                        'quantity' => $quantity,
                        'original_price' => $basePrice,
                        'tier_price' => $tierPrice,
                        'discount' => $discount * $quantity,
                        'product_tier_price_id' => $tier?->id,
                        'variant' => $variant,
                        'custom_fields_data' => $item->custom_fields_data ?? null,
                    ]);
                }
            }

            if (auth()->check()) {
                CartItem::where('user_id', auth()->id())->delete();
            } else {
                session()->forget('cart');
            }

            return $order;
        });
    }

    public function validateStock(?SupportCollection $items = null, ?int $userId = null, ?string $phone = null, ?string $email = null): void
    {
        $items ??= $this->getItems();

        foreach ($items as $item) {
            $product = $item->product ?? Product::find($item->product_id);
            $quantity = $item->quantity ?? $item->qty;
            $variant = $item->variant ?? null;

            if ($product->status !== 'active') {
                abort(422, 'This product is not available.');
            }

            if ($product->type !== 'digital' && $product->stock < $quantity) {
                abort(422, "Insufficient stock for \"{$product->name}\". Only {$product->stock} available.");
            }

            $flashProduct = $this->flashSaleService->findFlashProduct($product, $variant);

            if ($flashProduct) {
                $this->validateFlashItem($flashProduct, $quantity, $userId, $phone, $email);
            }
        }
    }

    private function validateFlashItem(\App\Models\FlashSaleProduct $flashProduct, int $quantity, ?int $userId, ?string $phone, ?string $email): void
    {
        if ($flashProduct->sale_stock !== null && $flashProduct->remaining() < $quantity) {
            abort(422, "This Flash Sale item only has {$flashProduct->remaining()} left.");
        }

        if ($flashProduct->max_per_customer !== null) {
            $bought = $this->flashSaleService->customerPurchasedQuantity(
                $flashProduct->flashSale,
                $flashProduct,
                $userId,
                $phone,
                $email,
            );

            if ($bought + $quantity > $flashProduct->max_per_customer) {
                abort(422, "Maximum {$flashProduct->max_per_customer} units can be purchased at the Flash Sale price.");
            }
        }
    }

    private function deductFlashStock(\App\Models\FlashSaleProduct $flashProduct, int $quantity): void
    {
        if ($flashProduct->sale_stock === null) {
            return;
        }

        $flashProduct->newQuery()
            ->where('id', $flashProduct->id)
            ->whereRaw('(sale_stock - sold) >= ?', [$quantity])
            ->increment('sold', $quantity);
    }

    private function addOrIncrementDb(int $productId, int $quantity = 1, ?string $variant = null, ?array $customFieldsData = null): CartItem
    {
        $existing = CartItem::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->where('variant', $variant)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);

            return $existing->fresh();
        }

        return CartItem::create([
            'user_id' => auth()->id(),
            'product_id' => $productId,
            'quantity' => $quantity,
            'variant' => $variant,
            'custom_fields_data' => $customFieldsData,
        ]);
    }

    private function addOrIncrementSession(int $productId, int $quantity = 1, ?string $variant = null, ?array $customFieldsData = null): object
    {
        $cart = session('cart', []);

        foreach ($cart as $key => $item) {
            if ((int) $item['product_id'] === $productId && ($item['variant'] ?? null) === $variant) {
                $cart[$key]['quantity'] += $quantity;
                session(['cart' => $cart]);

                return (object) $cart[$key];
            }
        }

        $cart[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'variant' => $variant,
            'custom_fields_data' => $customFieldsData,
        ];
        session(['cart' => $cart]);

        return (object) end($cart);
    }

    private function deductStock(Product $product, int $quantity): void
    {
        if ($product->type !== 'digital') {
            $product->decrement('stock', $quantity);
        }
    }

    private function assertOwnership(CartItem $cartItem): void
    {
        abort_if(! auth()->check() || $cartItem->user_id !== auth()->id(), 403, 'This item does not belong to you.');
    }

    private function getSessionItems(): SupportCollection
    {
        $sessionCart = session('cart', []);

        $productIds = array_column($sessionCart, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $items = collect();
        foreach ($sessionCart as $entry) {
            $product = $products->get($entry['product_id']);
            if (! $product) {
                continue;
            }

            $items->push((object) [
                'id' => 'session_'.$entry['product_id'],
                'product_id' => $product->id,
                'quantity' => $entry['quantity'],
                'product' => $product,
                'variant' => $entry['variant'] ?? null,
                'custom_fields_data' => $entry['custom_fields_data'] ?? null,
            ]);
        }

        return $items;
    }
}
