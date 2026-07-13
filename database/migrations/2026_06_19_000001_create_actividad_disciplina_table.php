<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad_disciplina', function (Blueprint $table) {
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->primary(['actividad_id', 'disciplina_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_disciplina');
    }
};
