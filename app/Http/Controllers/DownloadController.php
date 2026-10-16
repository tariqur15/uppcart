<?php

namespace App\Http\Controllers;

use App\Models\DownloadToken;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function show(DownloadToken $downloadToken)
    {
        $order = $downloadToken->order;

        if (auth()->check()) {
            abort_if($order->user_id !== auth()->id(), 403);
        } else {
            $phone = session('last_order_phone');

            if (! $phone || $order->phone !== $phone) {
                abort(403);
            }
        }

        if (! $downloadToken->canDownload()) {
            $reason = $downloadToken->isExpired()
                ? 'This download link has expired.'
                : 'Download limit reached for this file.';

            return back()->withErrors(['download' => $reason]);
        }

        $diskPath = $downloadToken->file_path;

        if (! Storage::disk('public')->exists($diskPath)) {
            abort(404, 'File not found.');
        }

        $downloadToken->increment('download_count');

        return Storage::disk('public')->download(
            $diskPath,
            $downloadToken->file_name
        );
    }
}
