<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductIndexRequest;
use App\Services\Contracts\ProductSearchServiceInterface;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductSearchServiceInterface $searchService,
    ) {
    }

    public function index(ProductIndexRequest $request): JsonResponse
    {
        $filters = $request->validated();

        $products = $this->searchService->search($filters);

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}
