<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;

class OrderPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderPayments = OrderPayment::with('order')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('order-payments.index', compact('orderPayments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('order-payments.create', [
            'orders' => Order::select('id', 'customer_name')->orderByDesc('id')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_status' => ['sometimes', 'string', 'max:50'],
            'payment_date' => ['nullable', 'date'],
        ]);

        OrderPayment::create($validated);

        return redirect()->route('order-payments.index')->with('success', 'Pago creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderPayment $orderPayment)
    {
        $orderPayment->load('order');

        return view('order-payments.show', compact('orderPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderPayment $orderPayment)
    {
        return view('order-payments.edit', [
            'orderPayment' => $orderPayment,
            'orders' => Order::select('id', 'customer_name')->orderByDesc('id')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderPayment $orderPayment)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_status' => ['sometimes', 'string', 'max:50'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $orderPayment->update($validated);

        return redirect()->route('order-payments.index')->with('success', 'Pago actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderPayment $orderPayment)
    {
        $orderPayment->delete();

        return redirect()->route('order-payments.index')->with('success', 'Pago eliminado exitosamente.');
    }
}
