<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla de soporte: detalle del pedido (líneas de cada compra).
// No está en la spec, pero es necesaria: un pedido tiene N productos.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            // CAMBIO vs. versión anterior: FK con borrado en cascada (antes columnas sueltas).
            $table->foreignId('order_id')->constrained('orders')
                ->cascadeOnUpdate()->cascadeOnDelete();
            // RESTRICT: no se puede borrar un producto que ya fue vendido.
            $table->foreignId('product_id')->constrained('products')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('quantity')->comment('Unidades compradas de este producto');
            // Precio unitario congelado al momento de la compra (aunque luego cambie en products).
            $table->decimal('price', 12, 2)->comment('Precio unitario al vender (foto del precio)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
