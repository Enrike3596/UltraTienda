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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name')->comment('Nombre del producto (spec: nombre)');
            $table->text('description')->nullable()->comment('Características del producto (spec: descripcion)');
            $table->decimal('price', 12, 2)->comment('Precio de venta (spec: precio)');
            $table->string('image')->nullable()->comment('Ruta de la imagen (spec: imagen)');
            $table->boolean('disponible')->default(true)->comment('Hay stock para vender (spec: disponible)');
            $table->boolean('is_active')->default(true)->comment('Visible en la tienda (spec: activo)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
