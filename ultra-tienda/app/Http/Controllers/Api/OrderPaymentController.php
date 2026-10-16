<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderPaymentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            OrderPayment::with('order')->orderByDesc('created_at')->orderByDesc('id')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_status' => ['sometimes', 'string', 'max:50'],
            'payment_date' => ['nullable', 'date'],
        ]);

        return response()->json(OrderPayment::create($validated)->load('order'), 201);
    }

    public function show(OrderPayment $orderPayment): JsonResponse
    {
        return response()->json($orderPayment->load('order'));
    }

    public function update(Request $request, OrderPayment $orderPayment): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_status' => ['sometimes', 'string', 'max:50'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $orderPayment->update($validated);

        return response()->json($orderPayment->fresh()->load('order'));
    }

    public function destroy(OrderPayment $orderPayment): JsonResponse
    {
        $orderPayment->delete();

        return response()->json(null, 204);
    }
}
