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
        Schema::create('disciplina_socio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $table->date('fecha_inscripcion');
            $table->string('estado', 20)->default('activa'); // activa, baja
            $table->unique(['disciplina_id', 'socio_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplina_socio');
    }
};
