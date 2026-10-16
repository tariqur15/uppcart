<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecomputeStock extends Command
{
    protected $signature = 'products:recompute-stock';

    protected $description = 'Recompute product stock from initial_stock and the order ledger';

    public function handle(): int
    {
        $activeStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed'];

        $items = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select('order_items.product_id', 'orders.status', DB::raw('SUM(order_items.quantity) as qty'))
            ->groupBy('order_items.product_id', 'orders.status')
            ->get();

        $totals = [];

        foreach ($items as $item) {
            if (! isset($totals[$item->product_id])) {
                $totals[$item->product_id] = 0;
            }

            if (in_array($item->status, $activeStatuses, true)) {
                $totals[$item->product_id] -= (int) $item->qty;
            } else {
                $totals[$item->product_id] += (int) $item->qty;
            }
        }

        $products = Product::where('type', '!=', 'digital')->get();

        foreach ($products as $product) {
            $delta = (int) ($totals[$product->id] ?? 0);
            $newStock = (int) $product->initial_stock + $delta;
            $product->stock = $newStock;
            $product->save();
            $this->line("Product #{$product->id} ({$product->name}): stock = {$newStock}");
        }

        $this->info('Done. '.$products->count().' products updated.');

        return 0;
    }
}
