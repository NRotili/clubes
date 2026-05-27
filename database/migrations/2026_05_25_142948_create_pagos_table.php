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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $table->foreignId('cuota_mensual_id')->nullable()->constrained('cuotas_mensuales')->nullOnDelete();
            $table->date('fecha');
            $table->string('metodo_pago', 30);     // efectivo, transferencia, tarjeta_debito
            $table->decimal('total', 10, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
