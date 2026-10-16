<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Services\AbandonedCartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AbandonedCartController extends Controller
{
    public function __construct(
        private readonly AbandonedCartService $abandonedCartService,
    ) {}

    public function recordReminder(Request $request, AbandonedCart $abandonedCart): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:reminder,stock,discount'],
        ]);

        $this->abandonedCartService->recordReminder($abandonedCart, $validated['type']);

        return response()->json(['success' => true]);
    }

    public function ignore(AbandonedCart $abandonedCart): RedirectResponse
    {
        $this->abandonedCartService->toggleIgnore($abandonedCart);

        return redirect()->back()->with('success', 'Abandoned cart updated.');
    }
}
