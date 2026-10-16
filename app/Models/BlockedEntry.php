<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedEntry extends Model
{
    protected $fillable = [
        'type',
        'value',
        'blocked_at',
        'blocked_by',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
    ];

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public static function isPhoneBlocked(string $phone): bool
    {
        return static::where('type', 'phone')->where('value', $phone)->exists();
    }

    public static function isIpBlocked(string $ip): bool
    {
        return static::where('type', 'ip')->where('value', $ip)->exists();
    }

    public static function blockPhone(string $phone, ?int $blockedBy = null): self
    {
        return static::firstOrCreate(
            ['type' => 'phone', 'value' => $phone],
            ['blocked_by' => $blockedBy, 'blocked_at' => now()],
        );
    }

    public static function blockIp(string $ip, ?int $blockedBy = null): self
    {
        return static::firstOrCreate(
            ['type' => 'ip', 'value' => $ip],
            ['blocked_by' => $blockedBy, 'blocked_at' => now()],
        );
    }

    public static function unblockPhone(string $phone): void
    {
        static::where('type', 'phone')->where('value', $phone)->delete();
    }

    public static function unblockIp(string $ip): void
    {
        static::where('type', 'ip')->where('value', $ip)->delete();
    }
}
