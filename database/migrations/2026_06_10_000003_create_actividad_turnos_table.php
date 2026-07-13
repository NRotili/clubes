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
        Schema::create('actividad_turnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('estado', 20)->default('confirmado'); // pendiente, confirmado, rechazado, cancelado
            $table->decimal('monto', 10, 2)->nullable();
            $table->boolean('pagado')->default(false);
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gestionado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['actividad_id', 'fecha', 'hora_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividad_turnos');
    }
};
