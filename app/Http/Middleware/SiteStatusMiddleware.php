<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteStatusMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin*') || $request->is('api/*') || $request->is('login') || $request->is('register') || $request->is('forgot-password*') || $request->is('reset-password*') || $request->is('verify-identity') || $request->is('verify-email*') || $request->is('up') || $request->is('checkout') || $request->is('checkout/*') || $request->is('set-password*') || $request->is('cart') || $request->is('cart/*') || $request->is('track-order')) {
            return $next($request);
        }

        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        $settings = Setting::pluck('value', 'key')->toArray();

        if (($settings['maintenance_mode'] ?? '0') === '1') {
            return response()->view('maintenance', [], 503);
        }

        if (($settings['coming_soon'] ?? '0') === '1') {
            $launchDate = $settings['coming_soon_launch_date'] ?? null;

            return response()->view('coming-soon', ['launchDate' => $launchDate], 200);
        }

        return $next($request);
    }
}
