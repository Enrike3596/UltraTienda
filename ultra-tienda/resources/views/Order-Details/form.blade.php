@php
    $detail = $detail ?? $orderDetail ?? null;
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
                        @selected((string) old('order_id', $detail?->order_id) === (string) $order->id)
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
            <label class="ut-label" for="product_id">
                Producto <span class="text-destructive">*</span>
            </label>
            <select
                id="product_id"
                name="product_id"
                required
                @class(['ut-select', 'ut-select-error' => $errors->has('product_id')])
            >
                <option value="">Selecciona un producto</option>
                @foreach ($products as $product)
                    <option
                        value="{{ $product->id }}"
                        @selected((string) old('product_id', $detail?->product_id) === (string) $product->id)
                    >
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
            @error('product_id')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="ut-field-row">
        <div class="ut-field">
            <label class="ut-label" for="quantity">
                Cantidad <span class="text-destructive">*</span>
            </label>
            <input
                type="number"
                id="quantity"
                name="quantity"
                min="1"
                step="1"
                value="{{ old('quantity', $detail?->quantity ?? 1) }}"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('quantity')])
            >
            @error('quantity')
                <p class="ut-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="ut-field">
            <label class="ut-label" for="price">
                Precio unitario <span class="text-destructive">*</span>
            </label>
            <input
                type="number"
                id="price"
                name="price"
                min="0"
                step="0.01"
                value="{{ old('price', $detail?->price) }}"
                placeholder="0.00"
                required
                @class(['ut-input', 'ut-input-error' => $errors->has('price')])
            >
            @error('price')
                <p class="ut-error">{{ $message }}</p>
            @enderror
            <p class="ut-hint">Precio al momento de la venta; no cambia si el producto se modifica.</p>
        </div>
    </div>
</div>
