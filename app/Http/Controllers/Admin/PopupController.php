<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use App\Models\PopupEvent;
use App\Services\PopupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PopupController extends Controller
{
    public function store(Request $request, PopupService $service): JsonResponse|RedirectResponse
    {
        $data = $this->validated($request);
        $data['config'] = $this->persistImage($request, $data['config']);

        $popup = Popup::create($data);

        return $this->popupSaveResponse($request, 'Popup created successfully.');
    }

    public function update(Request $request, Popup $popup, PopupService $service): JsonResponse|RedirectResponse
    {
        $data = $this->validated($request);
        $data['config'] = $this->persistImage($request, $data['config'], $popup);

        $popup->update($data);

        return $this->popupSaveResponse($request, 'Popup updated successfully.');
    }

    public function destroy(Popup $popup): RedirectResponse
    {
        $config = $popup->getConfig();

        if (! empty($config['image'])) {
            Storage::disk('public')->delete($config['image']);
        }

        $popup->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'additional', 'sub' => 'popup'])
            ->with('success', 'Popup deleted successfully.');
    }

    public function analytics(Popup $popup, PopupService $service): View
    {
        return view('admin.popups.analytics', [
            'popup' => $popup,
            'config' => $service->config($popup),
            'stats' => $this->statsFor($popup),
        ]);
    }

    private function popupSaveResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.settings.index', ['tab' => 'additional', 'sub' => 'popup']),
            ]);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'additional', 'sub' => 'popup'])
            ->with('success', $message);
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'config' => 'required|string|json',
            'image' => $request->isMethod('post')
                ? 'required|image|mimes:jpeg,png,jpg,webp,gif|max:4096'
                : 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'remove_image' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $config = json_decode($validated['config'], true);
        $config = is_array($config) ? $config : [];

        return [
            'name' => $validated['name'],
            'config' => $config,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];
    }

    private function persistImage(Request $request, array $config, ?Popup $popup = null): array
    {
        $previousImage = $popup ? ($popup->getConfig()['image'] ?? null) : null;

        if ($request->boolean('remove_image')) {
            if ($previousImage) {
                Storage::disk('public')->delete($previousImage);
            }

            $config['image'] = null;

            return $config;
        }

        if ($request->hasFile('image')) {
            if ($previousImage) {
                Storage::disk('public')->delete($previousImage);
            }

            $config['image'] = $request->file('image')->store('popups', 'public');
        }

        return $config;
    }

    private function statsFor(Popup $popup): array
    {
        $events = PopupEvent::query()
            ->where('popup_id', $popup->id)
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(type = 'impression') as impressions")
            ->selectRaw("SUM(type = 'view') as views")
            ->selectRaw("SUM(type = 'click') as clicks")
            ->selectRaw("SUM(type = 'close') as closes")
            ->first();

        $views = (int) $events->views;
        $clicks = (int) $events->clicks;

        return [
            'impressions' => (int) $events->impressions,
            'views' => $views,
            'clicks' => $clicks,
            'closes' => (int) $events->closes,
            'ctr' => $views > 0 ? round(($clicks / $views) * 100, 1) : 0.0,
        ];
    }
}