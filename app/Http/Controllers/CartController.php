<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\AbandonedCart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\FreeShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly FreeShippingService $freeShippingService,
    ) {}

    public function loading(): View
    {
        return view('cart.loading');
    }

    public function index(Request $request): View|JsonResponse
    {
        $data = $this->cartViewData();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('cart.partials.panel', $data)->render(),
                'free_shipping_qualifies' => $data['freeShippingQualifies'],
            ]);
        }

        return view('cart.index', $data);
    }

    /**
     * Build the shared view data for the cart page and its panel partial.
     *
     * @return array<string, mixed>
     */
    private function cartViewData(): array
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->calculateTotal($items);
        $freeShippingThreshold = (float) (Setting::get('free_shipping_threshold', '0'));
        $freeShippingType = Setting::get('free_shipping_type', 'order_amount');
        $freeShippingQualifies = $this->freeShippingService->qualifies($items);
        $freeShippingWeightThreshold = (float) (Setting::get('free_shipping_weight_threshold', '0'));
        $freeShippingProductIds = json_decode(Setting::get('free_shipping_products', '[]'), true) ?? [];
        $cartWeight = 0;
        if ($freeShippingType === 'product_weight') {
            $cartWeight = $items->sum(function ($item) {
                $product = $item->product ?? Product::find($item->product_id);

                return (float) ($product->weight ?? 0) * ($item->quantity ?? 1);
            });
        }

        $recommended = Product::where('status', 'active')
            ->whereNotIn('id', $items->pluck('product_id')->filter()->values())
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return [
            'items' => $items,
            'total' => $total,
            'recommended' => $recommended,
            'freeShippingThreshold' => $freeShippingThreshold,
            'freeShippingType' => $freeShippingType,
            'freeShippingQualifies' => $freeShippingQualifies,
            'freeShippingWeightThreshold' => $freeShippingWeightThreshold,
            'freeShippingProductIds' => $freeShippingProductIds,
            'cartWeight' => $cartWeight,
        ];
    }

    public function store(StoreCartItemRequest $request): RedirectResponse|JsonResponse
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        $variant = $request->input('variant');

        $customFieldsData = null;
        $all = $request->all();
        if (isset($all['custom_fields']) && is_array($all['custom_fields'])) {
            $customFieldsData = collect($all['custom_fields'])
                ->filter(fn ($f) => is_array($f) && isset($f['label']))
                ->mapWithKeys(function ($f) {
                    $value = $f['value'] ?? '';
                    if ($value instanceof UploadedFile) {
                        $value = $value->store('custom-fields', 'public');
                    }

                    return [($f['label'] ?? 'Field') => $value];
                })
                ->filter()
                ->all();
        }

        $this->cartService->addOrIncrement($productId, $quantity, $variant, $customFieldsData ?: null);

        $items = $this->cartService->getItems();
        $total = $this->cartService->calculateTotal($items);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added to cart.',
                'cart_count' => $items->sum('quantity'),
                'cart_total' => $total,
            ]);
        }

        $redirect = $request->input('_redirect');

        if ($redirect === 'checkout') {
            return redirect()->route('checkout.loading');
        }

        if (! auth()->check()) {
            return redirect()->back()->with('success', 'Item added to cart.');
        }

        return redirect()->route('cart.index')->with('success', 'Item added to cart.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse|JsonResponse
    {
        $this->cartService->updateQuantity($cartItem, $request->input('quantity'));

        if ($request->wantsJson()) {
            $items = $this->cartService->getItems();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'cart_count' => $items->sum('quantity'),
                'cart_total' => round($this->cartService->calculateTotal($items), 2),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function drawer(): JsonResponse
    {
        $items = $this->cartService->getItems();
        $isAuthenticated = auth()->check();

        $drawerItems = $items->map(fn ($item) => [
            'key' => (string) $item->id,
            'product_id' => $item->product_id,
            'is_session' => ! $isAuthenticated,
            'name' => $item->product->name,
            'url' => route('products.show', $item->product),
            'image' => $item->product->image ? asset('storage/'.$item->product->image) : null,
            'quantity' => (int) ($item->quantity ?? $item->qty),
            'unit_price' => round($this->cartService->itemUnitPrice($item), 2),
            'subtotal' => round($this->cartService->itemSubtotal($item), 2),
        ])->values();

        return response()->json([
            'success' => true,
            'items' => $drawerItems,
            'count' => $items->sum('quantity'),
            'total' => round($this->cartService->calculateTotal($items), 2),
        ]);
    }

    public function updateSession(int $productId, Request $request): RedirectResponse|JsonResponse
    {
        $quantity = (int) $request->input('quantity', 1);

        if ($quantity < 1 || $quantity > 99) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantity must be between 1 and 99.',
                ], 422);
            }

            return redirect()->route('cart.index')->withErrors(['quantity' => 'Quantity must be between 1 and 99.']);
        }

        $this->cartService->updateSessionItem($productId, $quantity);

        $items = $this->cartService->getItems();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'cart_count' => $items->sum('quantity'),
                'cart_total' => round($this->cartService->calculateTotal($items), 2),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem, Request $request): RedirectResponse|JsonResponse
    {
        $this->cartService->removeItem($cartItem);

        if ($request->ajax() || $request->wantsJson()) {
            $items = $this->cartService->getItems();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
                'cart_count' => $items->sum('quantity'),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    public function destroySession(int $productId, Request $request): RedirectResponse|JsonResponse
    {
        $this->cartService->removeSessionItem($productId);

        $items = $this->cartService->getItems();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
                'cart_count' => $items->sum('quantity'),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    public function clearAll(Request $request): RedirectResponse|JsonResponse
    {
        $this->cartService->clearCart();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared.',
                'cart_count' => 0,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }

    public function restore(AbandonedCart $abandonedCart): RedirectResponse
    {
        abort_unless($abandonedCart->recovered_at === null && $abandonedCart->item_count > 0, 404);

        foreach ($abandonedCart->items_data ?? [] as $item) {
            $this->cartService->addOrIncrement(
                (int) $item['product_id'],
                (int) ($item['quantity'] ?? 1),
                $item['variant'] ?? null,
            );
        }

        session(['restored_abandoned_cart_id' => $abandonedCart->id]);

        if (auth()->check()) {
            return redirect()->route('cart.index')->with('success', 'Your saved cart has been restored.');
        }

        return redirect()->route('checkout.loading')
            ->withInput(array_filter([
                'first_name' => $abandonedCart->name,
                'phone' => $abandonedCart->phone,
            ]))
            ->with('success', 'Your saved cart has been restored. Complete your order below.');
    }
}
