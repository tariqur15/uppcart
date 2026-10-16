<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Services\CollectionService;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function __construct(
        protected CollectionService $collectionService
    ) {}

    public function index(): View
    {
        $collections = Collection::active()
            ->withCount('products')
            ->latest()
            ->paginate(12);

        return view('collections.index', compact('collections'));
    }

    public function show(string $slug): View
    {
        $collection = Collection::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $products = $this->collectionService->getCollectionProducts($collection);

        return view('collections.show', compact('collection', 'products'));
    }
}
