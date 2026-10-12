@php
    $payment = $payment ?? $orderPayment ?? null;

    $paymentMethodOptions = [
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia bancaria',
        'tarjeta' => 'Tarjeta',
        'pse' => 'PSE',
        'otro' => 'Otro',
    ];

    $paymentStatusOptions = [
        'pending' => 'Pendiente',
        'partial' => 'Parcial',
        'paid' => 'Pagado',
        'failed' => 'Fallido',
        'refunded' => 'Reembolsado',
    ];
@endphp

<div class="ut-card-body flex flex-col gap-5">
    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="order_id">
                Pedido <span class="text-destructive">*</span>
            </label>
            <select id="order_id" name="order_id" required @class(['ut-select', 'ut-select-error' => $errors->has('order_id')])>
                <option value="">Selecciona un pedido</option>
                @foreach ($orders as $order)
                    <option
                        value="{{ $order->id }}"
                        @selected((string) old('order_id', $payment?->order_id) === (string) $order->id)
                    >
                        #{{ $order->id }} · {{ $order->customer_name }}
                    </option>
                @endforeach
            </select>
            @error('order_id')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="amount">
                Monto <span class="text-destructive">*</span>
            </label>
            <input
                type="number"
                id="amount"
                name="amount"
                min="0"
                step="0.01"
                value="{{ old('amount', $payment?->amount) }}"
                placeholder="0.00"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('amount')])
            >
            @error('amount')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="payment_method">
                Método de pago <span class="text-destructive">*</span>
            </label>
            <select
                id="payment_method"
                name="payment_method"
                required
                @class(['ut-select', 'ut-select-error' => $errors->has('payment_method')])
            >
                <option value="">Selecciona un método</option>
                @foreach ($paymentMethodOptions as $value => $label)
                    <option
                        value="{{ $value }}"
                        @selected(old('payment_method', $payment?->payment_method) === $value)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('payment_method')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="payment_status">Estado del pago</label>
            <select
                id="payment_status"
                name="payment_status"
                @class(['ut-select', 'ut-select-error' => $errors->has('payment_status')])
            >
                @foreach ($paymentStatusOptions as $value => $label)
                    <option
                        value="{{ $value }}"
                        @selected(old('payment_status', $payment?->payment_status ?? 'pending') === $value)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('payment_status')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="payment_date">Fecha de pago</label>
            <input
                type="datetime-local"
                id="payment_date"
                name="payment_date"
                value="{{ old('payment_date', $payment?->payment_date?->format('Y-m-d\TH:i')) }}"
                @class(['ut-input', 'ut-input-error' => $errors->has('payment_date')])
            >
            @error('payment_date')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
