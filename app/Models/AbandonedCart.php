<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'name',
        'phone',
        'address',
        'coupon_code',
        'items_data',
        'total',
        'item_count',
        'last_activity_at',
        'recovered_at',
        'reminder_type',
        'last_reminder_sent_at',
        'reminder_sent_count',
        'ignored_at',
    ];

    protected $casts = [
        'items_data' => 'array',
        'total' => 'decimal:2',
        'last_activity_at' => 'datetime',
        'recovered_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'reminder_sent_count' => 'integer',
        'ignored_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('recovered_at')->where('item_count', '>', 0);
    }

    public function scopeAbandoned($query)
    {
        return $query->active();
    }

    public function scopeIgnored($query)
    {
        return $query->whereNotNull('ignored_at')->whereNull('recovered_at');
    }
}
