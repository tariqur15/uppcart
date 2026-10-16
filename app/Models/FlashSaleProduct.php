<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSaleProduct extends Model
{
    /** @use HasFactory<\Database\Factories\FlashSaleProductFactory> */
    use HasFactory;

    public const SCOPE_WHOLE = 'whole';

    public const SCOPE_VARIANTS = 'variants';

    public const DISCOUNT_FIXED_PRICE = 'fixed_price';

    public const DISCOUNT_PERCENTAGE = 'percentage';

    public const DISCOUNT_FIXED_AMOUNT = 'fixed_amount';

    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'scope',
        'variant',
        'discount_type',
        'discount_value',
        'regular_price',
        'flash_price',
        'sale_stock',
        'sold',
        'max_per_customer',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'flash_price' => 'decimal:2',
        'sale_stock' => 'integer',
        'sold' => 'integer',
        'max_per_customer' => 'integer',
    ];

    public function flashSale()
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function remaining(): int
    {
        if ($this->sale_stock === null) {
            return PHP_INT_MAX;
        }

        return max(0, $this->sale_stock - $this->sold);
    }

    public function isWholeProduct(): bool
    {
        return $this->scope === self::SCOPE_WHOLE;
    }
}
