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
        Schema::create('disciplina_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->string('dia_semana', 20); // lunes, martes, miercoles, jueves, viernes, sabado, domingo
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplina_horarios');
    }
};
