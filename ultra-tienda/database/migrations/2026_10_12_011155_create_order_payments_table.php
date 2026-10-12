<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->comment('Valor del abono/pago');
            $table->string('payment_method')->comment('Medio usado en este pago');
            $table->string('payment_status')->default('pending')->comment('Estado de este pago');
            $table->timestamp('payment_date')->nullable()->comment('Cuándo se recibió el dinero');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
