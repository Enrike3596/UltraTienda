<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            OrderDetail::with(['order', 'product'])->orderByDesc('created_at')->orderByDesc('id')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        return response()->json(OrderDetail::create($validated)->load(['order', 'product']), 201);
    }

    public function show(OrderDetail $orderDetail): JsonResponse
    {
        return response()->json($orderDetail->load(['order', 'product']));
    }

    public function update(Request $request, OrderDetail $orderDetail): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $orderDetail->update($validated);

        return response()->json($orderDetail->fresh()->load(['order', 'product']));
    }

    public function destroy(OrderDetail $orderDetail): JsonResponse
    {
        $orderDetail->delete();

        return response()->json(null, 204);
    }
}
