<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencia_disciplina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $table->date('fecha');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['disciplina_id', 'socio_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencia_disciplina');
    }
};
