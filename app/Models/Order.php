<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $ddmmyy = now()->format('dmy');
            $random = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            $order->order_number = 'ORD-'.$ddmmyy.'-'.$random;
        });

        static::updating(function (Order $order) {
            if (! $order->isDirty('courier_status')) {
                return;
            }

            $old = $order->getOriginal('courier_status');
            $new = $order->courier_status;

            if ($old === $new || $new === null) {
                return;
            }

            $label = match (true) {
                str_contains(strtolower($new), 'cancel') => 'Shipment cancelled',
                str_contains(strtolower($new), 'return') => 'Shipment returned',
                str_contains(strtolower($new), 'delivered') => 'Shipment delivered',
                str_contains(strtolower($new), 'picked') || str_contains(strtolower($new), 'pickup') => 'Courier accepted pickup',
                str_contains(strtolower($new), 'in_transit') || str_contains(strtolower($new), 'on_the_way') || str_contains(strtolower($new), 'in_review') => 'Courier pickup requested',
                str_contains(strtolower($new), 'pending') => 'Courier pickup requested',
                default => 'Courier status: '.$new,
            };

            $courier = match ($order->courier_name) {
                'pathao' => 'Pathao',
                'steadfast' => 'Steadfast',
                'redx' => 'RedX',
                default => 'Courier',
            };

            Notification::create([
                'type' => 'courier_update',
                'title' => $courier.' — '.$label,
                'message' => '#'.$order->order_number.' — '.$new,
                'url' => route('admin.orders.show', $order),
            ]);
        });
    }

    protected $fillable = [
        'order_number',
        'user_id',
        'total_price',
        'status',
        'payment_status',
        'address',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address_type',
        'division',
        'district',
        'thana',
        'landmark',
        'delivery_option',
        'delivery_charge',
        'payment_method',
        'order_note',
        'internal_note',
        'promo_code',
        'discount_amount',
        'consignment_id',
        'tracking_code',
        'courier_status',
        'courier_name',
        'courier_pushed_at',
        'tracking_link',
        'ip_address',
        'device_id',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'courier_pushed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('changed_at');
    }

    public function downloadTokens(): HasMany
    {
        return $this->hasMany(DownloadToken::class);
    }

    public function fraudCheck(): HasOne
    {
        return $this->hasOne(FraudCheck::class);
    }

    public function hasDigitalItems(): bool
    {
        return $this->items()->whereHas('product', fn ($q) => $q->where('type', 'digital'))->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }
}
