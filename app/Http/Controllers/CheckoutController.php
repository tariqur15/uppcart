<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Coupon;
use App\Models\DownloadToken;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\AbandonedCartService;
use App\Services\CartService;
use App\Services\FreeShippingService;
use App\Services\PasswordSetupService;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly FreeShippingService $freeShippingService,
        private readonly PasswordSetupService $passwordSetupService,
        private readonly AbandonedCartService $abandonedCartService,
        private readonly ShippingService $shippingService,
    ) {}

    public function loading(): View
    {
        return view('checkout.loading');
    }

    public function index(): View
    {
        $items = $this->cartService->getItems();

        if ($items->isEmpty()) {
            return view('checkout.empty');
        }

        $subtotal = $this->cartService->calculateTotal($items);

        $geoData = json_decode(
            File::get(storage_path('app/bangladesh-geo-lean.json')),
            true,
        );

        $freeShippingQualifies = $this->freeShippingService->qualifies($items);
        $freeShippingType = Setting::get('free_shipping_type', 'order_amount');

        $shippingZones = $this->shippingService->zones();

        return view('checkout.index', compact(
            'items',
            'subtotal',
            'geoData',
            'freeShippingQualifies',
            'freeShippingType',
            'shippingZones',
        ));
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        $items = $this->cartService->getItems();
        $isDigitalOnly = $items->every(fn ($item) => ($item->product ?? Product::find($item->product_id))?->type === 'digital');

        $deliveryOption = $request->input('delivery_option');

        if ($this->shippingService->hasZones()) {
            $weight = $items->sum(fn ($item) => ((float) ($item->product->weight ?? 0)) * $item->quantity);
            $matched = $this->shippingService->zoneForDistrict($request->input('district'), $request->input('division'), $request->input('thana'));
            $methods = $this->shippingService->methodsForZone($matched);
            $method = collect($methods)->firstWhere('id', $deliveryOption);
            $deliveryCharge = $isDigitalOnly ? 0 : ($method ? $this->shippingService->chargeForMethod($method, $weight) : 0);
        } else {
            $deliveryCharges = [
                'inside_dhaka' => 60,
                'outside_dhaka' => 120,
                'express' => 150,
            ];

            $deliveryCharge = $isDigitalOnly ? 0 : ($deliveryCharges[$deliveryOption] ?? 60);
        }

        if ($this->freeShippingService->qualifies($items)) {
            $deliveryCharge = 0;
        }

        $fullAddress = collect([
            $request->input('full_address'),
            $request->input('thana'),
            $request->input('district'),
            $request->input('division'),
            'Bangladesh',
        ])->filter()->implode(', ');

        $discountAmount = 0;
        $promoCode = $request->input('promo_code');
        if ($promoCode) {
            $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($promoCode)])->first();
            if ($coupon && $coupon->isValid()) {
                $subtotal = $this->cartService->calculateTotal();
                if (! $coupon->min_order_amount || $subtotal >= $coupon->min_order_amount) {
                    $discountAmount = match ($coupon->type) {
                        'percentage' => min($subtotal * $coupon->value / 100, $subtotal),
                        'fixed' => min((float) $coupon->value, $subtotal),
                        default => 0,
                    };
                    $discountAmount = round($discountAmount, 2);
                    $coupon->increment('used_count');
                }
            }
        }

        $extras = [
            'first_name' => $request->input('first_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address_type' => $request->input('address_type'),
            'division' => $request->input('division'),
            'district' => $request->input('district'),
            'thana' => $request->input('thana'),
            'landmark' => $request->input('landmark'),
            'delivery_option' => $deliveryOption,
            'delivery_charge' => $deliveryCharge,
            'payment_method' => $request->input('payment_method'),
            'order_note' => $request->input('order_note'),
            'promo_code' => $promoCode,
            'discount_amount' => $discountAmount,
            'ip_address' => $request->ip(),
            'device_id' => md5($request->userAgent()),
        ];

        $user = $this->findOrCreateUser($request);

        $order = $this->cartService->checkout($fullAddress, $extras, $user->id);

        $this->abandonedCartService->markRecovered($request, $user->id);

        DownloadToken::generateForOrder($order);

        Notification::createForOrder('new_order', $order);

        session(['last_order_phone' => $request->validated('phone')]);

        if ($user->wasRecentlyCreated || ! $user->password_set) {
            $token = $this->passwordSetupService->generateToken($user);

            return redirect()->route('orders.show', $order)
                ->with('show_password_setup', true)
                ->with('password_setup_token', $token->token)
                ->with('success', 'Your order has been placed successfully. We\'ve created an account using your mobile number.');
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }

    public function trackAbandon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:1000',
        ]);

        $this->abandonedCartService->track($request, $validated['name'], $validated['phone'], $validated['address'] ?? null);

        return response()->json(['success' => true]);
    }

    public function validatePromo(Request $request): JsonResponse
    {
        $code = strtoupper(trim($request->input('code', '')));

        if (! $code) {
            return response()->json(['valid' => false, 'message' => 'Please enter a promo code.']);
        }

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [$code])->first();

        if (! $coupon || ! $coupon->isValid()) {
            return response()->json(['valid' => false, 'message' => "That code isn't valid, please try again."]);
        }

        $subtotal = (float) $request->input('subtotal', 0);

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimum order amount is ৳'.number_format($coupon->min_order_amount, 0).'.',
            ]);
        }

        $discount = match ($coupon->type) {
            'percentage' => min($subtotal * $coupon->value / 100, $subtotal),
            'fixed' => min((float) $coupon->value, $subtotal),
            default => 0,
        };

        $discount = round($discount, 2);

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'discount' => $discount,
            'message' => 'Code applied — you saved ৳'.number_format($discount, 0),
        ]);
    }

    private function findOrCreateUser(StoreCheckoutRequest $request): User
    {
        $phone = $request->validated('phone');
        $email = $request->validated('email');
        $name = $request->validated('first_name');

        $existingUser = User::where('phone', $phone)
            ->when($email, fn ($q) => $q->orWhere('email', $email))
            ->first();

        if ($existingUser) {
            $updates = [];
            if ($email && ! $existingUser->email) {
                $updates['email'] = $email;
            }
            if ($phone && ! $existingUser->phone) {
                $updates['phone'] = $phone;
            }
            if ($name && ! $existingUser->name) {
                $updates['name'] = $name;
            }
            if ($updates) {
                $existingUser->update($updates);
            }

            return $existingUser;
        }

        return DB::transaction(function () use ($name, $phone, $email) {
            return User::create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'password' => null,
                'password_set' => false,
                'role' => 'customer',
            ]);
        });
    }
}
