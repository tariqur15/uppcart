<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraudCheck extends Model
{
    protected $fillable = [
        'order_id',
        'phone',
        'data',
        'reports',
        'success_ratio',
        'total_parcel',
        'success_parcel',
        'cancelled_parcel',
    ];

    protected $casts = [
        'data' => 'array',
        'reports' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
