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
        Schema::create('disciplina_profesor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('disciplina_id');
            $table->unsignedBigInteger('profesor_id');
            $table->decimal('sueldo', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['disciplina_id', 'profesor_id']);
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->cascadeOnDelete();
            $table->foreign('profesor_id')->references('id')->on('profesores')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplina_profesor');
    }
};
