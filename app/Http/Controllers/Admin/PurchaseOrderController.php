<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = PurchaseOrder::with(['supplier'])->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($search = $request->input('search')) {
            $query->where('order_number', 'like', "%{$search}%");
        }

        $purchaseOrders = $query->latest()->paginate(15)->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.purchase-orders.create', compact('suppliers'));
    }

    public function searchProduct(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('q', ''));

        $query = Product::query()
            ->where('status', 'active')
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
            $query->get()->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'image' => $product->image ? url('storage/'.$product->image) : null,
                'stock' => (int) $product->stock,
                'cost_price' => (float) ($product->cost_price ?? 0),
            ])
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $purchaseOrder = DB::transaction(function () use ($validated) {
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'status' => PurchaseOrder::STATUS_PENDING,
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $total = 0;

            $products = Product::whereIn('id', array_column($validated['items'], 'product_id'))->get()->keyBy('id');

            foreach ($validated['items'] as $item) {
                if (! isset($products[$item['product_id']])) {
                    continue;
                }

                $subtotal = $item['quantity'] * $item['unit_cost'];
                $total += $subtotal;

                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $products[$item['product_id']]->name,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $subtotal,
                ]);
            }

            $purchaseOrder->update(['total_amount' => $total]);

            return $purchaseOrder;
        });

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order created successfully. Mark it as received to add stock.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'items.product']);

        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function markReceived(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if (! $purchaseOrder->isPending()) {
            return back()->with('error', 'Only pending purchase orders can be marked as received.');
        }

        $purchaseOrder->markReceived();

        return back()->with('success', 'Purchase order received. Product stock has been updated.');
    }

    public function cancel(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if (! $purchaseOrder->isPending()) {
            return back()->with('error', 'Only pending purchase orders can be cancelled.');
        }

        $purchaseOrder->update(['status' => PurchaseOrder::STATUS_CANCELLED]);

        return back()->with('success', 'Purchase order cancelled.');
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->isReceived()) {
            return back()->with('error', 'Received purchase orders cannot be deleted — they have already added stock.');
        }

        $purchaseOrder->delete();

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }
}