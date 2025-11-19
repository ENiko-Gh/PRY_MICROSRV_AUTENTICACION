<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Añade la columna 'perfil' a la tabla 'users'.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Añade el campo 'perfil' después de 'password' con un valor por defecto
            $table->string('perfil')->default('usuario común')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la columna 'perfil' de la tabla 'users'.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('perfil');
        });
    }
};
