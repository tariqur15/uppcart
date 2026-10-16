<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
use App\Services\CartService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $provider = $this;

        $this->mergeSocialAuthConfig();

        View::composer('*', function ($view) use ($provider) {
            $flat = Category::orderBy('name')->get();
            $view->with('allCategories', $provider->addHierarchyPrefix($flat));

            $settings = Setting::pluck('value', 'key')->toArray();
            $view->with('storeSettings', $settings);

            $currencySymbol = match ($settings['currency'] ?? 'USD') {
                'BDT' => '৳',
                'USD' => '$',
                'EUR' => '€',
                'GBP' => '£',
                'INR' => '₹',
                default => '$',
            };
            $view->with('currencySymbol', $currencySymbol);

            $cartService = app(CartService::class);
            $cartItems = $cartService->getItems();

            $view->with([
                'cartCount' => $cartItems->sum('quantity'),
                'cartPreviewItems' => $cartItems,
                'cartTotal' => $cartItems->sum(fn ($item) => $cartService->itemSubtotal($item)),
            ]);

            $view->with('fakeOrdersSettings', [
                'enabled' => ($settings['fake_orders_enabled'] ?? '0') === '1',
                'message' => $settings['fake_order_message'] ?? '',
                'interval_seconds' => (int) ($settings['fake_order_interval_seconds'] ?? 20),
                'position' => $settings['fake_order_position'] ?? 'bottom-left',
                'pages' => $settings['fake_order_pages'] ?? 'all',
            ]);

            $flashSaleService = app(\App\Services\FlashSaleService::class);
            $activeFlashSales = $flashSaleService->activeSales();

            $view->with([
                'activeFlashSales' => $activeFlashSales,
                'activeFlashSale' => $activeFlashSales->sortBy('ends_at')->first(),
                'flashProductIndex' => $activeFlashSales
                    ->flatMap(fn ($sale) => $sale->products)
                    ->keyBy(fn ($fp) => $fp->product_id.($fp->variant ? '|'.$fp->variant : '')),
            ]);

            $popupService = app(\App\Services\PopupService::class);
            $view->with('storePopups', $popupService->activePopups(request())
                ->map(fn ($popup) => [
                    'id' => $popup->id,
                    'name' => $popup->name,
                    'config' => $popupService->viewModel($popup),
                ]));

            $topbar = app(\App\Services\TopBarService::class);
            $view->with('topBars', collect($topbar->activeBars())
                ->filter(fn ($bar) => $topbar->shouldDisplay($bar, request()))
                ->map(fn ($bar) => ['bar' => $bar, 'content' => $topbar->content($bar)])
                ->values());
        });
    }

    private function mergeSocialAuthConfig(): void
    {
        try {
            $settings = Setting::pluck('value', 'key')->toArray();
        } catch (Throwable $e) {
            return;
        }

        $mapping = [
            'google' => [
                'client_id' => 'google_oauth_client_id',
                'client_secret' => 'google_oauth_client_secret',
                'redirect' => 'google_oauth_redirect_uri',
            ],
            'facebook' => [
                'client_id' => 'facebook_oauth_client_id',
                'client_secret' => 'facebook_oauth_client_secret',
                'redirect' => 'facebook_oauth_redirect_uri',
            ],
        ];

        foreach ($mapping as $provider => $keys) {
            $merge = [];
            foreach ($keys as $configKey => $settingKey) {
                $value = $settings[$settingKey] ?? null;
                if ($value !== null && $value !== '') {
                    $merge[$configKey] = $value;
                }
            }

            if ($merge) {
                config()->set("services.{$provider}", array_merge(config("services.{$provider}", []), $merge));
            }
        }
    }

    private function addHierarchyPrefix(Collection $categories, $parentId = null, $prefix = '', int $depth = 0): Collection    {
        if ($depth > 10) {
            return new Collection;
        }

        $result = new Collection;
        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $category->hierarchy_prefix = $prefix;
                $result->push($category);
                foreach ($this->addHierarchyPrefix($categories, $category->id, $prefix.'— ', $depth + 1) as $child) {
                    $result->push($child);
                }
            }
        }

        return $result;
    }
}
