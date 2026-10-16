<?php

use App\Http\Controllers\Admin\AbandonedCartController as AdminAbandonedCartController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\AttributeController as AdminAttributeController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FlashSaleController as AdminFlashSaleController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PopupController as AdminPopupController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PurchaseOrderController as AdminPurchaseOrderController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SalesReportController as AdminSalesReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionController as PublicCollectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FlashSaleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PasswordSetupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PopupEventController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrackOrderController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/track-order', [TrackOrderController::class, 'index'])->name('track-order');
Route::post('/track-order', [TrackOrderController::class, 'search'])->name('track-order.search');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search/ajax', [ProductController::class, 'searchAjax'])->name('products.search.ajax');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/flash-sale', [FlashSaleController::class, 'index'])->name('flash-sale.index');

Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');

Route::get('/collections', [PublicCollectionController::class, 'index'])->name('collections.index');
Route::get('/collections/{slug}', [PublicCollectionController::class, 'show'])->name('collections.show');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::delete('/cart/clear', [CartController::class, 'clearAll'])->name('cart.clear');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart management (requires auth)
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Legacy order URL redirect
    Route::get('/orders/{order}', fn ($order) => redirect()->route('orders.show', $order, 301));
});

// Cart page (guest + auth)
Route::get('/cart/loading', [CartController::class, 'loading'])->name('cart.loading');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Cart add + session remove (guest + auth)
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::get('/cart/drawer', [CartController::class, 'drawer'])->name('cart.drawer');
Route::patch('/cart/session/{productId}', [CartController::class, 'updateSession'])->name('cart.update-session');
Route::delete('/cart/session/{productId}', [CartController::class, 'destroySession'])->name('cart.destroy-session');

// Restore a saved abandoned cart (signed link shared via WhatsApp)
Route::get('/cart/restore/{abandonedCart}', [CartController::class, 'restore'])->name('cart.restore')->middleware('signed');

// Guest checkout (no auth required)
Route::get('/checkout/loading', [CheckoutController::class, 'loading'])->name('checkout.loading');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('check.blocked');
Route::post('/validate-promo', [CheckoutController::class, 'validatePromo'])->name('checkout.validate-promo');
Route::post('/checkout/track', [CheckoutController::class, 'trackAbandon'])->name('checkout.track');

// Order confirmation (guest access via session phone, handled in controller)
Route::get('/checkout/confirmation/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/checkout/confirmation/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice.download');
Route::get('/checkout/confirmation/{order}/print', [OrderController::class, 'printInvoice'])->name('orders.invoice.print');

// Live chat (storefront)
Route::post('/live-chat/start', [LiveChatController::class, 'start'])->name('live-chat.start');
Route::post('/live-chat/faq', [LiveChatController::class, 'faq'])->name('live-chat.faq');
Route::post('/live-chat/connect', [LiveChatController::class, 'connect'])->name('live-chat.connect');
Route::post('/live-chat/message', [LiveChatController::class, 'message'])->name('live-chat.message');
Route::get('/live-chat/state', [LiveChatController::class, 'state'])->name('live-chat.state');
Route::get('/live-chat/messages', [LiveChatController::class, 'messages'])->name('live-chat.messages');

// Password setup (guest access)
Route::get('/set-password/{token}', [PasswordSetupController::class, 'show'])->name('password.setup');
Route::post('/set-password/{token}', [PasswordSetupController::class, 'store'])->name('password.setup.store');

// Digital product downloads
Route::get('/download/{downloadToken}', [DownloadController::class, 'show'])->name('download.show');

// Storefront popup analytics tracking
Route::post('/popup/track', [PopupEventController::class, 'track'])->name('popup.track');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', AdminSearchController::class)->name('search');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/search/ajax', [AdminProductController::class, 'searchAjax'])->name('products.search-ajax');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');

    Route::post('/products/bulk/delete', [AdminProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::patch('/products/bulk/toggle-status', [AdminProductController::class, 'bulkToggleStatus'])->name('products.bulk-toggle-status');
    Route::patch('/products/bulk/update', [AdminProductController::class, 'bulkUpdate'])->name('products.bulk-update');

    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('/products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::get('/products/{product}/analytics', [AdminProductController::class, 'analytics'])->name('products.analytics');
    Route::post('/products/slug-check', [AdminProductController::class, 'slugCheck'])->name('products.slug-check');
    Route::post('/products/upload-custom-field-file', [AdminProductController::class, 'uploadCustomFieldFile'])->name('products.upload-custom-field-file');
    Route::post('/products/upload-digital-file', [AdminProductController::class, 'uploadDigitalFile'])->name('products.upload-digital-file');
    Route::get('/products/{product}/tier-pricing', [AdminProductController::class, 'tierPricing'])->name('products.tier-pricing');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/bulk/delete', [AdminCategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
    Route::post('/categories/{category}/toggle-featured', [AdminCategoryController::class, 'toggleFeatured'])->name('categories.toggle-featured');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::post('/abandoned-carts/{abandonedCart}/reminder', [AdminAbandonedCartController::class, 'recordReminder'])->name('abandoned-carts.reminder');
    Route::post('/abandoned-carts/{abandonedCart}/ignore', [AdminAbandonedCartController::class, 'ignore'])->name('abandoned-carts.ignore');
    Route::get('/orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [AdminOrderController::class, 'store'])->name('orders.store')->middleware('check.blocked');
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/search-product', [AdminOrderController::class, 'searchProduct'])->name('orders.search-product');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/bulk-status', [AdminOrderController::class, 'bulkStatus'])->name('orders.bulk-status');
    Route::post('/orders/{order}/push-steadfast', [AdminOrderController::class, 'pushToSteadfast'])->name('orders.push-steadfast');
    Route::post('/orders/{order}/push-pathao', [AdminOrderController::class, 'pushToPathao'])->name('orders.push-pathao');
    Route::post('/orders/{order}/push-redx', [AdminOrderController::class, 'pushToRedx'])->name('orders.push-redx');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/shipping-label', [AdminOrderController::class, 'shippingLabel'])->name('orders.shipping-label');
    Route::get('/orders/{order}/check-fraud', [AdminOrderController::class, 'checkFraud'])->name('orders.check-fraud');
    Route::get('/orders/{order}/geo', [AdminOrderController::class, 'geo'])->name('orders.geo');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    Route::patch('/orders/{order}/download-token/{downloadToken}', [AdminOrderController::class, 'updateDownloadToken'])->name('orders.update-download-token');

    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [AdminNotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/mark-read', [AdminNotificationController::class, 'markRead'])->name('notifications.mark-read');

    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::patch('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
    Route::get('/sales-reports', [AdminSalesReportController::class, 'index'])->name('sales-reports.index');
    Route::post('/sales-reports/export', [AdminSalesReportController::class, 'export'])->name('sales-reports.export');

    Route::get('/flash-sales', [AdminFlashSaleController::class, 'index'])->name('flash-sales.index');
    Route::get('/flash-sales/create', [AdminFlashSaleController::class, 'create'])->name('flash-sales.create');
    Route::post('/flash-sales', [AdminFlashSaleController::class, 'store'])->name('flash-sales.store');
    Route::get('/flash-sales/search-products/ajax', [AdminFlashSaleController::class, 'searchProducts'])->name('flash-sales.search-products');
    Route::get('/flash-sales/{flashSale}/edit', [AdminFlashSaleController::class, 'edit'])->name('flash-sales.edit');
    Route::patch('/flash-sales/{flashSale}', [AdminFlashSaleController::class, 'update'])->name('flash-sales.update');
    Route::get('/flash-sales/{flashSale}', [AdminFlashSaleController::class, 'show'])->name('flash-sales.show');
    Route::post('/flash-sales/{flashSale}/end', [AdminFlashSaleController::class, 'end'])->name('flash-sales.end');
    Route::post('/flash-sales/{flashSale}/duplicate', [AdminFlashSaleController::class, 'duplicate'])->name('flash-sales.duplicate');
    Route::delete('/flash-sales/{flashSale}', [AdminFlashSaleController::class, 'destroy'])->name('flash-sales.destroy');

    Route::get('/attributes', [AdminAttributeController::class, 'index'])->name('attributes.index');
    Route::get('/attributes/create', [AdminAttributeController::class, 'create'])->name('attributes.create');
    Route::post('/attributes', [AdminAttributeController::class, 'store'])->name('attributes.store');
    Route::get('/attributes/{attribute}/edit', [AdminAttributeController::class, 'edit'])->name('attributes.edit');
    Route::patch('/attributes/{attribute}', [AdminAttributeController::class, 'update'])->name('attributes.update');
    Route::delete('/attributes/{attribute}', [AdminAttributeController::class, 'destroy'])->name('attributes.destroy');

    Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [AdminBrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [AdminBrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [AdminBrandController::class, 'edit'])->name('brands.edit');
    Route::patch('/brands/{brand}', [AdminBrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [AdminBrandController::class, 'destroy'])->name('brands.destroy');
    Route::post('/brands/bulk-delete', [AdminBrandController::class, 'bulkDelete'])->name('brands.bulk-delete');

    Route::get('/tags', [AdminTagController::class, 'index'])->name('tags.index');
    Route::get('/tags/create', [AdminTagController::class, 'create'])->name('tags.create');
    Route::post('/tags', [AdminTagController::class, 'store'])->name('tags.store');
    Route::get('/tags/{tag}/edit', [AdminTagController::class, 'edit'])->name('tags.edit');
    Route::patch('/tags/{tag}', [AdminTagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [AdminTagController::class, 'destroy'])->name('tags.destroy');
    Route::post('/tags/bulk-delete', [AdminTagController::class, 'bulkDelete'])->name('tags.bulk-delete');
    Route::post('/tags/quick-create', [AdminTagController::class, 'quickCreate'])->name('tags.quick-create');

    Route::get('/collections', [AdminCollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/create', [AdminCollectionController::class, 'create'])->name('collections.create');
    Route::post('/collections', [AdminCollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/{collection}/edit', [AdminCollectionController::class, 'edit'])->name('collections.edit');
    Route::patch('/collections/{collection}', [AdminCollectionController::class, 'update'])->name('collections.update');
    Route::delete('/collections/{collection}', [AdminCollectionController::class, 'destroy'])->name('collections.destroy');
    Route::get('/collections/search-products', [AdminCollectionController::class, 'searchProducts'])->name('collections.search-products');
    Route::post('/collections/preview', [AdminCollectionController::class, 'preview'])->name('collections.preview');
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/create', [AdminReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [AdminReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/bulk-approve', [AdminReviewController::class, 'bulkApprove'])->name('reviews.bulk-approve');
    Route::post('/reviews/bulk-delete', [AdminReviewController::class, 'bulkDelete'])->name('reviews.bulk-delete');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
    Route::get('/reviews/{review}/edit', [AdminReviewController::class, 'edit'])->name('reviews.edit');
    Route::patch('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/suppliers', [AdminSupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [AdminSupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [AdminSupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}/edit', [AdminSupplierController::class, 'edit'])->name('suppliers.edit');
    Route::patch('/suppliers/{supplier}', [AdminSupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [AdminSupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::get('/purchase-orders', [AdminPurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [AdminPurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::post('/purchase-orders', [AdminPurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::get('/purchase-orders/search-products', [AdminPurchaseOrderController::class, 'searchProduct'])->name('purchase-orders.search-products');
    Route::get('/purchase-orders/{purchaseOrder}', [AdminPurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [AdminPurchaseOrderController::class, 'markReceived'])->name('purchase-orders.receive');
    Route::post('/purchase-orders/{purchaseOrder}/cancel', [AdminPurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    Route::delete('/purchase-orders/{purchaseOrder}', [AdminPurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');

    Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [AdminBannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [AdminBannerController::class, 'edit'])->name('banners.edit');
    Route::patch('/banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');

    Route::redirect('/popups', '/admin/settings?tab=additional')->name('popups.index');
    Route::redirect('/popups/create', '/admin/settings?tab=additional')->name('popups.create');
    Route::post('/popups', [AdminPopupController::class, 'store'])->name('popups.store');
    Route::redirect('/popups/{popup}/edit', '/admin/settings?tab=additional')->name('popups.edit');
    Route::match(['post', 'patch'], '/popups/{popup}', [AdminPopupController::class, 'update'])->name('popups.update');
    Route::delete('/popups/{popup}', [AdminPopupController::class, 'destroy'])->name('popups.destroy');
    Route::get('/popups/{popup}/analytics', [AdminPopupController::class, 'analytics'])->name('popups.analytics');
    Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::get('/blogs', [AdminBlogController::class, 'index'])->name('blogs.index');

    Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/export', [AdminCustomerController::class, 'export'])->name('customers.export');
    Route::get('/customers/{user}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{user}/edit', [AdminCustomerController::class, 'edit'])->name('customers.edit');
    Route::patch('/customers/{user}', [AdminCustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/{user}/block', [AdminCustomerController::class, 'block'])->name('customers.block');
    Route::post('/customers/{user}/restore', [AdminCustomerController::class, 'restore'])->name('customers.restore');
    Route::post('/customers/{user}/message', [AdminCustomerController::class, 'sendMessage'])->name('customers.message');
    Route::get('/customers/{user}/messages/poll', [AdminCustomerController::class, 'pollMessages'])->name('customers.messages.poll');
    Route::get('/messages/{message}/attachment', [AdminCustomerController::class, 'downloadAttachment'])->name('customers.message.attachment');

    Route::get('/media/recent', [AdminProductController::class, 'recentMedia'])->name('media.recent');
    Route::post('/media/upload', [AdminProductController::class, 'uploadMedia'])->name('media.upload');
    Route::post('/media/delete', [AdminProductController::class, 'deleteMedia'])->name('media.delete');
});

Route::get('/debug/edit-html/{id}', function ($id) {
    $product = Product::findOrFail($id);
    $html = view('admin.products.edit', ['product' => $product])->render();
    // Extract x-data attributes for inspection
    preg_match_all('/x-data="([^"]+)"/', $html, $matches);

    return response('<pre style="font-size:11px;white-space:pre-wrap;word-break:break-all">'
        .htmlspecialchars(implode("\n\n==========\n\n", $matches[1]))
        .'</pre>')->header('Content-Type', 'text/html');
})->middleware('web');

require __DIR__.'/auth.php';
