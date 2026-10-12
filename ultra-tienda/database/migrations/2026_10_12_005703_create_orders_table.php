<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla: pedidos — spec: id, usuario_id opcional, cliente, teléfono, dirección,
// subtotal, domicilio, total, estado, estado_pago.
// Registra cada compra y su evolución.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // CAMBIO vs. versión anterior: usuario OPCIONAL (antes obligatorio) para
            // permitir compra de invitado; SET NULL conserva el pedido si se borra el usuario.
            $table->foreignId('user_id')->nullable()->constrained('users')
                ->cascadeOnUpdate()->nullOnDelete();
            // Datos del cliente (spec: cliente, teléfono, dirección).
            $table->string('customer_name')->comment('Nombre del cliente (spec: cliente)');
            // Extra útil no exigido por la spec: correo para notificar el pedido.
            $table->string('customer_email')->nullable()->comment('Correo para notificaciones del pedido');
            $table->string('customer_phone')->comment('Teléfono de contacto/entrega (spec: teléfono)');
            $table->string('customer_address')->comment('Dirección de entrega (spec: dirección)');
            // CAMBIO: se agregan subtotal y domicilio; el total se llama "total"
            // (antes "total_amount"). Debe cumplirse: subtotal + domicilio = total.
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Suma de los productos (spec: subtotal)');
            $table->decimal('shipping_cost', 12, 2)->default(0)->comment('Valor del domicilio / envío (spec: domicilio)');
            $table->decimal('total', 12, 2)->comment('Total a cobrar: subtotal + domicilio (spec: total)');
            $table->string('status')->default('pending')->comment('Evolución del pedido: pending, paid, shipped... (spec: estado)');
            // Extra útil: método de pago elegido (el estado del dinero vive en payment_status
            // y en la tabla order_payments).
            $table->string('payment_method')->nullable()->comment('Método elegido: efectivo, transferencia, etc.');
            $table->string('payment_status')->default('pending')->comment('Estado del dinero: pending, paid... (spec: estado_pago)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
