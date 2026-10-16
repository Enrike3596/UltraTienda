<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Category::withCount('products')->orderBy('orden')->orderBy('id')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'description' => ['nullable', 'string'],
            'orden' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(Category::create($validated), 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($category->loadCount('products'));
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string'],
            'orden' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $category->update($validated);

        return response()->json($category->fresh());
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene productos asociados.',
            ], 409);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}
