<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'tier_price',
        'original_price',
        'discount',
        'product_tier_price_id',
        'flash_sale_id',
        'flash_sale_product_id',
        'variant',
        'custom_fields_data',
    ];

    protected $casts = [
        'variant' => 'array',
        'custom_fields_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tierPrice(): BelongsTo
    {
        return $this->belongsTo(ProductTierPrice::class);
    }

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function flashSaleProduct(): BelongsTo
    {
        return $this->belongsTo(FlashSaleProduct::class);
    }
}
