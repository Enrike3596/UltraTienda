<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla: roles — spec (dentro de usuarios): rol. Gestiona el acceso de
// administradores y, si se necesita, clientes registrados.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CAMBIO vs. versión anterior: tabla en plural "roles" (convención Laravel)
        // y nombre único para no duplicar roles.
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nombre del rol: admin, cliente, etc. (spec: rol)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
