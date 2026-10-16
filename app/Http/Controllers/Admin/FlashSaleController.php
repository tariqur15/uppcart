<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\Product;
use App\Services\FlashSaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FlashSaleController extends Controller
{
    public function __construct(
        private readonly FlashSaleService $flashSaleService,
    ) {}

    public function index(Request $request)
    {
        $statuses = [
            'all' => 'All',
            'draft' => 'Draft',
            'upcoming' => 'Upcoming',
            'active' => 'Active',
            'ended' => 'Ended',
            'cancelled' => 'Cancelled',
        ];

        $query = FlashSale::query();

        $search = trim((string) $request->input('search'));
        $status = $request->input('status', 'all');
        $dateFilter = $request->input('date', '');
        $from = $request->input('from');
        $to = $request->input('to');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $now = now();

        if ($status === 'upcoming') {
            $query->where('status', FlashSale::STATUS_SCHEDULED)->where('starts_at', '>', $now);
        } elseif ($status === 'active') {
            $query->where('status', FlashSale::STATUS_ACTIVE)
                ->where('starts_at', '<=', $now)
                ->where('ends_at', '>=', $now);
        } elseif (in_array($status, [FlashSale::STATUS_DRAFT, FlashSale::STATUS_ENDED, FlashSale::STATUS_CANCELLED], true)) {
            $query->where('status', $status);
        }

        if ($dateFilter === 'today') {
            $query->where(function ($q) use ($now) {
                $q->whereDate('starts_at', $now->toDateString())
                    ->orWhereDate('ends_at', $now->toDateString());
            });
        } elseif ($dateFilter === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateFilter === 'month') {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($dateFilter === 'custom' && $from && $to) {
            $query->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        }

        $query->withCount('products as products_count');

        $allowedSorts = ['name', 'starts_at', 'ends_at', 'products_count', 'created_at', 'status'];

        if (in_array($sort, $allowedSorts, true)) {
            $sortable = match ($sort) {
                'starts_at' => 'starts_at',
                'ends_at' => 'ends_at',
                'products' => 'products_count',
                'units' => 'units_sold',
                'revenue' => 'revenue',
                default => $sort,
            };

            if (in_array($sortable, ['units_sold', 'revenue'], true)) {
                $query->withSum('orderItems as units_sold', 'quantity');
                $query->withSum('orderItems as revenue', 'price');
            }

            $query->orderBy($sortable, $direction);
        }

        $sales = $query->paginate(15)->withQueryString();

        foreach ($sales as $sale) {
            $sale->effective_status = $this->flashSaleService->status($sale);
        }

        return view('admin.flash-sales.index', compact('sales', 'statuses', 'search', 'status', 'dateFilter', 'from', 'to', 'sort', 'direction'));
    }

    public function create(Request $request)
    {
        return view('admin.flash-sales.create', ['flashSale' => null, 'initialProductsJs' => '[]']);
    }

    public function store(Request $request)
    {
        $data = $this->validateSale($request);

        $sale = FlashSale::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'status' => $data['status'],
            'created_by' => $request->user()?->id,
        ]);

        $this->syncProducts($sale, $data['products']);

        return redirect()
            ->route('admin.flash-sales.show', $sale)
            ->with('success', 'Flash Sale created successfully.');
    }

    public function show(FlashSale $flashSale)
    {
        $sale = $flashSale;
        $sale->effective_status = $this->flashSaleService->status($sale);

        $sale->load(['products.product']);

        $soldCount = $sale->products->sum('sold');
        $totalRevenue = 0.0;
        $totalDiscount = 0.0;

        foreach ($sale->products as $fp) {
            $totalRevenue += (float) $fp->flash_price * $fp->sold;
            $totalDiscount += ((float) $fp->regular_price - (float) $fp->flash_price) * $fp->sold;
        }

        $orders = Order::whereHas('items', fn ($q) => $q->where('flash_sale_id', $sale->id))->count();
        $averageOrderValue = $orders > 0 ? round($totalRevenue / $orders, 2) : 0.0;

        $bestSeller = $sale->products->sortByDesc('sold')->first();

        return view('admin.flash-sales.show', compact('sale', 'soldCount', 'totalRevenue', 'totalDiscount', 'orders', 'averageOrderValue', 'bestSeller'));
    }

    public function edit(FlashSale $flashSale)
    {
        $sale = $flashSale;
        $sale->load(['products.product']);

        $initial = collect();

        foreach ($sale->products->where('scope', FlashSaleProduct::SCOPE_WHOLE) as $fp) {
            $initial->push([
                'key' => 'edit_'.$fp->id,
                'product_id' => $fp->product_id,
                'product_name' => $fp->product?->name,
                'image' => $fp->product?->image ? url('storage/'.$fp->product->image) : null,
                'regular_price' => (float) $fp->regular_price,
                'variants' => [],
                'scope' => 'whole',
                'discount_type' => $fp->discount_type,
                'discount_value' => (float) $fp->discount_value,
                'sale_stock' => $fp->sale_stock ?? '',
                'max_per_customer' => $fp->max_per_customer ?? '',
            ]);
        }

        foreach ($sale->products->where('scope', FlashSaleProduct::SCOPE_VARIANTS)->sortBy('variant')->groupBy('product_id') as $productId => $fps) {
            $first = $fps->first();

            $initial->push([
                'key' => 'edit_v_'.$first->id,
                'product_id' => $first->product_id,
                'product_name' => $first->product?->name,
                'image' => $first->product?->image ? url('storage/'.$first->product->image) : null,
                'regular_price' => (float) $first->regular_price,
                'variants' => $fps->map(fn ($fp) => [
                    'name' => $fp->variant,
                    'regular_price' => (float) $fp->regular_price,
                    'discount_type' => $fp->discount_type,
                    'discount_value' => (float) $fp->discount_value,
                    'sale_stock' => $fp->sale_stock ?? '',
                    'max_per_customer' => $fp->max_per_customer ?? '',
                ])->values()->all(),
                'scope' => 'variants',
                'discount_type' => 'percentage',
                'discount_value' => 0,
                'sale_stock' => '',
                'max_per_customer' => '',
            ]);
        }

        $initialProductsJs = $initial->values()->toJson();

        return view('admin.flash-sales.create', compact('sale', 'flashSale', 'initialProductsJs'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $data = $this->validateSale($request, $flashSale);

        $flashSale->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
        ]);

        if (! $this->flashSaleService->hasOrders($flashSale)) {
            $flashSale->update(['status' => $data['status']]);
            $this->syncProducts($flashSale, $data['products']);
        } else {
            $this->syncProductsForOrderedSale($flashSale, $data['products']);
        }

        return redirect()
            ->route('admin.flash-sales.show', $flashSale)
            ->with('success', 'Flash Sale updated successfully.');
    }

    public function searchProducts(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $query = Product::query()
            ->where('status', 'active')
            ->with(['category'])
            ->limit(20);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        } else {
            $query->latest();
        }

        return response()->json(
            $query->get()->map(function ($product) {
                $variants = $product->variants_enabled ? ($product->variant_data ?? []) : [];

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'image' => $product->image ? url('storage/'.$product->image) : null,
                    'price' => (float) ($product->sale_price ?? $product->price),
                    'stock' => (int) $product->stock,
                    'variants_enabled' => (bool) $product->variants_enabled,
                    'variants' => $product->variants_enabled
                        ? collect($variants)->map(fn ($v, $name) => [
                            'name' => (string) $name,
                            'price' => (float) ($v['price'] ?? ($product->sale_price ?? $product->price)),
                            'stock' => (int) ($v['stock'] ?? 0),
                            'sku' => $v['sku'] ?? null,
                        ])->values()
                        : [],
                ];
            })
        );
    }

    public function end(Request $request, FlashSale $flashSale)
    {
        $flashSale->update([
            'status' => FlashSale::STATUS_ENDED,
            'ends_at' => now(),
        ]);

        return back()->with('success', 'Flash Sale ended. Normal pricing has been restored.');
    }

    public function duplicate(Request $request, FlashSale $flashSale)
    {
        $copy = FlashSale::create([
            'name' => $flashSale->name.' (Copy)',
            'description' => $flashSale->description,
            'starts_at' => null,
            'ends_at' => null,
            'status' => FlashSale::STATUS_DRAFT,
            'created_by' => $request->user()?->id,
        ]);

        foreach ($flashSale->products as $fp) {
            $copy->products()->create([
                'product_id' => $fp->product_id,
                'scope' => $fp->scope,
                'variant' => $fp->variant,
                'discount_type' => $fp->discount_type,
                'discount_value' => $fp->discount_value,
                'regular_price' => $fp->regular_price,
                'flash_price' => $fp->flash_price,
                'sale_stock' => $fp->sale_stock,
                'sold' => 0,
                'max_per_customer' => $fp->max_per_customer,
            ]);
        }

        return redirect()
            ->route('admin.flash-sales.edit', $copy)
            ->with('success', 'Flash Sale duplicated as a draft.');
    }

    public function destroy(Request $request, FlashSale $flashSale)
    {
        if ($this->flashSaleService->hasOrders($flashSale)) {
            $flashSale->update(['status' => FlashSale::STATUS_CANCELLED]);

            return back()->with('warning', 'This Flash Sale has order history and cannot be deleted. It has been cancelled/archived instead.');
        }

        $flashSale->products()->delete();
        $flashSale->delete();

        return redirect()
            ->route('admin.flash-sales.index')
            ->with('success', 'Flash Sale deleted successfully.');
    }

    /**
     * Validate the flash sale request.
     *
     * @return array<string, mixed>
     */
    private function validateSale(Request $request, ?FlashSale $current = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', 'in:draft,scheduled,active,ended,cancelled'],
            'products' => ['required', 'json'],
        ], [
            'ends_at.after' => 'End date/time must be after start date/time.',
            'products.required' => 'Select at least one product.',
            'products.json' => 'Products data is invalid.',
        ]);

        $products = json_decode($data['products'], true);

        if (! is_array($products) || count($products) === 0) {
            throw \Illuminate\Validation\ValidationException::withMessages(['products' => 'Select at least one product.']);
        }

        $rows = $this->buildProductRows($products);

        if (empty($rows)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['products' => 'At least one valid product with a flash price is required.']);
        }

        $noDiscount = collect($rows)->filter(fn ($row) => (float) $row['flash_price'] >= (float) $row['regular_price']);

        if ($noDiscount->isNotEmpty()) {
            $messages = $noDiscount->map(function ($row) {
                $product = Product::find($row['product_id']);

                return sprintf(
                    '"%s"%s must have a flash price lower than its regular price.',
                    $product?->name ?? "Product #{$row['product_id']}",
                    $row['variant'] ? " ({$row['variant']})" : ''
                );
            });

            throw \Illuminate\Validation\ValidationException::withMessages(['products' => $messages->values()->all()]);
        }

        $this->checkOverlap($rows, $data['starts_at'], $data['ends_at'], $current);

        return [
            'name' => $data['name'],
            'description' => $data['description'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'status' => $data['status'],
            'products' => $rows,
        ];
    }

    /**
     * Build normalized flash sale product rows from the submitted payload.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildProductRows(array $products): array
    {
        $rows = [];

        foreach ($products as $entry) {
            $productId = (int) ($entry['product_id'] ?? 0);
            $product = Product::find($productId);

            if (! $product) {
                continue;
            }

            $scope = ($entry['scope'] ?? 'whole') === 'variants' ? FlashSaleProduct::SCOPE_VARIANTS : FlashSaleProduct::SCOPE_WHOLE;

            if ($scope === FlashSaleProduct::SCOPE_WHOLE) {
                $regular = (float) ($product->sale_price ?? $product->price);
                $row = $this->buildRow($product, null, $regular, $entry);

                if ($row) {
                    $rows[] = $row;
                }

                continue;
            }

            foreach (($entry['variants'] ?? []) as $variantEntry) {
                $variantName = trim((string) ($variantEntry['name'] ?? ''));
                $variant = $product->variant_data[$variantName] ?? null;

                if ($variantName === '' || ! $variant) {
                    continue;
                }

                $regular = (float) ($variant['price'] ?? ($product->sale_price ?? $product->price));

                $row = $this->buildRow($product, $variantName, $regular, $variantEntry);

                if ($row) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildRow(Product $product, ?string $variant, float $regular, array $entry): ?array
    {
        $discountType = (string) ($entry['discount_type'] ?? FlashSaleProduct::DISCOUNT_PERCENTAGE);
        $discountValue = (float) ($entry['discount_value'] ?? 0);

        if (! in_array($discountType, [FlashSaleProduct::DISCOUNT_FIXED_PRICE, FlashSaleProduct::DISCOUNT_PERCENTAGE, FlashSaleProduct::DISCOUNT_FIXED_AMOUNT], true)) {
            return null;
        }

        $flashPrice = match ($discountType) {
            FlashSaleProduct::DISCOUNT_FIXED_PRICE => $discountValue,
            FlashSaleProduct::DISCOUNT_PERCENTAGE => $regular * (1 - (float) $discountValue / 100),
            FlashSaleProduct::DISCOUNT_FIXED_AMOUNT => $regular - (float) $discountValue,
            default => $regular,
        };

        $flashPrice = round(max(0, $flashPrice), 2);

        if ($flashPrice <= 0) {
            return null;
        }

        return [
            'product_id' => $product->id,
            'scope' => $variant ? FlashSaleProduct::SCOPE_VARIANTS : FlashSaleProduct::SCOPE_WHOLE,
            'variant' => $variant,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'regular_price' => $regular,
            'flash_price' => $flashPrice,
            'sale_stock' => isset($entry['sale_stock']) && $entry['sale_stock'] !== '' ? max(0, (int) $entry['sale_stock']) : null,
            'max_per_customer' => isset($entry['max_per_customer']) && $entry['max_per_customer'] !== '' ? max(1, (int) $entry['max_per_customer']) : null,
        ];
    }

    /**
     * Persist flash sale product rows.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncProducts(FlashSale $sale, array $rows): void
    {
        $sale->products()->delete();

        foreach ($rows as $row) {
            $sale->products()->create($row);
        }
    }

    /**
     * For sales that already have orders, only add/update-new rows and keep
     * historical rows untouched so existing order prices are preserved.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncProductsForOrderedSale(FlashSale $sale, array $rows): void
    {
        $existing = $sale->products()->get()->keyBy(fn ($fp) => $fp->variant ? $fp->product_id.'|'.$fp->variant : $fp->product_id.'|');

        foreach ($rows as $row) {
            $key = $row['variant'] ? $row['product_id'].'|'.$row['variant'] : $row['product_id'].'|';

            if ($existing->has($key)) {
                $existing->get($key)->update([
                    'sale_stock' => $row['sale_stock'],
                ]);

                continue;
            }

            $sale->products()->create($row);
        }
    }

    /**
     * Prevent the same product/variant from being in overlapping scheduled or
     * active flash sales with a conflicting price.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function checkOverlap(array $rows, string $startsAt, string $endsAt, ?FlashSale $current = null): void
    {
        $start = Carbon::parse($startsAt);
        $end = Carbon::parse($endsAt);

        $conflicts = [];

        foreach ($rows as $row) {
            $query = FlashSaleProduct::query()
                ->where('product_id', $row['product_id'])
                ->where(fn ($q) => $q->whereNull('variant')->orWhere('variant', $row['variant']))
                ->whereHas('flashSale', function ($q) use ($start, $end, $current) {
                    $q->whereIn('status', [FlashSale::STATUS_ACTIVE, FlashSale::STATUS_SCHEDULED])
                        ->whereNotNull('starts_at')
                        ->whereNotNull('ends_at')
                        ->where('starts_at', '<', $end)
                        ->where('ends_at', '>', $start);

                    if ($current) {
                        $q->where('id', '!=', $current->id);
                    }
                })
                ->with('flashSale')
                ->first();

            if ($query) {
                $product = Product::find($row['product_id']);
                $conflicts[] = "\"{$product?->name}\" is already in \"{$query->flashSale->name}\" during this period.";
            }
        }

        if (! empty($conflicts)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['products' => array_values(array_unique($conflicts))]);
        }
    }
}
