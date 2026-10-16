<?php

namespace App\Models;

use Database\Factories\PurchaseOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    /** @use HasFactory<PurchaseOrderFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_RECEIVED = 'received';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_RECEIVED,
        self::STATUS_CANCELLED,
    ];

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $purchaseOrder) {
            $date = $purchaseOrder->created_at ?? now();
            $purchaseOrder->order_number = 'PO-'.$date->format('dmy').'-'.strtoupper(Str::random(4));
        });
    }

    protected $fillable = [
        'order_number',
        'supplier_id',
        'status',
        'total_amount',
        'expected_date',
        'notes',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'expected_date' => 'date',
            'received_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isReceived(): bool
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function markReceived(): void
    {
        DB::transaction(function () {
            $this->update([
                'status' => self::STATUS_RECEIVED,
                'received_at' => now(),
            ]);

            foreach ($this->items as $item) {
                if ($item->product !== null) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        });
    }
}