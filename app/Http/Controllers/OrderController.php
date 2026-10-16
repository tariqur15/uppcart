<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        $this->authorizeAccess($order);

        $storeName = Setting::get('store_name') ?? config('app.name', 'Store');

        session()->forget('success');

        return view('orders.show', compact('order', 'storeName'));
    }

    public function invoice(Order $order)
    {
        $this->authorizeAccess($order);

        $order->load('items.product');

        $settings = Setting::pluck('value', 'key')->toArray();
        $storeName = $settings['store_name'] ?? config('app.name', 'Store');
        $currencySymbol = match ($settings['currency'] ?? 'USD') {
            'BDT' => '৳',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            default => '$',
        };

        $pdf = Pdf::loadView('orders.invoice', compact('order', 'storeName', 'currencySymbol', 'settings'));

        return $pdf->download('invoice-'.$order->order_number.'.pdf');
    }

    public function printInvoice(Order $order)
    {
        $this->authorizeAccess($order);

        $order->load('items.product');

        $settings = Setting::pluck('value', 'key')->toArray();
        $storeName = $settings['store_name'] ?? config('app.name', 'Store');
        $currencySymbol = match ($settings['currency'] ?? 'USD') {
            'BDT' => '৳',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            default => '$',
        };

        return view('orders.invoice', compact('order', 'storeName', 'currencySymbol', 'settings'));
    }

    private function authorizeAccess(Order $order): void
    {
        if (auth()->check()) {
            abort_if($order->user_id !== auth()->id(), 403);
        } else {
            $phone = session('last_order_phone');

            if (! $phone || $order->phone !== $phone) {
                abort(403);
            }
        }
    }
}
