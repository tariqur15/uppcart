<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['type', 'title', 'message', 'url', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public static function createForOrder(string $type, Order $order): self
    {
        $phone = $order->phone ?? '';
        $shortPhone = $phone ? substr($phone, -4) : '????';
        $total = number_format($order->total_price, 2);

        return match ($type) {
            'new_order' => static::create([
                'type' => 'new_order',
                'title' => 'New Order Received',
                'message' => "Order #{$order->order_number} ({$shortPhone}) - Tk {$total}",
                'url' => route('admin.orders.show', $order),
            ]),
            default => throw new \InvalidArgumentException("Unknown notification type: {$type}"),
        };
    }
}
