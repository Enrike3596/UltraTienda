<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:recent,price_asc,price_desc,name'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->whereHas('category', fn (Builder $builder) => $builder->where('is_active', true));

        $this->applyFilters($query, $filters);
        $this->applySort($query, $filters['sort'] ?? 'recent');

        $paginator = $query->paginate($filters['per_page'] ?? config('storefront.per_page'));

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn (Product $product) => $this->present($product))
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        abort_unless($product->is_active && $product->category?->is_active, 404);

        return response()->json([
            'data' => $this->present($product->loadMissing('category')),
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (filled($filters['search'] ?? null)) {
            $term = '%'.trim((string) $filters['search']).'%';

            $query->where(fn (Builder $builder) => $builder
                ->whereLike('name', $term)
                ->orWhereLike('description', $term));
        }

        $query
            ->when(filled($filters['category_id'] ?? null), fn (Builder $builder) => $builder->where('category_id', $filters['category_id']))
            ->when(filled($filters['min_price'] ?? null), fn (Builder $builder) => $builder->where('price', '>=', $filters['min_price']))
            ->when(filled($filters['max_price'] ?? null), fn (Builder $builder) => $builder->where('price', '<=', $filters['max_price']));
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('id'),
            'name' => $query->orderBy('name')->orderBy('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => (float) $product->price,
            'currency' => config('storefront.currency'),
            'image' => $product->image_url ? url($product->image_url) : null,
            'available_for_sale' => $product->disponible,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
            'created_at' => $product->created_at?->toIso8601String(),
        ];
    }
}
