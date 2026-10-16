<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductView extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'ip_address',
        'user_agent',
        'session_id',
        'browser',
        'os',
        'device_type',
        'country',
        'city',
        'traffic_source',
        'referrer_url',
        'landing_url',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function parseUserAgent(string $userAgent): array
    {
        $browser = 'Other';
        if (str_contains($userAgent, 'Firefox') && ! str_contains($userAgent, 'Seamonkey')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Edg')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'Chrome') && ! str_contains($userAgent, 'Edg')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Safari') && ! str_contains($userAgent, 'Chrome')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR')) {
            $browser = 'Opera';
        }

        $os = 'Other';
        if (str_contains($userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgent, 'Mac OS X')) {
            $os = 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $os = 'iOS';
        }

        $deviceType = 'desktop';
        if (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (str_contains($userAgent, 'iPad')) {
            $deviceType = 'tablet';
        }

        return ['browser' => $browser, 'os' => $os, 'device_type' => $deviceType];
    }

    public static function parseTrafficSource(?string $referrer): string
    {
        if (! $referrer) {
            return 'direct';
        }

        $host = strtolower(parse_url($referrer, PHP_URL_HOST) ?? '');

        if (str_contains($host, 'google')) {
            return 'google';
        }
        if (str_contains($host, 'facebook') || str_contains($host, 'fb.com')) {
            return 'facebook';
        }
        if (str_contains($host, 'instagram')) {
            return 'instagram';
        }
        if (str_contains($host, 'twitter') || str_contains($host, 'x.com')) {
            return 'twitter';
        }
        if (str_contains($host, 'tiktok')) {
            return 'tiktok';
        }
        if (str_contains($host, 'youtube')) {
            return 'youtube';
        }
        if (str_contains($host, 'pinterest')) {
            return 'pinterest';
        }
        if (str_contains($host, 'linkedin')) {
            return 'linkedin';
        }
        if (str_contains($host, 'reddit')) {
            return 'reddit';
        }

        return 'referral';
    }
}
