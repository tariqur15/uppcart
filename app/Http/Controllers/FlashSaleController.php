<?php

namespace App\Http\Controllers;

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Services\FlashSaleService;

class FlashSaleController extends Controller
{
    public function __construct(
        private readonly FlashSaleService $flashSaleService,
    ) {}

    public function index()
    {
        $flashProducts = FlashSaleProduct::query()
            ->whereHas('flashSale', fn ($q) => $q
                ->where('status', FlashSale::STATUS_ACTIVE)
                ->whereNotNull('starts_at')
                ->whereNotNull('ends_at')
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now()))
            ->with(['flashSale', 'product'])
            ->whereHas('product', fn ($q) => $q->where('status', 'active'))
            ->orderBy('product_id')
            ->paginate(12)
            ->withQueryString();

        $countdown = $flashProducts->getCollection()
            ->pluck('flashSale')
            ->unique('id')
            ->first();

        return view('flash-sale.index', compact('flashProducts', 'countdown'));
    }
}
