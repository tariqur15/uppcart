<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\ShippingService;
use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $validPayments = [];
        if (($settings['payment_bkash_enabled'] ?? '1') === '1') {
            $validPayments[] = 'bkash';
        }
        if (($settings['payment_nagad_enabled'] ?? '1') === '1') {
            $validPayments[] = 'nagad';
        }
        if (($settings['payment_rocket_enabled'] ?? '1') === '1') {
            $validPayments[] = 'rocket';
        }
        if (($settings['payment_card_enabled'] ?? '1') === '1') {
            $validPayments[] = 'card';
        }
        if (($settings['payment_cod_enabled'] ?? '1') === '1') {
            $validPayments[] = 'cod';
        }
        if (empty($validPayments)) {
            $validPayments = ['bkash', 'nagad', 'rocket', 'card', 'cod'];
        }

        $shippingService = app(ShippingService::class);

        $validDeliveries = [];
        if ($shippingService->hasZones()) {
            foreach ($shippingService->zones() as $zoneIndex => $zone) {
                foreach ($zone['methods'] ?? [] as $methodIndex => $method) {
                    if (! empty($method['enabled'])) {
                        $validDeliveries[] = $shippingService->methodId($zoneIndex, $methodIndex);
                    }
                }
            }
            if (empty($validDeliveries)) {
                $validDeliveries = ['z0m0'];
            }
        } else {
            if (($settings['delivery_inside_dhaka'] ?? '1') === '1') {
                $validDeliveries[] = 'inside_dhaka';
            }
            if (($settings['delivery_outside_dhaka'] ?? '1') === '1') {
                $validDeliveries[] = 'outside_dhaka';
            }
            if (($settings['delivery_express'] ?? '1') === '1') {
                $validDeliveries[] = 'express';
            }
            if (empty($validDeliveries)) {
                $validDeliveries = ['inside_dhaka', 'outside_dhaka', 'express'];
            }
        }

        $cartService = app(CartService::class);
        $items = $cartService->getItems();
        $isDigitalOnly = $items->isNotEmpty() && $items->every(fn ($item) => ($item->product ?? Product::find($item->product_id))?->type === 'digital');

        return [
            'first_name' => ['required', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_type' => ['nullable', 'string', 'in:home,office,other'],
            'division' => $isDigitalOnly ? ['nullable', 'string', 'max:50'] : ['required', 'string', 'max:50'],
            'district' => $isDigitalOnly ? ['nullable', 'string', 'max:50'] : ['required', 'string', 'max:50'],
            'thana' => ['nullable', 'string', 'max:100'],
            'full_address' => $isDigitalOnly ? ['nullable', 'string', 'max:500'] : ['required', 'string', 'min:3', 'max:500'],
            'landmark' => ['nullable', 'string', 'max:200'],
            'delivery_option' => $isDigitalOnly ? ['nullable', 'string'] : ['required', 'string', 'in:'.implode(',', $validDeliveries)],
            'payment_method' => ['required', 'string', 'in:'.implode(',', $validPayments)],
            'order_note' => ['nullable', 'string', 'max:500'],
            'promo_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Please enter your full name.',
            'phone.required' => 'Please enter your mobile number.',
            'division.required' => 'Please select your division.',
            'district.required' => 'Please select your district.',
            'full_address.required' => 'Please enter your full delivery address.',
            'delivery_option.required' => 'Please select a delivery option.',
            'payment_method.required' => 'Please select a payment method.',
        ];
    }
}
