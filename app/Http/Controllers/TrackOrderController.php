<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\PathaoService;
use App\Services\SteadfastService;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index()
    {
        return view('track-order');
    }

    public function search(Request $request, SteadfastService $steadfast, PathaoService $pathao)
    {
        $request->validate([
            'order_number' => 'required|string',
            'phone' => 'required|string',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->where('phone', $request->phone)
            ->with('items.product', 'statusHistory')
            ->first();

        if (! $order) {
            return back()->withErrors(['order_number' => 'No order found with that order number and phone number.'])->withInput();
        }

        $storeName = Setting::get('store_name') ?? config('app.name', 'Store');
        $currencySymbol = match (Setting::get('currency') ?? 'USD') {
            'BDT' => '৳',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            default => '$',
        };

        $courierTracking = null;

        if ($order->consignment_id && $order->courier_name) {
            $courierTracking = match ($order->courier_name) {
                'pathao' => $pathao->trackByConsignmentId($order->consignment_id),
                'steadfast' => $order->tracking_code ? $steadfast->trackByTrackingCode($order->tracking_code) : null,
                default => null,
            };

            if ($courierTracking && isset($courierTracking['status']) && $courierTracking['status'] !== $order->courier_status) {
                $order->update(['courier_status' => $courierTracking['status']]);
            }
        }

        return view('track-order', compact('order', 'storeName', 'currencySymbol', 'courierTracking'));
    }
}
