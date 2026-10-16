<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Product::with('category')->orderByDesc('created_at')->orderByDesc('id')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'disponible' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(Product::create($validated)->load('category'), 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('category'));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'disponible' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $product->update($validated);

        return response()->json($product->fresh()->load('category'));
    }

    public function destroy(Product $product): JsonResponse
    {
        if (OrderDetail::where('product_id', $product->id)->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el producto porque está asociado a uno o más pedidos.',
            ], 409);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}
