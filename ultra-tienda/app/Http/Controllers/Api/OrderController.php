<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Order::with('user')->withCount('details')->orderByDesc('created_at')->orderByDesc('id')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'string', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_address' => ['required', 'string', 'max:255'],
            'subtotal' => ['sometimes', 'numeric', 'min:0'],
            'shipping_cost' => ['sometimes', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'max:50'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['sometimes', 'string', 'max:50'],
        ]);

        return response()->json(Order::create($validated)->load('user'), 201);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load(['user', 'details.product', 'payments']));
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'string', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_address' => ['required', 'string', 'max:255'],
            'subtotal' => ['sometimes', 'numeric', 'min:0'],
            'shipping_cost' => ['sometimes', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'max:50'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['sometimes', 'string', 'max:50'],
        ]);

        $order->update($validated);

        return response()->json($order->fresh()->load(['user', 'details.product', 'payments']));
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json(null, 204);
    }
}
