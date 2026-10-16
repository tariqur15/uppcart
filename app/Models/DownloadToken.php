<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DownloadToken extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'token',
        'file_name',
        'file_path',
        'download_count',
        'download_limit',
        'download_expiry_days',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'download_count' => 'integer',
            'download_limit' => 'integer',
            'download_expiry_days' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function generateForOrder(Order $order): array
    {
        $tokens = [];

        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product->type !== 'digital' || empty($product->digital_files)) {
                continue;
            }

            foreach ($product->digital_files as $file) {
                $expiresAt = $product->download_expiry
                    ? now()->addDays($product->download_expiry)
                    : null;

                $tokens[] = self::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'token' => Str::random(64),
                    'file_name' => $file['name'] ?? basename($file['path'] ?? 'file'),
                    'file_path' => $file['path'] ?? '',
                    'download_limit' => $product->download_limit,
                    'download_expiry_days' => $product->download_expiry,
                    'expires_at' => $expiresAt,
                ]);
            }
        }

        return $tokens;
    }

    public function canDownload(): bool
    {
        if ($this->download_limit && $this->download_count >= $this->download_limit) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isLimitReached(): bool
    {
        return $this->download_limit && $this->download_count >= $this->download_limit;
    }
}
