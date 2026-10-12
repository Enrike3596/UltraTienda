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
            $table->foreignId('user_id')->nullable()->constrained('users')
                ->cascadeOnUpdate()->nullOnDelete();
            $table->string('customer_name')->comment('Nombre del cliente (spec: cliente)');
            $table->string('customer_email')->nullable()->comment('Correo para notificaciones del pedido');
            $table->string('customer_phone')->comment('Teléfono de contacto/entrega (spec: teléfono)');
            $table->string('customer_address')->comment('Dirección de entrega (spec: dirección)');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Suma de los productos (spec: subtotal)');
            $table->decimal('shipping_cost', 12, 2)->default(0)->comment('Valor del domicilio / envío (spec: domicilio)');
            $table->decimal('total', 12, 2)->comment('Total a cobrar: subtotal + domicilio (spec: total)');
            $table->string('status')->default('pending')->comment('Evolución del pedido: pending, paid, shipped... (spec: estado)');
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
