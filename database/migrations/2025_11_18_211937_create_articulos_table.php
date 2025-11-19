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
        // Creamos la tabla 'articulos'
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            // Título del artículo (ej: string de 255 caracteres)
            $table->string('titulo');
            // Contenido del artículo (ej: texto largo)
            $table->text('contenido');

            // CLAVE: El artículo debe estar asociado a un usuario (quien lo creó).
            // Esto crea la clave foránea a la tabla 'users' y borra en cascada.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Estado para verificar si está publicado o es borrador
            $table->enum('estado', ['publicado', 'borrador'])->default('borrador');
            // Timestamps para created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
