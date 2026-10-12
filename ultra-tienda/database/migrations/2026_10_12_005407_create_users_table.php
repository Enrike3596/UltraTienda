<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla: usuarios — spec: id, nombre, correo, password, rol.
// (nombre/correo/password ya existen en la migración base de Laravel;
// aquí solo se agrega el rol con su FK).
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                // CAMBIO vs. versión anterior: FK a roles con SET NULL (antes columna
                // suelta). Si se elimina un rol, el usuario queda sin rol en vez de romperse.
                $table->foreignId('role_id')->nullable()->constrained('roles')
                    ->cascadeOnUpdate()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
        });
    }
};
