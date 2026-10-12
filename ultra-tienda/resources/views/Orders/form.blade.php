@php
    $order = $order ?? null;

    $statusOptions = [
        'pending' => 'Pendiente',
        'processing' => 'En preparación',
        'paid' => 'Pagado',
        'shipped' => 'Enviado',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
    ];

    $paymentStatusOptions = [
        'pending' => 'Pendiente',
        'partial' => 'Parcial',
        'paid' => 'Pagado',
        'failed' => 'Fallido',
        'refunded' => 'Reembolsado',
    ];

    $paymentMethodOptions = [
        '' => 'Sin método definido',
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia bancaria',
        'tarjeta' => 'Tarjeta',
        'pse' => 'PSE',
        'otro' => 'Otro',
    ];
@endphp

<div class="ut-card-body flex flex-col gap-5">
    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="customer_name">
                Cliente <span class="text-destructive">*</span>
            </label>
            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="{{ old('customer_name', $order?->customer_name) }}"
                placeholder="Nombre completo"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('customer_name')])
            >
            @error('customer_name')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="customer_email">Correo electrónico</label>
            <input
                type="email"
                id="customer_email"
                name="customer_email"
                value="{{ old('customer_email', $order?->customer_email) }}"
                placeholder="cliente@correo.com"
                @class(['ut-input', 'ut-input-error' => $errors->has('customer_email')])
            >
            @error('customer_email')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="customer_phone">
                Teléfono <span class="text-destructive">*</span>
            </label>
            <input
                type="text"
                id="customer_phone"
                name="customer_phone"
                value="{{ old('customer_phone', $order?->customer_phone) }}"
                placeholder="300 000 0000"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('customer_phone')])
            >
            @error('customer_phone')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="customer_address">
                Dirección de entrega <span class="text-destructive">*</span>
            </label>
            <input
                type="text"
                id="customer_address"
                name="customer_address"
                value="{{ old('customer_address', $order?->customer_address) }}"
                placeholder="Calle, número, ciudad"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('customer_address')])
            >
            @error('customer_address')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="user_id">Usuario vinculado</label>
            <select id="user_id" name="user_id" @class(['ut-select', 'ut-select-error' => $errors->has('user_id')])>
                <option value="">Compra sin usuario registrado</option>
                @foreach ($users as $user)
                    <option
                        value="{{ $user->id }}"
                        @selected((string) old('user_id', $order?->user_id) === (string) $user->id)
                    >
                        {{ $user->name }} · {{ $user->email }}
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="payment_method">Método de pago</label>
            <select
                id="payment_method"
                name="payment_method"
                @class(['ut-select', 'ut-select-error' => $errors->has('payment_method')])
            >
                @foreach ($paymentMethodOptions as $value => $label)
                    <option
                        value="{{ $value }}"
                        @selected(old('payment_method', $order?->payment_method) === $value)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('payment_method')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="subtotal">Subtotal</label>
            <input
                type="number"
                id="subtotal"
                name="subtotal"
                step="0.01"
                min="0"
                value="{{ old('subtotal', $order?->subtotal ?? 0) }}"
                @class(['ut-input', 'ut-input-error' => $errors->has('subtotal')])
            >
            @error('subtotal')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="shipping_cost">Domicilio</label>
            <input
                type="number"
                id="shipping_cost"
                name="shipping_cost"
                step="0.01"
                min="0"
                value="{{ old('shipping_cost', $order?->shipping_cost ?? 0) }}"
                @class(['ut-input', 'ut-input-error' => $errors->has('shipping_cost')])
            >
            @error('shipping_cost')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="total">
                Total <span class="text-destructive">*</span>
            </label>
            <input
                type="number"
                id="total"
                name="total"
                step="0.01"
                min="0"
                value="{{ old('total', $order?->total) }}"
                placeholder="0.00"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('total')])
            >
            @error('total')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="status">Estado del pedido</label>
            <select id="status" name="status" @class(['ut-select', 'ut-select-error' => $errors->has('status')])>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $order?->status ?? 'pending') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
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
                        @selected(old('payment_status', $order?->payment_status ?? 'pending') === $value)
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
</div>
