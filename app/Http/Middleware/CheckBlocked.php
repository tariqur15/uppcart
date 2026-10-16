<?php

namespace App\Http\Middleware;

use App\Models\BlockedEntry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (BlockedEntry::isIpBlocked($ip)) {
            abort(403, 'Your IP address has been blocked.');
        }

        if ($user = $request->user()) {
            $phone = $user->phone;

            if (BlockedEntry::isPhoneBlocked($phone)) {
                abort(403, 'Your account has been blocked.');
            }
        }

        if ($phone = $request->input('phone')) {
            if (BlockedEntry::isPhoneBlocked($phone)) {
                abort(403, 'This phone number has been blocked.');
            }
        }

        return $next($request);
    }
}
