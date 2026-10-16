<?php

namespace App\Http\Controllers;

use App\Models\PopupEvent;
use App\Services\PopupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopupEventController extends Controller
{
    public function track(Request $request, PopupService $service): JsonResponse
    {
        $validated = $request->validate([
            'popup_id' => 'required|integer|exists:popups,id',
            'type' => 'required|in:impression,view,click,close',
        ]);

        PopupEvent::create([
            'popup_id' => $validated['popup_id'],
            'type' => $validated['type'],
            'page' => $request->input('page', ''),
            'device' => $service->device($request),
        ]);

        return response()->json(['ok' => true]);
    }
}
