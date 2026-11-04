<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_address' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'string', Rule::in(config('storefront.payment_methods'))],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $items = collect($validated['items']);
        $products = Product::query()
            ->with('category')
            ->whereIn('id', $items->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $this->ensureProductsArePurchasable($items, $products);

        [$subtotal, $shippingCost, $total] = $this->calculateTotals($items, $products);

        $order = DB::transaction(function () use ($validated, $items, $products, $subtotal, $shippingCost, $total): Order {
            $order = Order::create([
                'user_id' => null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
            ]);

            foreach ($items as $item) {
                $order->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $products[$item['product_id']]->price,
                ]);
            }

            return $order;
        });

        return response()->json([
            'message' => 'Pedido creado correctamente.',
            'data' => $this->present($order->load('details.product')),
        ], 201);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array{product_id: int, quantity: int}>  $items
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    private function ensureProductsArePurchasable($items, $products): void
    {
        $errors = [];

        foreach ($items as $index => $item) {
            $product = $products->get($item['product_id']);

            if (! $product instanceof Product) {
                $errors["items.$index.product_id"] = 'El producto seleccionado no existe.';

                continue;
            }

            if (! $product->is_active || ! $product->category?->is_active) {
                $errors["items.$index.product_id"] = 'El producto ya no está disponible en la tienda.';

                continue;
            }

            if (! $product->disponible) {
                $errors["items.$index.product_id"] = "El producto {$product->name} no tiene stock disponible.";
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array{product_id: int, quantity: int}>  $items
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     * @return array{0: float, 1: float, 2: float}
     */
    private function calculateTotals($items, $products): array
    {
        $subtotal = round($items->sum(
            fn (array $item) => (float) $products[$item['product_id']]->price * $item['quantity'],
        ), 2);

        $shippingCost = $subtotal >= (float) config('storefront.free_shipping_threshold')
            ? 0.0
            : (float) config('storefront.shipping_cost');

        return [$subtotal, $shippingCost, round($subtotal + $shippingCost, 2)];
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Order $order): array
    {
        return [
            'id' => $order->id,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'customer_address' => $order->customer_address,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'subtotal' => (float) $order->subtotal,
            'shipping_cost' => (float) $order->shipping_cost,
            'total' => (float) $order->total,
            'created_at' => $order->created_at?->toIso8601String(),
            'items' => $order->details->map(fn ($detail) => [
                'product_id' => $detail->product_id,
                'name' => $detail->product?->name,
                'quantity' => $detail->quantity,
                'unit_price' => (float) $detail->price,
                'line_total' => round((float) $detail->price * $detail->quantity, 2),
            ])->all(),
        ];
    }
}
