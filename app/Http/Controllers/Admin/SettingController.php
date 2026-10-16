<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Popup;
use App\Models\PopupEvent;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const TABS = [
        'general' => 'General',
        'shipping' => 'Shipping',
        'payments' => 'Payments',
        'integration' => 'Integration',
        'seo' => 'SEO',
        'contact' => 'Contact',
        'additional' => 'Additional',
    ];

    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'general');

        if (! array_key_exists($activeTab, self::TABS)) {
            $activeTab = 'general';
        }

        $settings = Setting::allByGroup($activeTab);

        $viewData = [
            'tabs' => self::TABS,
            'activeTab' => $activeTab,
            'settings' => $settings,
        ];

        if ($activeTab === 'shipping') {
            $viewData['geoData'] = json_decode(
                File::get(storage_path('app/bangladesh-geo-lean.json')),
                true,
            );
            $viewData['allProducts'] = Product::select('id', 'name')->orderBy('name')->get();
            $viewData['allCategories'] = Category::select('id', 'name')->orderBy('name')->get();
            $viewData['allCollections'] = Collection::select('id', 'name')->orderBy('name')->get();
        }

        if ($activeTab === 'additional') {
            $viewData['topBarsConfig'] = app(\App\Services\TopBarService::class)->bars();
            $viewData['topBarTypes'] = \App\Services\TopBarService::TYPES;
            $viewData['topBarAnimations'] = \App\Services\TopBarService::ANIMATIONS;

            $popupService = app(\App\Services\PopupService::class);
            $viewData['popups'] = Popup::orderBy('sort_order')->orderByDesc('id')->get()->map(function (Popup $popup) {
                $events = PopupEvent::query()
                    ->where('popup_id', $popup->id)
                    ->selectRaw('COUNT(*) as total')
                    ->selectRaw("SUM(type = 'impression') as impressions")
                    ->selectRaw("SUM(type = 'view') as views")
                    ->selectRaw("SUM(type = 'click') as clicks")
                    ->selectRaw("SUM(type = 'close') as closes")
                    ->first();

                $views = (int) $events->views;
                $clicks = (int) $events->clicks;

                $popup->stats = [
                    'impressions' => (int) $events->impressions,
                    'views' => $views,
                    'clicks' => $clicks,
                    'closes' => (int) $events->closes,
                    'ctr' => $views > 0 ? round(($clicks / $views) * 100, 1) : 0.0,
                ];

                return $popup;
            });
            $viewData['popupDefaults'] = $popupService->defaults();
            $viewData['popupDevices'] = \App\Services\PopupService::DEVICES;
            $viewData['popupPages'] = \App\Services\PopupService::PAGES;
            $viewData['popupCustomers'] = \App\Services\PopupService::CUSTOMERS;
            $viewData['popupTriggers'] = \App\Services\PopupService::TRIGGERS;
            $viewData['popupFrequencies'] = \App\Services\PopupService::FREQUENCIES;
            $viewData['popupImagePositions'] = \App\Services\PopupService::IMAGE_POSITIONS;
        }

        return view('admin.settings.index', $viewData);
    }

    public function update(Request $request): RedirectResponse
    {
        $activeTab = $request->input('tab', 'general');

        $fileFields = ['store_logo', 'store_favicon', 'payment_bkash_logo', 'payment_nagad_logo', 'payment_rocket_logo', 'payment_cod_logo', 'payment_card_logo', 'payment_sslcommerz_logo', 'popup_image'];

        $toggleFields = ['fraud_check_enabled', 'chat_bubble_custom', 'abandoned_whatsapp_enabled', 'top_bar_enabled', 'popup_enabled', 'fake_orders_enabled'];

        $jsonFields = ['top_bars'];

        $arrayFields = [];

        $customFields = ['fake_order_mode'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $old = Setting::get($field);
                if ($old && File::exists(storage_path('app/public/'.$old))) {
                    File::delete(storage_path('app/public/'.$old));
                }
                $path = $request->file($field)->store('settings', 'public');
                Setting::set($field, $path, $activeTab);
            }
        }

        foreach ($toggleFields as $field) {
            Setting::set($field, $request->boolean($field) ? '1' : '', $activeTab);
        }

        foreach ($jsonFields as $field) {
            $raw = $request->input($field);

            if (is_string($raw) && json_decode($raw, true) !== null) {
                Setting::set($field, $raw, $activeTab);
            }
        }

        $fields = $request->except(array_merge(['_token', 'tab'], $fileFields, $toggleFields, $jsonFields, $arrayFields, $customFields));

        foreach ($fields as $key => $value) {
            if (is_string($value)) {
                Setting::set($key, $value, $activeTab);
            }
        }

        foreach ($arrayFields as $field) {
            $selected = $request->input($field, []);
            Setting::set($field, is_array($selected) ? implode(',', $selected) : '', $activeTab);
        }

        $fakeOrderMode = $request->input('fake_order_mode', 'all');
        $fakeOrderPresets = ['all', 'home', 'products', 'product', 'cart', 'checkout'];

        if (in_array($fakeOrderMode, $fakeOrderPresets, true)) {
            Setting::set('fake_order_pages', $fakeOrderMode, $activeTab);
        } else {
            $rawUrls = preg_split('/\r\n|\r|\n/', (string) $request->input('fake_order_pages', ''));
            $rawUrls = array_values(array_filter(array_map('trim', $rawUrls)));
            Setting::set('fake_order_pages', implode("\n", $rawUrls), $activeTab);
        }

        return redirect()
            ->route('admin.settings.index', ['tab' => $activeTab])
            ->with('success', ucfirst($activeTab).' settings updated successfully.');
    }
}
