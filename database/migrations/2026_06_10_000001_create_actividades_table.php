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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('estado', 20)->default('activa'); // activa, inactiva
            $table->boolean('requiere_aprobacion')->default(false);
            $table->boolean('requiere_pago')->default(false);
            $table->decimal('costo', 10, 2)->nullable();
            $table->unsignedInteger('anticipacion_dias')->nullable(); // null = sin límite
            $table->unsignedInteger('max_turnos_activos')->nullable(); // por socio, null = sin límite
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
