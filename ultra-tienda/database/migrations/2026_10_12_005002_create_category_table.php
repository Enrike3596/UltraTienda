<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla: categorias — spec: id, nombre, descripcion, activa, orden.
// Permite agregar, modificar y ordenar las secciones de la tienda.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CAMBIO vs. versión anterior: tabla en plural "categories" (convención Laravel)
        // y columna "orden" como entero (antes "order" string, no servía para ordenar).
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nombre de la sección (spec: nombre)');
            $table->text('description')->nullable()->comment('Descripción de la sección (spec: descripcion)');
            $table->integer('orden')->default(0)->comment('Posición de la sección en la tienda (spec: orden)');
            $table->boolean('is_active')->default(true)->comment('Sección visible en la tienda (spec: activa)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
