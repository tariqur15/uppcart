<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTierPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'minimum_quantity',
        'maximum_quantity',
        'discount_type',
        'discount_value',
        'fixed_price',
        'priority',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'minimum_quantity' => 'integer',
            'maximum_quantity' => 'integer',
            'discount_value' => 'decimal:2',
            'fixed_price' => 'decimal:2',
            'priority' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
