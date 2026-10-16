<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Models\DownloadToken;
use App\Models\FraudCheck;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\FraudCheckService;
use App\Services\PathaoService;
use App\Services\RedxService;
use App\Services\SteadfastService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        // Base scope for date range (applies to both stats and list)
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $statsQuery = Order::query();
        if ($dateFrom) {
            $statsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $statsQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Stats (filtered by date range only)
        $totalOrders = (clone $statsQuery)->count();
        $pendingOrders = (clone $statsQuery)->where('status', 'pending')->count();
        $confirmedOrders = (clone $statsQuery)->where('status', 'confirmed')->count();
        $processingOrders = (clone $statsQuery)->where('status', 'processing')->count();
        $shippedOrders = (clone $statsQuery)->where('status', 'shipped')->count();
        $deliveredOrders = (clone $statsQuery)->where('status', 'delivered')->count();
        $completedOrders = (clone $statsQuery)->where('status', 'completed')->count();
        $cancelledOrders = (clone $statsQuery)->where('status', 'cancelled')->count();
        $returnedOrders = (clone $statsQuery)->where('status', 'returned')->count();
        $totalRevenue = (clone $statsQuery)->where('status', '!=', 'cancelled')->sum('total_price');

        // Filtered list query (date range + search + status + payment_status)
        $listQuery = Order::with('user', 'items.product', 'fraudCheck');

        if ($dateFrom) {
            $listQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $listQuery->whereDate('created_at', '<=', $dateTo);
        }

        if ($search = $request->input('search')) {
            $listQuery->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhere('total_price', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $listQuery->where('status', $status);
        }

        if ($paymentStatus = $request->input('payment_status')) {
            $listQuery->where('payment_status', $paymentStatus);
        }

        if ($paymentMethod = $request->input('payment_method')) {
            $listQuery->where('payment_method', $paymentMethod);
        }

        if ($courier = $request->input('courier')) {
            if ($courier === 'none') {
                $listQuery->whereNull('courier_name');
            } else {
                $listQuery->where('courier_name', $courier);
            }
        }

        if ($productId = $request->input('product_id')) {
            $listQuery->whereHas('items', fn ($q) => $q->where('product_id', $productId));
        }

        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $listQuery->oldest(),
            'high' => $listQuery->orderByDesc('total_price'),
            'low' => $listQuery->orderBy('total_price'),
            default => $listQuery->latest(),
        };

        $perPage = (int) $request->input('per_page', 10);
        $orders = $listQuery->paginate($perPage);
        $filteredTotal = $orders->total();

        $this->tagCustomerSegments($orders);

        $view = $request->input('view', 'orders');
        abort_unless(in_array($view, ['all', 'orders', 'abandoned']), 404);

        $abandonedQuery = AbandonedCart::with('user')
            ->abandoned()
            ->when($dateFrom, fn ($q) => $q->whereDate('last_activity_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('last_activity_at', '<=', $dateTo))
            ->when($reminderType = $request->input('reminder_type'), function ($q) use ($reminderType) {
                if ($reminderType === 'ignored') {
                    $q->whereNotNull('ignored_at');
                } elseif ($reminderType === 'none') {
                    $q->whereNull('reminder_type');
                } else {
                    $q->where('reminder_type', $reminderType);
                }
            })
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            }))
            ->latest('last_activity_at');

        $abandonedCarts = $view === 'orders' ? collect() : $abandonedQuery->paginate($perPage);
        $abandonedTotal = $abandonedQuery->count();

        return view('admin.orders.index', compact(
            'orders', 'totalOrders', 'pendingOrders', 'confirmedOrders', 'processingOrders',
            'shippedOrders', 'deliveredOrders', 'completedOrders', 'cancelledOrders', 'returnedOrders',
            'totalRevenue', 'filteredTotal', 'dateFrom', 'dateTo', 'perPage', 'sort',
            'view', 'abandonedCarts', 'abandonedTotal'
        ));
    }

    /**
     * Tag each order on the page as the customer's first ("new") or a repeat ("repeated") order.
     *
     * Customers are matched by user id, falling back to phone number.
     */
    private function tagCustomerSegments($orders): void
    {
        $userIds = $orders->pluck('user_id')->filter()->unique();
        $phones = $orders->pluck('phone')->filter()->unique();

        $firstByUser = Order::query()
            ->whereIn('user_id', $userIds)
            ->selectRaw('user_id, MIN(id) AS first_order_id')
            ->groupBy('user_id')
            ->pluck('first_order_id', 'user_id');

        $firstByPhone = Order::query()
            ->whereIn('phone', $phones)
            ->selectRaw('phone, MIN(id) AS first_order_id')
            ->groupBy('phone')
            ->pluck('first_order_id', 'phone');

        $orders->each(function (Order $order) use ($firstByUser, $firstByPhone) {
            $firstOrderId = min(
                $firstByUser[$order->user_id] ?? $order->id,
                $firstByPhone[$order->phone] ?? $order->id,
            );

            $order->customer_segment = $order->id === $firstOrderId ? 'new' : 'repeated';
        });
    }

    public function create(Request $request): View
    {
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $customer = null;

        if ($request->query('user_id')) {
            $customer = User::find($request->query('user_id'));
        }

        return view('admin.orders.create', compact('products', 'customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'thana' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'payment_method' => 'required|string',
            'payment_status' => 'required|in:unpaid,paid',
            'delivery_charge' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'order_note' => 'nullable|string',
            'internal_note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $items = collect($validated['items'])->filter(fn ($item) => ($item['quantity'] ?? 0) > 0);

        if ($items->isEmpty()) {
            return back()->withErrors(['items' => 'At least one item is required.'])->withInput();
        }

        $subtotal = $items->sum(fn ($item) => $item['price'] * $item['quantity']);
        $deliveryCharge = (float) ($validated['delivery_charge'] ?? 0);
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);
        $totalPrice = $subtotal + $deliveryCharge - $discountAmount;

        $orderNumber = 'ORD-'.now()->format('dmy').'-'.strtoupper(Str::random(6));

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => null,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'],
            'division' => $validated['division'] ?? null,
            'district' => $validated['district'] ?? null,
            'thana' => $validated['thana'] ?? null,
            'landmark' => $validated['landmark'] ?? null,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'status' => 'confirmed',
            'total_price' => $totalPrice,
            'delivery_charge' => $deliveryCharge,
            'discount_amount' => $discountAmount,
            'order_note' => $validated['order_note'] ?? null,
            'internal_note' => $validated['internal_note'] ?? null,
            'courier_name' => null,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $this->deductStockForOrder($order);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'note' => 'Order created by admin',
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order created successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Order::with('user', 'items.product');

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhere('total_price', 'like', "%{$search}%");
            });
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($paymentMethod = $request->input('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }
        if ($courier = $request->input('courier')) {
            if ($courier === 'none') {
                $query->whereNull('courier_name');
            } else {
                $query->where('courier_name', $courier);
            }
        }

        $query->latest();

        $filename = 'orders_export_'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Order #', 'Date', 'Customer', 'Phone', 'Email',
                'Items', 'Total', 'Payment Method', 'Payment Status',
                'Status', 'Courier', 'Address', 'IP Address',
            ]);

            $query->chunk(200, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $customerName = trim($order->first_name.' '.$order->last_name);
                    if (! $customerName && $order->user) {
                        $customerName = $order->user->name ?? '';
                    }

                    $itemSummary = $order->items->map(function ($item) {
                        $qty = $item->quantity;
                        $name = $item->product->name ?? $item->product_name ?? $item->name;

                        return "{$name} (x{$qty})";
                    })->implode('; ');

                    $courierLabel = match ($order->courier_name) {
                        'pathao' => 'Pathao',
                        'steadfast' => 'Steadfast',
                        'redx' => 'RedX',
                        default => '',
                    };

                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at->format('Y-m-d H:i'),
                        $customerName,
                        $order->phone,
                        $order->user->email ?? '',
                        $itemSummary,
                        number_format($order->total_price, 2),
                        strtoupper($order->payment_method),
                        $order->payment_status,
                        ucfirst($order->status),
                        $courierLabel,
                        $order->address,
                        $order->ip_address ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function show(Order $order): View
    {
        $order->load('user', 'items.product', 'statusHistory', 'downloadTokens.product', 'fraudCheck');

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $order->load('items.product');

        $currencySymbol = Setting::get('general.currency_symbol', '৳');

        return view('admin.orders.edit', compact('order', 'currencySymbol'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'thana' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'delivery_charge' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'order_note' => 'nullable|string',
            'internal_note' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer|exists:order_items,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:0',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        $order->update(collect($validated)->only([
            'first_name', 'last_name', 'phone', 'email', 'address',
            'division', 'district', 'thana', 'landmark',
            'delivery_charge', 'discount_amount', 'order_note', 'internal_note',
            'payment_method', 'payment_status',
        ])->toArray());

        if (isset($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    if (($itemData['quantity'] ?? 0) <= 0) {
                        OrderItem::where('id', $itemData['id'])->where('order_id', $order->id)->delete();
                    } else {
                        OrderItem::where('id', $itemData['id'])->where('order_id', $order->id)->update([
                            'quantity' => $itemData['quantity'],
                            'price' => $itemData['price'],
                        ]);
                    }
                } elseif (isset($itemData['product_id'])) {
                    if (($itemData['quantity'] ?? 0) > 0) {
                        $order->items()->create([
                            'product_id' => $itemData['product_id'],
                            'quantity' => $itemData['quantity'],
                            'price' => $itemData['price'] ?? Product::find($itemData['product_id'])->price,
                        ]);
                    }
                }
            }
        }

        $subtotal = $order->items()->sum(DB::raw('price * quantity'));
        $order->update(['total_price' => $subtotal + $order->delivery_charge - $order->discount_amount]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function searchProduct(Request $request, Order $order): JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'price', 'sku', 'image']);

        return response()->json($products);
    }

    public function updateDownloadToken(Request $request, Order $order, DownloadToken $downloadToken): RedirectResponse
    {
        abort_unless($downloadToken->order_id === $order->id, 404);

        $validated = $request->validate([
            'download_limit' => 'nullable|integer|min:0',
            'download_expiry_days' => 'nullable|integer|min:0',
        ]);

        $update = [];

        if (array_key_exists('download_limit', $validated)) {
            $update['download_limit'] = $validated['download_limit'] ?: null;
        }

        if (array_key_exists('download_expiry_days', $validated)) {
            $update['download_expiry_days'] = $validated['download_expiry_days'] ?: null;
            $update['expires_at'] = $validated['download_expiry_days']
                ? $downloadToken->created_at->addDays((int) $validated['download_expiry_days'])
                : null;
        }

        $downloadToken->update($update);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Download settings updated for "'.$downloadToken->file_name.'".');
    }

    private const STEP_ORDER = ['pending', 'confirmed', 'processing', 'shipped', 'completed', 'delivered'];

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,returned',
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        if ($order->status !== $newStatus) {
            $order->update(['status' => $newStatus]);
            $this->recordStatusSteps($order, $newStatus);
            $this->syncStockForStatusChange($order, $oldStatus, $newStatus);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated.');
    }

    public function bulkStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,returned',
        ]);

        $newStatus = $validated['status'];
        $now = now();

        $orders = Order::whereIn('id', $validated['ids'])->get();

        foreach ($orders as $order) {
            if ($order->status !== $newStatus) {
                $oldStatus = $order->status;
                $order->update(['status' => $newStatus]);
                $this->recordStatusSteps($order, $newStatus, $now);
                $this->syncStockForStatusChange($order, $oldStatus, $newStatus);
            }
        }

        return redirect()->route('admin.orders.index')
            ->with('success', count($validated['ids']).' order(s) updated to '.$newStatus.'.');
    }

    private function deductStockForOrder(Order $order): void
    {
        $this->adjustStockForOrder($order, -1);
    }

    private function restoreStockForOrder(Order $order): void
    {
        $this->adjustStockForOrder($order, 1);
    }

    private function adjustStockForOrder(Order $order, int $direction): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if (! $product || $product->type === 'digital') {
                continue;
            }

            if ($direction < 0) {
                $product->decrement('stock', $item->quantity);
            } else {
                $product->increment('stock', $item->quantity);
            }
        }
    }

    private function syncStockForStatusChange(Order $order, string $oldStatus, string $newStatus): void
    {
        $activeStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed'];
        $wasActive = in_array($oldStatus, $activeStatuses, true);
        $isActive = in_array($newStatus, $activeStatuses, true);

        if ($wasActive && ! $isActive) {
            $this->restoreStockForOrder($order);
        } elseif (! $wasActive && $isActive) {
            $this->deductStockForOrder($order);
        }
    }

    private function recordStatusSteps(Order $order, string $status, ?Carbon $timestamp = null): void
    {
        $timestamp = $timestamp ?? now();
        $targetIndex = array_search($status, self::STEP_ORDER);

        if ($targetIndex === false) {
            $order->statusHistory()->create([
                'status' => $status,
                'changed_at' => $timestamp,
            ]);

            return;
        }

        foreach (array_slice(self::STEP_ORDER, 0, $targetIndex + 1) as $stepStatus) {
            $existing = $order->statusHistory()->where('status', $stepStatus)->first();

            if (! $existing) {
                $order->statusHistory()->create([
                    'status' => $stepStatus,
                    'changed_at' => $timestamp,
                ]);
            }
        }
    }

    public function pushToSteadfast(Order $order, SteadfastService $steadfast): RedirectResponse
    {
        if ($order->consignment_id) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'This order has already been pushed to Steadfast. Consignment ID: '.$order->consignment_id);
        }

        $result = $steadfast->createConsignment($order);

        if ($result['success']) {
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Consignment pushed to Steadfast successfully. Tracking code: '.$result['tracking_code']);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('error', $result['message']);
    }

    public function pushToPathao(Order $order, PathaoService $pathao): RedirectResponse
    {
        if ($order->consignment_id) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'This order has already been pushed to a courier. Consignment ID: '.$order->consignment_id);
        }

        $result = $pathao->createConsignment($order);

        if ($result['success']) {
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Consignment pushed to Pathao successfully. Consignment ID: '.$result['consignment_id']);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('error', $result['message']);
    }

    public function pushToRedx(Order $order, RedxService $redx): RedirectResponse
    {
        if ($order->consignment_id) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'This order has already been pushed to a courier. Consignment ID: '.$order->consignment_id);
        }

        $result = $redx->createConsignment($order);

        if ($result['success']) {
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Consignment pushed to RedX successfully. Consignment ID: '.$result['consignment_id']);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('error', $result['message']);
    }

    public function invoice(Order $order): Response
    {
        $order->load('items.product');

        $settings = Setting::pluck('value', 'key')->toArray();
        $storeName = $settings['store_name'] ?? config('app.name', 'Store');
        $currencySymbol = match ($settings['currency'] ?? 'BDT') {
            'BDT' => 'Tk',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            default => '$',
        };

        $pdf = Pdf::loadView('orders.invoice', compact('order', 'storeName', 'currencySymbol', 'settings'));

        return $pdf->download('invoice-'.$order->order_number.'.pdf');
    }

    public function shippingLabel(Order $order): Response
    {
        $order->load('items.product');

        $settings = Setting::pluck('value', 'key')->toArray();
        $storeName = $settings['store_name'] ?? config('app.name', 'Store');
        $currencySymbol = match ($settings['currency'] ?? 'BDT') {
            'BDT' => 'Tk',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            default => '$',
        };
        $courierLabel = match ($order->courier_name) {
            'pathao' => 'Pathao',
            'redx' => 'RedX',
            default => 'Steadfast',
        };

        $barcodeHtml = null;
        if ($order->consignment_id) {
            $generator = new BarcodeGeneratorHTML;
            $barcodeHtml = $generator->getBarcode($order->consignment_id, $generator::TYPE_CODE_128, 2, 40);
        }

        $pdf = Pdf::loadView('admin.orders.shipping-label', compact('order', 'storeName', 'currencySymbol', 'courierLabel', 'barcodeHtml'));

        return $pdf->download('shipping-label-'.$order->order_number.'.pdf');
    }

    public function checkFraud(Order $order): JsonResponse
    {
        $phone = $order->phone ?? $order->user?->phone;

        if (! $phone) {
            return response()->json(['success' => false, 'message' => 'No phone number found for this order.']);
        }

        $fraudService = new FraudCheckService;
        $result = $fraudService->checkByPhone($phone);

        if ($result['success']) {
            $summary = $result['data']['summary'] ?? [];

            FraudCheck::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'phone' => $phone,
                    'data' => $result['data'],
                    'reports' => $result['reports'] ?? [],
                    'success_ratio' => $summary['success_ratio'] ?? 0,
                    'total_parcel' => $summary['total_parcel'] ?? 0,
                    'success_parcel' => $summary['success_parcel'] ?? 0,
                    'cancelled_parcel' => $summary['cancelled_parcel'] ?? 0,
                ]
            );
        }

        return response()->json($result);
    }

    public function geo(Order $order, Request $request): JsonResponse
    {
        $ip = $request->query('ip') ?: $order->ip_address;

        if (! $ip) {
            return response()->json(['success' => false, 'message' => 'No IP address recorded for this order.']);
        }

        $isPublic = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;

        if (! $isPublic) {
            return response()->json([
                'success' => false,
                'message' => 'Private or reserved IP — location cannot be determined.',
            ]);
        }

        $data = Cache::remember('geo_ip_'.md5($ip), now()->addDay(), function () use ($ip) {
            try {
                $response = Http::timeout(5)->retry(1, 500)->get("https://ipwho.is/{$ip}");
            } catch (ConnectionException) {
                return ['success' => false, 'message' => 'IP lookup service unreachable.'];
            }

            abort_unless($response->ok(), 502, 'IP lookup service unreachable.');

            return $response->json();
        });

        if (! ($data['success'] ?? false)) {
            Cache::forget('geo_ip_'.md5($ip));

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Unable to locate this IP.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ip' => $data['ip'] ?? $ip,
                'city' => $data['city'] ?? null,
                'region' => $data['region'] ?? null,
                'country' => $data['country'] ?? null,
                'country_code' => $data['country_code'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'postal' => $data['postal'] ?? null,
                'isp' => $data['connection']['isp'] ?? null,
                'org' => $data['connection']['org'] ?? null,
                'asn' => $data['connection']['asn'] ?? null,
                'timezone' => $data['timezone']['id'] ?? null,
            ],
        ]);
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->items()->delete();
        $order->statusHistory()->delete();
        $order->downloadTokens()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
